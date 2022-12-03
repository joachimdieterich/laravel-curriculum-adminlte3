<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class VariantDefinition extends Model
{
    protected $guarded = [];

    public function path()
    {
        return route('variantDefinitions.show', $this->id);
    }

    public function variants()
    {
        return $this->hasMany('App\Variant');
    }

}

