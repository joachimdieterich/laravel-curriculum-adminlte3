<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Medium extends Model
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
        return "/media/{$this->id}";
    }

    public function absolutePath()
    {
        if ($this->mime_type !== 'url') {
            return Storage::disk('local')->path(ltrim($this->path.$this->medium_name, '/'));
        } else {
            return $this->path;
        }
    }

    public function relativePath()
    {
        return $this->path.$this->medium_name;
    }

    public function license()
    {
        return $this->hasOne('App\License', 'id', 'license_id');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\MediumSubscription');
    }

    public function subscribe($model, $sharing_level_id = 1, $visibility = true)
    {
        $subscribe = new MediumSubscription([
            'medium_id' => $this->id,
            'subscribable_type' => get_class($model),
            'subscribable_id' => $model->id,
            'sharing_level_id' => $sharing_level_id,
            'visibility' => $visibility,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();
    }

    public function getByFilemanagerPath($path)
    {
        return $this->where('path', str_replace(config('lfm.url_prefix', 'laravel-filemanager').'/', '', dirname($path)).'/')
                    ->where('medium_name', basename($path))
                    ->get()
                    ->first();
    }

    /**
     * @param  type  $eventPath
     * @param  type  $cutBasename if true basename is cut off
     * @param  type  $basePath
     * @return type
     */
    public function convertFilemanagerEventPathToMediumPath($eventPath, $cutBasename = true, $basePath = 'app')
    {
        $filePath = str_replace(public_path(), '', $eventPath);
        if ($cutBasename) {
            return str_replace(basename($filePath), '', str_replace(storage_path()."/{$basePath}", '', $eventPath));
        } else {
            return str_replace(storage_path()."/{$basePath}", '', $eventPath);
        }
    }
}
