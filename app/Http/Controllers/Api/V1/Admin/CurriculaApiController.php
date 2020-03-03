<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Curriculum;
use App\ObjectiveType;

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
    
    public function getSingleMetadataset(Curriculum $curriculum)
    {
        return $this->generateMetadataset($curriculum);

    }
    
    public function getAllMetadatasets()
    {
        $curricula = Curriculum::all();
        
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
        $prefix .= str_pad("001", 2, '0', STR_PAD_LEFT);
        
        // curriculum 
        $metadata[] = [
            'id'        => $prefix."000000000000", 
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
                    'title' => str_replace(array("\n", "\r", "\t"), ' ', strip_tags(ObjectiveType::where('id', $terminalObjective->objective_type_id)->get()->first()->title)),
                    'parent_id' => $prefix
                        ."000000000000"
                ];
                $ter = 1; //set/reset terminal_id
            } 
            // terminal objective
            $metadata[] = [
                'id' => $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    ."000000", 
                'title' => str_replace(array("\n", "\r", "\t"), ' ', strip_tags($terminalObjective->title)),
                'parent_id' => $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    ."000000000",
            ];
            
            // enabling objective
            $ena = 1;
            foreach ($terminalObjective->enablingObjectives()->get() as $enablingObjective)
            {
                $metadata[] = [
                'id' => $prefix
                    .str_pad($ter_type, 3, '0', STR_PAD_LEFT)
                    .str_pad($ter, 3, '0', STR_PAD_LEFT)
                    .str_pad($ena, 3, '0', STR_PAD_LEFT)
                    ."000", 
                'title' => str_replace(array("\n", "\r", "\t"), ' ', (strip_tags($enablingObjective->title))),
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

   
}
