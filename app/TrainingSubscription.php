<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class TrainingSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'training_id',
        'subscribable_type',
        'subscribable_id',
        'order_id',
        'editable',
        'owner_id'
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

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function training()
    {
        return $this->belongsTo('App\Training', 'training_id', 'id');
    }
}
