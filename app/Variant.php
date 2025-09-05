<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class Variant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'description' => CleanHtml::class,
    ];

    public function definition()
    {
        return $this->hasOne('App\VariantDefinition', 'id', 'variant_definition_id');
    }
}
