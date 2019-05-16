<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    public function license()
    {
        return $this->hasOne('App\License', 'id', 'license_id');
    }
}
