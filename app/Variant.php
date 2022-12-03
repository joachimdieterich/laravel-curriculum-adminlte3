<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Variant extends Model
{
    protected $guarded = [];

    public function definition()
    {
        return $this->hasOne('App\VariantDefinition', 'id', 'variant_definition_id');
    }

}


