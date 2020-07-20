<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Curriculum;
use App\ObjectiveType;
use Illuminate\Http\Request;
use App\Config;

class CurriculaApiController extends Controller
{
    public function index()
    {
        $curricula = Curriculum::all();

        return $curricula;
    }


    public function show(Curriculum $curriculum)
    {

        return $curriculum;
        
    }
    
    public function getSingleMetadataset(Curriculum $curriculum, Request $request)
    {
        $metadata_password = Config::where([
                ['key', '=',  'metadata_password']
            ])->get()->first()->value;
        if ($metadata_password != $request->query('password'))
        {
            return 'forbidden';
        }
        
        return $this->generateMetadataset($curriculum);
    }
    
    public function getAllMetadatasets(Request $request)
    {
        $metadata_password = Config::where([
                ['key', '=',  'metadata_password']
            ])->get()->first()->value;
        if ($metadata_password != $request->query('password'))
        {
            return 'forbidden';
        }
        
        $curricula = Curriculum::where('type_id', 1)->get(); //only export global curricula, todo: pass type_id as param
        
        $metadata = array(); //{'id', 'title'}
        
        foreach ($curricula as $curriculum)
        {
            $metadata[] = $this->generateMetadataset($curriculum);
        }
        return $metadata;

    }
    
    
    private function generateMetadataset($curriculum) {    
        $metadata = array(); //{'id', 'title'}
        
        // generate organizational part of identifier
        $prefix  = str_pad($curriculum->country()->get()->first()->id, 3, '0', STR_PAD_LEFT);
        $prefix .= str_pad($curriculum->state()->get()->first()->id, 2, '0', STR_PAD_LEFT);
        $prefix .= str_pad($curriculum->organizationType()->get()->first()->external_id, 2, '0', STR_PAD_LEFT);
        $prefix .= str_pad($curriculum->subject()->get()->first()->external_id, 5, '0', STR_PAD_LEFT); //includes subject type!
        $prefix .= str_pad($curriculum->grade()->get()->first()->external_begin, 2, '0', STR_PAD_LEFT);
        $prefix .= str_pad($curriculum->grade()->get()->first()->external_end, 2, '0', STR_PAD_LEFT);
        $prefix .= str_pad("001", 2, '0', STR_PAD_LEFT); //version todo: dynamic
        
        // curriculum 
        $curriculum->ui = $prefix."000000000000";
        $curriculum->save();                         //persist unique curriulum identifier
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
                        ."000000", 
                    'title' => $this->format_data(ObjectiveType::where('id', $terminalObjective->objective_type_id)->get()->first()->title),
                    'parent_id' => $prefix
                        ."000000000000"
                ];
                $ter = 1; //set/reset terminal_id
            } 
            // terminal objective
            $terminalObjective->ui = $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    ."000000";
            $terminalObjective->save();                        //persist unique terminalobjective identifier
            $metadata[] = [
                'id' => $terminalObjective->ui, 
                'title' => $this->format_data($terminalObjective->title),
                'parent_id' => $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    ."000000000",
            ];
            
            // enabling objective
            $ena = 1;
            foreach ($terminalObjective->enablingObjectives()->get() as $enablingObjective)
            {
                $enablingObjective->ui = $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    .str_pad($ena, 3, '0', STR_PAD_LEFT)
                    ."000";
                $enablingObjective->save();                        //persist unique enablingobjective identifier
                $metadata[] = [
                'id' => $enablingObjective->ui, 
                'title' => $this->format_data($enablingObjective->title),
                'parent_id' => $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    ."000000", 
            ];
                $ena++;
            }
            
            $ter++;
        }
        
        return $metadata;
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
