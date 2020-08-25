<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Curriculum;
use App\ObjectiveType;
use App\Metadataset;


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
        Metadataset::create(['version' => $this->argument('version'), 
                             'metadataset' => json_encode($metadata)]);
      
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
        
        $prefix = $this->generateUniqueCurriculumIdentifier($curriculum, $version, $curriculum_iterator);
        $metadata[] = [
            'id'        => $curriculum->ui, 
            'title'     => $curriculum->title, 
            'parent_id' => null
        ];
        
        
        // foreach terminal_type
        $ter_type = 0;
        $ter_type_previous_iteration = 0;
        
        foreach ($curriculum->terminalObjectives()->get() as $terminalObjective)
        {
            // terminal objective type
            if ($ter_type_previous_iteration != $terminalObjective->objective_type_id)
            {
                $ter = 0;
                $ter_type_previous_iteration = $terminalObjective->objective_type_id;
                $ter_type++;

                $metadata[] = [
                    'id' => $prefix
                        .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                        .str_pad($ter, 3, '0', STR_PAD_LEFT)
                        ."000", 
                    'title' => $this->format_data(ObjectiveType::where('id', $terminalObjective->objective_type_id)->get()->first()->title),
                    'parent_id' => $prefix
                        ."000000000"
                ];
                $ter = 1; //set/reset terminal_id
            } 
            // terminal objective
            $terminalObjective->ui = $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    ."000";
            $terminalObjective->save();                        //persist unique terminalobjective identifier
            $metadata[] = [
                'id' => $terminalObjective->ui, 
                'title' => $this->format_data($terminalObjective->title),
                'parent_id' => $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    ."000000",
            ];
            
            // enabling objective
            $ena = 1;
            foreach ($terminalObjective->enablingObjectives()->get() as $enablingObjective)
            {
                $enablingObjective->ui = $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    .str_pad($ena, 3, '0', STR_PAD_LEFT);
                $enablingObjective->save();                        //persist unique enablingobjective identifier
                $metadata[] = [
                'id' => $enablingObjective->ui, 
                'title' => $this->format_data($enablingObjective->title),
                'parent_id' => $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    ."000", 
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
    
    private function format_data($input)
    {
        $entry_limiter = 150;   //todo: should be set dynamic
        
        $input = preg_replace('/<br>/', ' ', $input);
        $input = strip_tags($input);
        $input = strlen($input) > $entry_limiter ? substr($input,0,$entry_limiter)."..." : $input;  // limit text   
                                                   // replace multiple spaces, tabs, or linebrakes with one single space
        return mb_ereg_replace('\s+', ' ', mb_convert_encoding($input, 'UTF-8', 'UTF-8'));
    }
    
    
}