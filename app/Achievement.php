<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Achievement extends Model
{
    protected $guarded = [''];
    
    protected $cast = ['status' => 'string']; //important to get id as unique string
    //public $incrementing = false;
//    protected function setKeysForSaveQuery(Builder $query)
//    {
//        $query
//            ->where('referenceable_type', '=', $this->referenceable_type)
//            ->where('referenceable_id', '=', $this->referenceable_id);
//        return $query;
//    }
    
}
