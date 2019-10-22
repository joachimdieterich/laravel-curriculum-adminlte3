<?php

namespace App;

use App\MediumSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Medium extends Model
{
    
    protected $guarded = [];
    
    public function absolutePath(){
        if ($this->mime_type !== 'url')
        {
            return Storage::disk('local')->path(ltrim($this->path . $this->medium_name, '/'));
        } 
        else 
        {
            return $this->path;
        }
    }
    
    public function license()
    {
        return $this->hasOne('App\License', 'id', 'license_id');
    }
    
    public function subscribe($model, $sharing_level_id = 1, $visibility = true)
    {
        $subscribe = new MediumSubscription([
			"medium_id" =>  $this->id,
			"subscribable_type"=> get_class($model),
			"subscribable_id"=> $model->id,
			"sharing_level_id"=> $sharing_level_id,
			"visibility"=> $visibility,
			"owner_id"=> auth()->user()->id,	
	]);
        $subscribe->save();
    }
}
