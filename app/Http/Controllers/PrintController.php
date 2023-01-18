<?php

namespace App\Http\Controllers;

use App\Content;
use App\Curriculum;
use App\Glossar;
use App\Medium;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PrintController extends Controller
{
    public function content(Content $content)
    {
        $content->isAccessible();

        $html = view('print.content')
            ->with(compact('content'))
            ->render();

        return $this->print($html, $content->title.'.pdf');
    }

    /*public function curriculum(Curriculum $curriculum)
    {
        $html = view('print.curriculum')
                ->with(compact('curriculum'))
                ->render();
        return $this->print($html, $curriculum->title.'.pdf');
    }*/

    public function glossar(Glossar $glossar)
    {
        $glossar->isAccessible();
        $entries = $glossar->contents;
        $html = view('print.glossar', compact('entries'))->render();

        return $this->print($html, 'glossar.pdf', 'save');
    }

    public function model($model, $id)
    {
        //todo: check if user has permission to see this model
        //$model->isAccessible(); //should do the trick
        //delegate to model class?
        abort(403);
        /*
        $view = class_basename($model);
        $model= str_starts_with($model, 'App\\') ? $model : "App\\".$model;
        $model =  app()->make($model)::find($id);

        $html = view('print.'. strtolower($view))
                ->with(compact('model'))
                ->render();

        return $this->print($html, $model->title.'.pdf', 'download' );*/
    }

    public function references(Curriculum $curriculum)
    {
        $curriculum->isAccessible();

        $html = view('print.references')
            ->with(compact('curriculum'))
            ->render();

        return $this->print($html, $curriculum->title.'_references.pdf', 'download', 'landscape');
    }

    /**
     * @param  type  $html
     * @param  type  $path
     * @param  string  $target 'download', 'save', 'inline'
     * @return type
     */
    public static function print($html, $path, $target = 'download', $orientation = 'portrait')
    {
        $meta = '<meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />';

        /* replace relative media links with absolute paths to get snappy working */

        $html = relativeToAbsoutePaths($html);

        $pdf = SnappyPdf::loadHTML($meta.$html)
                   ->setPaper('a4')
                   ->setOrientation($orientation)
                   ->setOption('margin-top', 20)
                   ->setOption('margin-left', 20)
                   ->setOption('margin-right', 20)
                   ->setOption('margin-bottom', 20);

        switch ($target) {
            case 'inline': //open in same window
                return $pdf->inline($path);
            break;

            case 'save':  //save on path
                //if file exists return file,
                 if (file_exists(storage_path('app/users/'.auth()->user()->id.'/'.$path))) {
                     //dump(storage_path("app/users/".auth()->user()->id."/".$path));
                     unlink(storage_path('app/users/'.auth()->user()->id.'/'.$path));
                 }
                //else generate file
                $pdf->save(storage_path('app/users/'.auth()->user()->id.'/'.$path));

                $basename = basename($path);
                $media = new Medium([
                    'path' => dirname('/users/'.auth()->user()->id.'/'.$path).'/',
                    'title' => $basename,
                    'medium_name' => $basename,
                    'description' => 'printed',
                    'author' => auth()->user()->fullName(),
                    'publisher' => '',
                    'city' => '',
                    'date' => date('Y-m-d_H-i-s'),
                    'size' => File::size(Storage::disk('local')->path('users/'.auth()->user()->id.'/'.$path)),
                    'mime_type' => File::mimeType(Storage::disk('local')->path('users/'.auth()->user()->id.'/'.$path)),
                    'license_id' => 2, //$media_node->getAttribute('license'), //hack fix false entries in import files

                    'owner_id' => auth()->user()->id,
                ]);
                $media->save();

                return $media->path();
            break;

            case 'download':
            default:
                return $pdf->download($path);
                break;
        }
    }
}
