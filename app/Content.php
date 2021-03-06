<?php

namespace App;

use App\ContentSubscription;
use App\Quote;
use App\Categorie;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $guarded = [];

    public function path()
    {
        return "/contents/{$this->id}";
    }

    public function owner(){
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(ContentSubscription::class);
    }

    public function subscribe($model, $sharing_level_id = 1, $visibility = true)
    {
        $subscribe = new ContentSubscription([
			"content_id" =>  $this->id,
			"subscribable_type"=> get_class($model),
			"subscribable_id"=> $model->id,
			"sharing_level_id"=> $sharing_level_id,
			"visibility"=> $visibility,
			"owner_id"=> auth()->user()->id,
            "order_id"=> ContentSubscription::where([
                                "subscribable_type"=> get_class($model),
                                "subscribable_id"=> $model->id])->max('order_id')+1
	    ]);
        $subscribe->save();
    }

    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function media()
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on curriculum table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function quotes()
    {
        return $this->hasMany('App\Quote', 'referenceable_id', 'id');
    }

    public function navigator_item()
    {
        return $this->morphOne('App\NavigatorItem', 'referenceable');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Categorie'); //should be without timestamp to get sync working
    }

}
