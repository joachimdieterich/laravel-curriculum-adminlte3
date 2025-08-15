<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'curriculum_id',
        'editable',
        'due_date',
        'title',
        'sharing_token',
        'owner_id'
    ];


    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function curriculum()
    {
        return $this->belongsTo('App\Curriculum', 'curriculum_id', 'id');
    }

    public function isAccessible()
    {
        return $this->curriculum->isAccessible();
    }
}
