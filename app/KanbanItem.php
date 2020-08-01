<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KanbanItem extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return route('kanban.show', $this->id);
    }
     
    public function kanban()
    {
        return $this->belongsTo('App\Kanban', 'id', 'kanban_id');   
    
    }
    
    public function subscribable()
    {
        return $this->morphTo();
    }
    
    public function status()
    {
        return $this->hasOne('App\KanbanStatus', 'id', 'kanban_status_id');   
    
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
    }
    
    
}
