<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationRoleUser extends Model
{
    
    protected $guarded = [];
    
    
    public function organization()
    {
      return $this->belongsTo('Organization');
    }
    public function user()
    {
      return $this->belongsTo('User');
    }
    public function role()
    {
      return $this->belongsTo('Role');
    }
   
}
