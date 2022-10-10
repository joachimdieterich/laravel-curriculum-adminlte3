<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $fillable = ['title', 'created_at', 'updated_at'];

    public function path()
    {
        return "/categories/{$this->id}";
    }

    public function contents()
    {
        return $this->belongsToMany('App\Content')->withTimestamps();
    }
}
