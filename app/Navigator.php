<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navigator extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'updated_at',
        'created_at',
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
        return "/navigators/{$this->id}";
    }

    public function views()
    {
        return $this->hasMany('App\NavigatorView', 'navigator_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }
}
