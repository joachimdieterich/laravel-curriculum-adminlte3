<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use UniSharp\Laravelfilemanager\Events\ImageIsUploading;
use UniSharp\Laravelfilemanager\Events\ImageWasUploaded;
use UniSharp\LaravelFilemanager\Events\ImageIsRenaming;
use UniSharp\LaravelFilemanager\Events\ImageWasRenamed;
use UniSharp\LaravelFilemanager\Events\ImageIsDeleting;
use UniSharp\LaravelFilemanager\Events\ImageWasDeleted;
use UniSharp\LaravelFilemanager\Events\FolderIsRenaming;
use UniSharp\LaravelFilemanager\Events\FolderWasRenamed;
use UniSharp\LaravelFilemanager\Events\ImageIsResizing;
use UniSharp\LaravelFilemanager\Events\ImageWasResized;
use UniSharp\LaravelFilemanager\Events\ImageIsCropping;
use UniSharp\LaravelFilemanager\Events\ImageWasCropped;
use App\Medium;
use App\Http\Controllers\MediumController;
use Illuminate\Support\Facades\File;
use App\Listeners\UploadListener;


class UploadListener
{
    public function subscribe($events) 
    {
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageIsUploading',
            'App\Listeners\UploadListener@onImageIsUploading',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageWasUploaded',
            'App\Listeners\UploadListener@onImageWasUploaded',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageIsRenaming',
            'App\Listeners\UploadListener@onImageIsRenaming',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageWasRenamed',
            'App\Listeners\UploadListener@onImageWasRenamed',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageIsDeleting',
            'App\Listeners\UploadListener@onImageIsDeleting',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageWasDeleted',
            'App\Listeners\UploadListener@onImageWasDeleted',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\FolderIsRenaming',
            'App\Listeners\UploadListener@onFolderIsRenaming',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\FolderWasRenamed',
            'App\Listeners\UploadListener@onFolderWasRenamed',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageIsResizing',
            'App\Listeners\UploadListener@onImageIsResizing',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageWasResized',
            'App\Listeners\UploadListener@onImageWasResized',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageIsCropping',
            'App\Listeners\UploadListener@onImageIsCropping',
        );
        $events->listen('UniSharp\LaravelFilemanager\Events\ImageWasCropped',
            'App\Listeners\UploadListener@onImageWasCropped',
        );
    }
 
    public function handle($event)
    {
       $method = 'on'.class_basename($event);
       if (method_exists($this, $method)) {
           call_user_func([$this, $method], $event);
       }
    }
    
    public function onImageIsUploading(ImageIsUploading $event)
    {
        // ImageIsUploading
    }
    
    
    public function onImageWasUploaded(ImageWasUploaded $event)
    { 
        // Get the public path to the file and save it to the database
        $medium = new Medium();
        $filePath = str_replace(public_path(), "", $event->path());    
        Medium::create([
            'path'          => $medium->convertFilemanagerEventPathToMediumPath($event->path()),
            'medium_name'   => basename($filePath),
            'title'         => basename($filePath),
            'description'   => '',
            'author'        => auth()->user()->username,
            'publisher'     => '',
            'city'          => '',
            'date'          => date("Y-m-d_H-i-s"),
            'size'          => File::size($filePath),
            'mime_type'     => File::mimeType($filePath),
            'license_id'    => 2,//$media_node->getAttribute('license'), //hack fix false entries in import files
           
            'owner_id'      => auth()->user()->id,
        ]);
    }

    public function onImageIsRenaming(ImageIsRenaming $event)
    {
        // ImageIsRenaming
    }
    
    public function onImageWasRenamed(ImageWasRenamed $event)
    {
        $controller = new MediumController();
        $medium     = $controller->getMediumByEventPath($event->oldPath());
        $medium->medium_name = basename($event->newPath());
        $medium->update();
    }
    
    /**
     * Check if medium is subscribed:
     * - if not, delete medium
     * - if yes, display information of subscribing models and abort deletion
     * @param ImageIsDeleting $event
     */
    public function onImageIsDeleting(ImageIsDeleting $event)
    { 
        //check if folder is given
        if (is_dir($event->path()))
        {
            if (count(find_all_files($event->path())) > 0)
            {
                die('<p>The folder is not empty</p>');
            } 
            return ;
        }
        
        $m      = new Medium();
        $medium = Medium::where('path', $m->convertFilemanagerEventPathToMediumPath($event->path()))
                        ->where('medium_name', basename($event->path()))
                        ->get()->first();
       
        if ($medium->subscriptions->count() > 0) {
            // The image is in use, create a response message
            $message  = "<p>The file you are trying to delete is in use in the file_paths table with the following id's:</p>";
            $message .= "<ul>";
            foreach ($medium->subscriptions as $medium_subscription) {
                $message .= "<li>{$medium_subscription->medium_id}: subscribed by $medium_subscription->subscribable_type({$medium_subscription->subscribable_id})</li>";
            }
            $message .= "</ul>";
            $message .= "<p>Please remove those subscriptions before you can delete the file.</p>";
            
            die($message);  // Die with response message
        } 
    }
    /**
     * Removes entry on media table if medium was successful deleted
     * @param ImageWasDeleted $event
     */
    public function onImageWasDeleted(ImageWasDeleted $event)
    {
        $m      = new Medium();
        $medium = Medium::where('path', $m->convertFilemanagerEventPathToMediumPath($event->path()))
                        ->where('medium_name', basename($event->path()))
                        ->get()->first();
        $medium->delete();
    }

    public function onFolderIsRenaming(FolderIsRenaming $event)
    {
        // onFolderIsRenaming
    }

    /**
     * find all media where $event->oldPath() exists and replace those with $event->newPath()
     * @param FolderWasRenamed $event
     */
    public function onFolderWasRenamed(FolderWasRenamed $event)
    {
        
        $m = new Medium();
        $medium_path = $m->convertFilemanagerEventPathToMediumPath($event->oldPath(), false);
        $media = Medium::where('path', 'LIKE', '%'.$medium_path.'%')->get();
        foreach ($media as $medium) {
            $medium = Medium::find($medium->id);
            $medium->path = str_replace($medium_path, $m->convertFilemanagerEventPathToMediumPath($event->newPath(), false), $medium->path);
            $medium->update();
        }
    }
    
    public function onImageIsResizing(ImageIsResizing $event)
    {
        // onImageIsResizing
    }
    
    public function onImageWasResized(ImageWasResized $event)
    {
        // ImageWasResized
    }
    
    public function onImageIsCropping(ImageIsCropping $event)
    {
        // ImageIsCropping
    }
    
    public function onImageWasCropped(ImageWasCropped $event)
    {
        // ImageWasCropped
    }
  
}
