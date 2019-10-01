<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NavigatorItem extends Model
{
    public function navigator()
    {
        return $this->belongsTo('App\NavigatorView', 'id', 'navigator_view_id');
    }
}
