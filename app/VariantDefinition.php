<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class VariantDefinition extends Model
{
    protected $guarded = [];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function path()
    {
        return route('variantDefinitions.show', $this->id);
    }

    public function variants()
    {
        return $this->hasMany('App\Variant');
    }
}
