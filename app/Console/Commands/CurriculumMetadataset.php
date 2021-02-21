<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Curriculum;
use App\ObjectiveType;
use App\Metadataset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CurriculumMetadataset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curriculum:metadataset
                            {version : Version of metadataset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make metadataset of all curricula';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Generate metadataset version '.$this->argument('version'));

        $curricula = Curriculum::where('type_id', 1)->get();
        $this->generate($curricula, $this->argument('version'));

        $this->info(' ...done');
    }

    protected function generate($curricula, $version)
    {
        $bar = $this->output->createProgressBar(count($curricula));

        $bar->start();
        $curriculum_iterator = 0;
        $metadata = array(); //{'id', 'title'}

        foreach($curricula as $curriculum)
        {
            $metadata[] = $this->processCurriculum($curriculum, $version, $curriculum_iterator);
            $curriculum_iterator++;
            $bar->advance();
        }

        if ($this->validateOrRollback($metadata))
        {
            Metadataset::create(['version' => $this->argument('version'),
                'metadataset' => json_encode($metadata)]);
        }

        $bar->finish();
    }

    /*
     * Identifier
     *
     * Country State OrgType Subject AgeBegin AgeEnd  Version TerminalType TerminalObjective EnablingObjective NotUsedYet
     * 276     11    00      00074   01       01      001     000           000              000               000
     * changed -> 25.08.2020
     * Country State OrgType Subject AgeBegin AgeEnd  Version CurriculumID  TerminalType TerminalObjective EnablingObjective
     * 276     11    00      00074   01       01      001     000           000          000               000
     */
    private function processCurriculum($curriculum, $version, $curriculum_iterator)
    {
        $metadata = array(); //{'id', 'title'}

        // generate curriculum part of identifier
        $prefix = $this->generateUniqueCurriculumIdentifier($curriculum, $version, $curriculum_iterator);
        $metadata[] = [
            'id'        => $curriculum->ui,
            'title'     => $curriculum->title,
            'parent_id' => null
        ];


        $ter_type = 0;
        $ter_type_previous_iteration = 0;
        $ter = 0; // initial value
        foreach ($curriculum->terminalObjectives()->get() as $terminalObjective)            // foreach terminal_type
        {

            // terminal objective type
            if ($ter_type_previous_iteration != $terminalObjective->objective_type_id)
            {
                $ter = 0; // reset on new objective type
                $ter_type_previous_iteration = $terminalObjective->objective_type_id;
                $ter_type++;

                $ter_type_ui = $this->generateTerminalObjectiveUi($prefix, $ter_type, $ter); // generates terminal_objective_type ID, ter = 0

                $metadata[] = [
                    'id' => $ter_type_ui, //this ID cannot be persisted in the DB -> todo: check if this could be a problem.
                    'title' => $this->format_data(ObjectiveType::where('id', $terminalObjective->objective_type_id)->get()->first()->title),
                    'parent_id' => $prefix . "000000000"
                ];
                $ter = 1; //set/reset terminal_id
            }

            // terminal objective
            $this->setTerminalObjectiveUi($terminalObjective, $prefix, $ter_type, $ter);

            $metadata[] = [
                'id' => $terminalObjective->ui,
                'title' => $this->format_data($terminalObjective->title),
                'parent_id' => $ter_type_ui, //-> do not use Str::substr($terminalObjective->ui, 0,24) ."000000", this could produce duplicates on following versions
            ];

            // enabling objective
            $ena = 1;
            foreach ($terminalObjective->enablingObjectives()->get() as $enablingObjective)
            {
                $this->setEnablingObjectiveUi($enablingObjective, $prefix, $ter_type, $ter, $ena);
                $metadata[] = [
                    'id' => $enablingObjective->ui,
                    'title' => $this->format_data($enablingObjective->title),
                    'parent_id' => $terminalObjective->ui, // do not use Str::substr($enablingObjective->ui, 0,27) ."000", this could produce duplicates on following versions
                ];
                $ena++;
            }

            $ter++;
        }

        return $metadata;
    }


    private function generateUniqueCurriculumIdentifier($curriculum, $version, $curriculum_iterator)
    {

        // generate curriculum part of identifier
        if ($curriculum->ui != null)
        {
            return Str::substr($curriculum->ui, 0,16) . str_pad($version, 3, '0', STR_PAD_LEFT) . Str::substr($curriculum->ui, 19,3); // replace version in string
        }
        else
        {
            $prefix  = str_pad($curriculum->country()->get()->first()->id, 3, '0', STR_PAD_LEFT);
            $prefix .= str_pad($curriculum->state()->get()->first()->id, 2, '0', STR_PAD_LEFT);
            $prefix .= str_pad($curriculum->organizationType()->get()->first()->external_id, 2, '0', STR_PAD_LEFT);
            $prefix .= str_pad($curriculum->subject()->get()->first()->external_id, 5, '0', STR_PAD_LEFT); //includes subject type!
            $prefix .= str_pad($curriculum->grade()->get()->first()->external_begin, 2, '0', STR_PAD_LEFT);
            $prefix .= str_pad($curriculum->grade()->get()->first()->external_end, 2, '0', STR_PAD_LEFT);
            $prefix .= str_pad($version, 3, '0', STR_PAD_LEFT);
            $prefix .= str_pad($curriculum_iterator, 3, '0', STR_PAD_LEFT); //add iterator to prevent duplicats

            // curriculum
            $curriculum->ui = $prefix."000000000";
            $curriculum->save();                         //persist unique curriulum identifier

            return $prefix;
        }
    }

    /**
     * @param $enablingObjective
     * @param string $prefix
     * @param int $ter_type
     * @param int $ter
     * @param int $ena
     */
    private function setEnablingObjectiveUi($enablingObjective, string $prefix, int $ter_type, int $ter, int $ena): void
    {
        if ($enablingObjective->ui == null)
        {
            $enablingObjective->ui = $this->generateEnablingObjectiveUi($prefix, $ter_type, $ter, $ena);
            $enablingObjective->save();
        }
    }

    /**
     * @param string $prefix
     * @param int $ter_type
     * @param int $ter
     * @param int $ena
     * @return string
     */
    private function generateEnablingObjectiveUi(string $prefix, int $ter_type, int $ter, int $ena): string
    {
        return $prefix
            . str_pad($ter_type, 3, '0', STR_PAD_LEFT)
            . str_pad($ter, 3, '0', STR_PAD_LEFT)
            . str_pad($ena, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @param $terminalObjective
     * @param string $prefix
     * @param int $ter_type
     * @param int $ter
     */
    private function setTerminalObjectiveUi($terminalObjective, string $prefix, int $ter_type, int $ter): void
    {
        if ($terminalObjective->ui == null)
        {
            $terminalObjective->ui = $this->generateTerminalObjectiveUi($prefix, $ter_type, $ter);
            $terminalObjective->save();
        }
    }

    /**
     * @param string $prefix
     * @param int $ter_type
     * @param int $ter
     * @return string
     */
    private function  generateTerminalObjectiveUi(string $prefix, int $ter_type, int $ter): string
    {
        return $prefix
            . str_pad($ter_type, 3, '0', STR_PAD_LEFT)
            . str_pad($ter, 3, '0', STR_PAD_LEFT)
            . "000";
    }

    private function format_data($input)
    {
        $entry_limiter = 150;   //todo: should be set dynamic

        $input = preg_replace('/<br>/', ' ', $input);
        $input = strip_tags($input);
        $input = strlen($input) > $entry_limiter ? substr($input,0,$entry_limiter)."..." : $input;  // limit text

        return mb_ereg_replace('\s+', ' ', mb_convert_encoding($input, 'UTF-8', 'UTF-8')); // replace multiple spaces, tabs, or linebrakes with one single space
    }

    /**
     * Validate metadataset, check for duplicates
     * @param $metadataset
     * @return bool
     */
    private function validateOrRollback($metadataset)
    {

        $this->info(''); //todo: when updated to Version 8 use $this->newLine();
        $this->info('Starting validation...');
        $ui_array = array();
        $duplicates = array();
        foreach ($metadataset as $curriculum)
        {
            foreach($curriculum as $entry)

                if(!isset($ui_array[$entry['id']]))
                {
                    $ui_array[$entry['id']]=0;
                }
                else
                {
                    $duplicates[] = $entry['id'];
                }
        }

        if (count($duplicates) > 0)
        {
            $this->info('Validation failed. ' .count($duplicates) .' duplicates found.');
            $this->info('Use a new Version "php artisan curriculum:metadataset [version + 1]" to generate uis for those entries.');
            $this->info('Rollback duplicates uis in db...');
            $this->rollback($duplicates);
            return false; // a duplicate was found, end early with false
        }

        $this->info('Validation successful! Metadataset has ' . count($ui_array) . ' unique Ids');

        return true;
    }

    private function rollback($duplicates)
    {

        DB::table('curricula')->where('ui', $duplicates)->update(array('ui' => null));
        DB::table('terminal_objectives')->where('ui', $duplicates)->update(array('ui' => null));
        DB::table('enabling_objectives')->where('ui', $duplicates)->update(array('ui' => null));

    }
}
