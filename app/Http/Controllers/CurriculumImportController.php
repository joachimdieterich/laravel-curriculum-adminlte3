<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\Content;
use App\Curriculum;
use App\EnablingObjective;
use App\Glossar;
use App\Grade;
use App\Medium;
use App\MediumSubscription;
use App\OrganizationType;
use App\Quote;
use App\QuoteSubscription;
use App\Reference;
use App\Subject;
use App\TerminalObjective;
use DOMDocument;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CurriculumImportController extends Controller
{
    public function import()
    {
        return view('curricula.import');
    }

    /*
     * example http://127.0.0.1:8000/curricula/import?path=/curricula/2019-07-24_15-43-14_curriculum_nr_347.curriculum
     */
    public function store()
    {
        ini_set('max_file_uploads', 200);
        if (! request()->hasFile('imports')) {
            return redirect('/home');
        }

        foreach (request()->file('imports') as $current_file) {
            if (pathinfo($current_file->getClientOriginalName(), PATHINFO_EXTENSION) == 'curriculum') {
                $curricula_id[] = $this->importCurriculumClassicExport($current_file);
            } elseif (pathinfo($current_file->getClientOriginalName(), PATHINFO_EXTENSION) == 'cur') {
                $curricula_id[] = $this->importLaravelExport($current_file);
            }
        }

        return redirect('/curricula');
    }

    private function importCurriculumClassicExport($backup)
    {
        $folder = 'imports';

        $original_file_name = $backup->getClientOriginalName();
        $zip_path = $backup
                ->storeAs("/{$folder}", $original_file_name
                );

        $zip = new ZipArchive;
        if ($zip->open(storage_path("app/{$folder}/").$original_file_name) === true) {
            $zip->extractTo(storage_path("app/{$folder}/"));
            $zip->close();
        //ok
        } else {
            //error handlinng
        }

        $filename = pathinfo($original_file_name, PATHINFO_FILENAME);

        $xml = new DOMDocument('1.0', 'UTF-8');
        //dd(public_path("{$folder}/{$filename}.xml"));
        $xml->load(storage_path("app/{$folder}/{$filename}.xml"));
        $xml_data = $xml->getElementsByTagName('curriculum')[0];
        $old_curriculum_id = $xml_data->getAttribute('id');
        $curriculum_import_data = [
            'title' => $xml_data->getAttribute('curriculum'),
            'description' => html_entity_decode($xml_data->getAttribute('description')),
            'grade_id' => optional(Grade::where('title', $xml_data->getAttribute('grade'))->first())->id ?: 1,
            'subject_id' => optional(Subject::where('title', $xml_data->getAttribute('subject'))->first())->id ?: 1,
            'organization_type_id' => optional(OrganizationType::where('id', $xml_data->getAttribute('schooltype'))->first())->id ?: 1,
            //'organization_type_id' => optional(OrganizationType::where("external_id", $xml_data->getAttribute('schooltype'))->first())->external_id ?: 1,
            'state_id' => 'DE-RP',
            'country_id' => 'DE',
            'medium_id' => null,
            'owner_id' => auth()->user()->id,
        ];

        //persist curriculum
        $curriculum = Curriculum::create($curriculum_import_data);

        //persist terminal_objectives
        foreach ($xml->getElementsByTagName('terminal_objective') as $ter) {
            $old_ter_id = $ter->getAttribute('id');
            $terminal_import_data = [
                'curriculum_id' => $curriculum->id,
                'title' => html_entity_decode(
                        //$ter->getAttribute('terminal_objective')
                         $this->importEmbeddedFiles(
                                html_entity_decode($ter->getAttribute('terminal_objective')),
                                $ter,
                                $curriculum,
                                $folder,
                                $old_curriculum_id,
                                'embedded-file'
                            ),
                         ENT_QUOTES),
                'description' => html_entity_decode(
                        //$ter->getAttribute('description')
                        $this->importEmbeddedFiles(
                                html_entity_decode($ter->getAttribute('description')),
                                $ter,
                                $curriculum,
                                $folder,
                                $old_curriculum_id,
                                'embedded-file'
                            ),
                            ENT_QUOTES),
                'order_id' => $ter->getAttribute('order_id'),
                'color' => $ter->getAttribute('color'),
                'objective_type_id' => $ter->getAttribute('type_id'),
                //perist references, ter_files
            ];
            $terminal_objective = TerminalObjective::create($terminal_import_data);

            /* ter files */
            $terminal_media_nodes = getImmediateChildrenByTagName($ter, 'file');
            foreach ($terminal_media_nodes as $terminal_medium) {
                //$this->importMedia($terminal_medium, $terminal_objective, $folder, $old_curriculum_id.'/'.$old_ter_id.'/'); //call import function
                $media = $this->importMedia($terminal_medium, $terminal_objective, $folder, $ter->getAttribute('path').'/'); //call import function
            }

            /*ter references*/
            $this->process($ter, $terminal_objective, 'reference', 'importReference', $folder, $old_curriculum_id);
            $this->process($ter, $terminal_objective, 'quote_subscription', 'importQuoteSubscription', $folder, $old_curriculum_id);

            //persist enabing_objectives
            foreach ($ter->getElementsByTagName('enabling_objective') as $ena) {
                $old_ena_id = $ena->getAttribute('id');
                $enabling_import_data = [
                    'curriculum_id' => $curriculum->id,
                    'terminal_objective_id' => $terminal_objective->id,
                    'title' => htmlspecialchars_decode(
                            //$ena->getAttribute('enabling_objective')
                            $this->importEmbeddedFiles(
                                html_entity_decode($ena->getAttribute('enabling_objective')),
                                $ena,
                                $curriculum,
                                $folder,
                                $old_curriculum_id,
                                'embedded-file'
                            ),
                            ENT_QUOTES),
                    'description' => htmlspecialchars_decode(
                            //$ena->getAttribute('description')
                            $this->importEmbeddedFiles(
                                html_entity_decode($ena->getAttribute('description')),
                                $ena,
                                $curriculum,
                                $folder,
                                $old_curriculum_id,
                                'embedded-file'
                            ),
                             ENT_QUOTES),
                    'order_id' => $ter->getAttribute('order_id'),
                    //perist references, ena_files
                ];
                $enabling_objective = EnablingObjective::create($enabling_import_data);

                /* ena files */
                $enabling_medium_nodes = getImmediateChildrenByTagName($ena, 'file');
                foreach ($enabling_medium_nodes as $enabling_medium) {
                    $this->importMedia($enabling_medium, $enabling_objective, $folder, $ena->getAttribute('description').'/'); //call import function
                    //$this->importMedia($enabling_medium, $enabling_objective, $folder, $old_curriculum_id.'/'.$old_ter_id.'/'.$old_ena_id.'/'); //call import function
                }

                $this->process($ena, $enabling_objective, 'reference', 'importReference', $folder, $old_curriculum_id);
                $this->process($ena, $enabling_objective, 'quote_subscription', 'importQuoteSubscription', $folder, $old_curriculum_id);
            }
        }

        /* import content */
        $this->process($xml_data, $curriculum, 'content', 'importContent', $folder, $old_curriculum_id);
        /* end import content */

        /* import glossar */
        $glossar_content_nodes = getImmediateChildrenByTagName($xml_data, 'glossar');
        if (count($glossar_content_nodes) > 0) {
            $glossar = new Glossar([
                'subscribable_type' => get_class($curriculum),
                'subscribable_id' =>  $curriculum->id,
            ]);
            $glossar->save();
            $glossar->fresh();
            foreach ($glossar_content_nodes as $gl) {
                $this->importContent($gl, $glossar, $folder, $old_curriculum_id);
            }
        }
        /* end import glossar */

        /* import curriculum files */
        $media_content_nodes = getImmediateChildrenByTagName($xml_data, 'file');
        foreach ($media_content_nodes as $cur_fil) {
            $this->importMedia($cur_fil, $curriculum, $folder, $old_curriculum_id.'/');
        }
        /* end import curriculum files */

        /**
         * clean up
         */
        Storage::deleteDirectory($folder);

        return $curriculum->id;
    }

    private function importMedia($media_node, $model, $folder, $path, $tag = 'file')
    {
        $base_path = 'users/'.auth()->user()->id; //define path to current users folder

        if ($media_node->getAttribute('full_path') == '') {
            return;
        }
        $new_folder = class_basename($model);
        $temp_filepath = storage_path("app/{$folder}/{$path}").$media_node->getAttribute('filename');
        if (! file_exists($temp_filepath) and ($media_node->getAttribute('type') !== '.url')) {
            $path = $media_node->getAttribute('path'); //fallback if files are in folder 102 (old admin folder)
            $temp_filepath = storage_path("app/{$folder}/{$path}").$media_node->getAttribute('filename');
            if (! file_exists($temp_filepath) and ($media_node->getAttribute('type') !== '.url')) {
                dump('missing'.$temp_filepath);

                return;
            }
        }
        $media = new Medium([
            'path'          => ($media_node->getAttribute('type') == '.url') ? $media_node->getAttribute('path') : "/{$base_path}/{$new_folder}/{$model->id}/",
            'title'         => substr($media_node->getAttribute('title'), 0, 190),
            'medium_name'   => substr($media_node->getAttribute('filename'), 0, 190),
            'description'   => $media_node->getAttribute('description'),
            'author'        => $media_node->getAttribute('author'),
            'publisher'     => $media_node->getAttribute('publisher'),
            'city'          => $media_node->getAttribute('city'),
            'date'          => $media_node->getAttribute('date'),
            'size'          => ($media_node->getAttribute('type') == '.url') ? 0 : File::size($temp_filepath),
            'mime_type'     => ($media_node->getAttribute('type') == '.url') ? 'url' : File::mimeType($temp_filepath),
            'license_id'    => 2, //$media_node->getAttribute('license'), //hack fix false entries in import files

            'owner_id'      => auth()->user()->id,

        ]);
        $media->save();

        if (! Storage::disk('local')->exists("{$base_path}/{$new_folder}/{$model->id}/{$media_node->getAttribute('filename')}") and ($media_node->getAttribute('type') != '.url')) { //only copy if not exists
            Storage::disk('local')
                    ->move("{$folder}/{$path}".$media_node->getAttribute('filename'),
                           "{$base_path}/{$new_folder}/{$model->id}/{$media_node->getAttribute('filename')}");
        }

        $media->fresh();

        if ($tag == 'file') { //for now, only files gets subscribed, embedded files not!
            $media->subscribe($model);
        }

        return [['old_id' => $media_node->getAttribute('id'), 'new_media' => $media]];
    }

    public function process($base_node, $model, $tag, $function, $folder, $old_curriculum_id)
    {
        $nodes = getImmediateChildrenByTagName($base_node, $tag);
        foreach ($nodes as $ref) {
            $this->$function($ref, $model, $folder, $old_curriculum_id); //call import function e.g. importReference, importQuoteSubscription, importContent
        }
    }

    private function importContent($content_node, $model, $folder, $old_curriculum_id)
    {
        $content = new Content([
            'title'   =>  htmlspecialchars_decode(getImmediateChildrenByTagName($content_node, 'title')[0]->nodeValue, ENT_QUOTES),
            'content' =>  htmlspecialchars_decode(
                            $this->importEmbeddedFiles(
                                getImmediateChildrenByTagName($content_node, 'text')[0]->nodeValue,
                                $content_node,
                                $model,
                                $folder,
                                $old_curriculum_id,
                                'embedded-file'
                            ),
                            ENT_QUOTES),
            'owner_id'=> auth()->user()->id,
        ]);
        $content->save();
        $content->fresh();

        //import possible quotes
        foreach (getImmediateChildrenByTagName($content_node, 'quote') as $quote_node) {
            $this->importQuote($quote_node, $content, $folder, $old_curriculum_id);
        }

        $content->subscribe($model);
    }

    private function importEmbeddedFiles($data, $ref, $model, $folder, $old_curriculum_id, $tag = 'file')
    {

        /* import files */
        foreach ($ref->getElementsByTagName($tag) as $media_node) {
            $data = preg_replace_callback('/\<img[^\>]*src="[^>]*accessfile\.php\?id='.$media_node->getAttribute('id').'"[^\>]*>/s',
            function () use ($media_node, $model, $folder, $old_curriculum_id, $tag) {
                $media = $this->importMedia($media_node, $model, $folder, $old_curriculum_id.'/', $tag);  //import media if file is embedded

                return '<img src="/media/'.$media[0]['new_media']->id.'"/>';
            }, $data);
        }
        /* end import files */

        return $data;
    }

    private function importReference($ref_node, $model, $folder, $old_curriculum_id)
    {
        $reference = Reference::where('id', $ref_node->getAttribute('unique_id'))->first(); //check first --> do not use firstOrCreate, it will fail on some older *.curriculum exports

        if ($reference === null) {
            $reference = Reference::Create(['id'            => $ref_node->getAttribute('unique_id'),
                'owner_id'      => auth()->user()->id,
                'description'   => htmlspecialchars_decode(
                                        $this->importEmbeddedFiles(
                                            $this->getDescription($ref_node),
                                            $ref_node,
                                            $model,
                                            $folder,
                                            $old_curriculum_id,
                                            'embedded-file'
                                        ),
                                        ENT_QUOTES
                                   ),
                'grade_id'      => optional(Grade::where('title', $ref_node->getAttribute('grade'))->first())->id ?: 1,
            ]);
        }

        if ($reference->id == 0) {
            $reference = Reference::find($ref_node->getAttribute('unique_id'));
        }

        $reference->subscribe($model);
    }

    private function getDescription($ref_node)
    {
        if (! isset(getImmediateChildrenByTagName($ref_node, 'content')[0])) { //fix bug in import files
            return null;
        }
        $node = getImmediateChildrenByTagName($ref_node, 'content')[0];

        return getImmediateChildrenByTagName($node, 'text')[0]->nodeValue;
    }

    private function importQuote($ref_node, $model, $folder, $old_curriculum_id)
    {
        if (Quote::where('id', $ref_node->getAttribute('unique_id'))->first() === null) {
            $content = Content::where('id', $model->id)->get();

            $regex = '/\<quote id="quote_'.$ref_node->getAttribute('unique_id').'"\>(|.+?)\<\/quote\>/s';
            preg_match($regex, $content->first()->content, $matches);

            Quote::firstOrCreate(['id'       => $ref_node->getAttribute('unique_id'),
                'referenceable_type'=> get_class($model),
                'referenceable_id'=> $model->id,
                'quote' =>  $matches ? $matches[1] : null, //$matches[0] == with quote tag //$matches[1] == quote only
                'owner_id' => auth()->user()->id,
            ]);
        }
    }

    private function importQuoteSubscription($ref_node, $model, $folder, $old_curriculum_id)
    {
        QuoteSubscription::firstOrCreate([
            'quote_id' =>  $ref_node->getAttribute('unique_id'),
            'quotable_type'=> get_class($model),
            'quotable_id'=> $model->id,
            'sharing_level_id'=> $ref_node->getAttribute('sharing_level_id'),
            'visibility'=> $ref_node->getAttribute('sharing_level_id'),
            'owner_id'=> auth()->user()->id,
        ]);
    }

    /*
     * Import of "new" Exports (Laravel versions)
     */
    private function importLaravelExport($backup)
    {
        $folder = 'imports';
        $original_file_name = $backup->getClientOriginalName();

        $filename = pathinfo($original_file_name, PATHINFO_FILENAME);

        $zip_path = $backup
            ->storeAs("/{$folder}", $original_file_name
            );

        $zip_file = $folder.'/'.$original_file_name;
        $zip = new ZipArchive;
        if ($zip->open(storage_path("app/{$zip_file}")) === true) {
            $zip->extractTo(storage_path("app/{$folder}/{$filename}"));
            $zip->close();
        //ok
        } else {
            //error handling
        }

        $json = Storage::disk('local')->get("{$folder}/{$filename}/{$filename}.json");
        //dd(json_decode($json));
        $data = json_decode($json);

        $folder = "{$folder}/{$filename}";

        $medium = $this->importMediumV2($data->medium_id, $folder);
        $cur = $this->storeCurriculum($data, $medium);
        $cur->save();

        if ($medium != null) {
            $this->subscribeMediaToModel($cur, [$medium]);
        }

        // import curriculum.media
        foreach ($data->media as $media) {
            $this->subscribeMediaToModel($cur, [
                $this->importMediumV2($media->id, $folder),
            ]);
        }

        // import curriculum.contents
        $this->importAndSubscribeContent($data->contents, $folder, $cur);

        // import glossar.contents
        if (isset($data->glossar)) {
            $model = Glossar::Create([
                'subscribable_type' => "App\Curriculum",
                'subscribable_id' => $cur->id,
            ]);
            $this->importAndSubscribeContent($data->glossar->contents, $folder, $model);
        }
        $id_sync = [];

        //import terminal_objectives
        foreach ($data->terminal_objectives as $terminalObjective) {
            $mediaToSubscribe = $this->importEmbeddedMedia($terminalObjective->description, $folder);

            $ter = $this->storeTerminalObjective($terminalObjective, $cur);
            $id_sync['TerminalObjective'][$terminalObjective->id] = $ter->id; //to translate ids in certificates

            $this->subscribeMediaToModel($ter, $mediaToSubscribe);

            // import terminalObjective.media
            foreach ($terminalObjective->media as $media) {
                $this->subscribeMediaToModel($ter, [
                    $this->importMediumV2($media->id, $folder, "objectives/{$terminalObjective->id}/"),
                ]);
            }

            // import terminalObjective.contents
            $this->importAndSubscribeContent($terminalObjective->contents, $folder, $ter);

            // import references
            $this->importReferences($terminalObjective->references, $folder, $ter);

            // todo: import predecessors,successors

            //import enabling_objectives
            foreach ($terminalObjective->enabling_objectives as $enablingObjective) {
                $mediaToSubscribe = $this->importEmbeddedMedia($enablingObjective->description, $folder);

                $ena = $this->storeEnablingObjective($enablingObjective, $cur, $ter);
                $id_sync['EnablingObjective'][$enablingObjective->id] = $ena->id; //to translate ids in certificates

                $this->subscribeMediaToModel($ena, $mediaToSubscribe);

                // import enabling.media
                foreach ($enablingObjective->media as $media) {
                    $this->subscribeMediaToModel($ena, [
                        $this->importMediumV2($media->id, $folder, "objectives/{$terminalObjective->id}/{$enablingObjective->id}/"),
                    ]);
                }

                // import enablingObjective.contents
                $this->importAndSubscribeContent($enablingObjective->contents, $folder, $ena);

                // import enablingobjectives.references
                $this->importReferences($enablingObjective->references, $folder, $ena);

                // todo: import predecessors,successors
            }
        }

        // import certificates
        $this->importCertificates($data, $folder, $cur, $id_sync);
        //todo: import/subscribe predecessors and successors

        // cleanup
        Storage::delete($zip_file);
        Storage::deleteDirectory($folder);                                // clean up
    }

    private function importMediumV2($medium_id, $folder, $subfolder = '')
    {
        $pathPrefix = 'users/'.auth()->user()->id.'/';

        if (File::exists(storage_path("app/{$folder}/media/{$subfolder}{$medium_id}"))) {
            $file = File::files(storage_path("app/{$folder}/media/{$subfolder}{$medium_id}"));   // always only one file!
            File::copy($file[0]->getRealPath(), storage_path('app/'.$pathPrefix.$file[0]->getBasename()));

            return Medium::create([
                'path'          => '/'.$pathPrefix,
                'medium_name'   => $file[0]->getBasename(),
                'title'         => $file[0]->getBasename(),
                'description'   => '',
                'author'        => auth()->user()->username,
                'publisher'     => '',
                'city'          => '',
                'date'          => date('Y-m-d_H-i-s'),
                'size'          => File::size(storage_path('app/'.$pathPrefix.$file[0]->getBasename())),
                'mime_type'     => File::mimeType(storage_path('app/'.$pathPrefix.$file[0]->getBasename())),
                'license_id'    => 2,
                'public'        => 1,   //default public

                'owner_id'      => auth()->user()->id,
            ]);
        } else {
            return null;
        }
    }

    private function importEmbeddedMedia(&$string, $folder)
    {
        $media = [];
        preg_replace_callback("/src=\"\/media\/(.+?)\"/s",
            function ($matches) use ($folder, &$media) {
                $medium = $this->importMediumV2($matches[1], $folder, 'embedded/');
                if ($medium != null) {
                    $media[] = $medium;
                }

                return ($medium != null) ? 'src="/media/'.$medium->id.'"' : 'src=""';   //replace if medium != null
            },
            $string);

        return $media;
    }

    private function subscribeMediaToModel($model, $media)
    {
        foreach ($media as $medium) {
            $subscribe = MediumSubscription::updateOrCreate([
                'medium_id'         => $medium->id,
                'subscribable_type' => get_class($model),
                'subscribable_id'   => $model->id,
            ], [
                'sharing_level_id'  => 1, // has to be global = 1
                'visibility'        => 1, // has to be public  = 1
                'owner_id'          => auth()->user()->id,
            ]);
            $subscribe->save();
        }
    }

    /**
     * @param  array  $contents
     * @param  string  $folder
     * @param $model
     * @return bool
     */
    private function importAndSubscribeContent($contents, string $folder, $model): bool
    {
        foreach ($contents as $content) {
            $mediaToSubscribe = $this->importEmbeddedMedia($content->content, $folder);

            $content = Content::Create([
                'title' => $content->title,
                'content' => $content->content,
                'owner_id' => auth()->user()->id,
            ]);
            $content->subscribe($model);

            $this->subscribeMediaToModel($content, $mediaToSubscribe);
        }

        return true; //todo: error handling
    }

    /**
     * @param $data
     * @param  string  $folder
     * @param  Curriculum  $cur
     * @param  array  $id_sync
     */
    private function importCertificates($data, string $folder, Curriculum $cur, array $id_sync): void
    {
        foreach ($data->certificates as $certificate) {
            $mediaToSubscribe = $this->importEmbeddedMedia($certificate->body, $folder);

            $certificates = Certificate::Create([
                'title' => $certificate->title,
                'description' => $certificate->description,
                'body' => $this->translateIds($id_sync, $certificate->body), //todo:yet only App\TerminalObjective is translated (other models aren't used yet
                'curriculum_id' => $cur->id,
                'organization_id' => auth()->user()->current_organization_id,
                'owner_id' => auth()->user()->id,
                'global' => $certificate->global,
                'type' => $certificate->type,
            ]);
            $this->subscribeMediaToModel($certificates, $mediaToSubscribe);
        }
    }

    /**
     * @param  array  $id_sync
     * @param $string
     * @param  string  $model
     * @return string|string[]|null
     */
    private function translateIds(array $id_sync, $string, string $model = 'TerminalObjective')
    {
        return preg_replace_callback('/reference_type="App\\\\'.$model.'"\sreference_id="([[:digit:],]+)"/m',
            function ($matches) use ($model, $id_sync) {
                $old_ids = explode(',', $matches[1]);
                $new_ids = [];
                foreach ($old_ids as $old_id) {
                    $new_ids[] = $id_sync[$model][$old_id];
                }

                return 'reference_type="App\\'.$model.'" reference_id="'.implode(',', $new_ids).'\"';
            },
            $string);
    }

    /**
     * @param  array  $references
     * @param string folder
     * @param    $model
     */
    private function importReferences($references, string $folder, $model): void
    {
        foreach ($references as $reference) {
            $mediaToSubscribe = $this->importEmbeddedMedia($reference->description, $folder);
            $reference = Reference::firstOrCreate(
                ['id' =>  $reference->id],
                ['owner_id'      => auth()->user()->id,
                    'description'   => $reference->description,
                    'grade_id'      => $reference->grade_id,
                ]
            );
            $this->subscribeMediaToModel($reference, $mediaToSubscribe);
            $reference->subscribe($model);
        }
    }

    /**
     * @param $enablingObjective
     * @param  Curriculum  $cur
     * @param $ter
     * @return mixed
     */
    private function storeEnablingObjective($enablingObjective, Curriculum $cur, $ter)
    {
        return EnablingObjective::Create([
            'title' => $enablingObjective->title,
            'description' => $enablingObjective->description,
            'time_approach' => $enablingObjective->time_approach,
            'curriculum_id' => $cur->id,
            'terminal_objective_id' => $ter->id,
            'visibility' => $enablingObjective->visibility,
            'order_id' => $enablingObjective->order_id,
        ]);
    }

    /**
     * @param $terminalObjective
     * @param  Curriculum  $cur
     * @return mixed
     */
    private function storeTerminalObjective($terminalObjective, Curriculum $cur)
    {
        return TerminalObjective::Create([
            'title' => $terminalObjective->title,
            'description' => $terminalObjective->description,
            'color' => $terminalObjective->color,
            'time_approach' => $terminalObjective->time_approach,
            'curriculum_id' => $cur->id,
            'objective_type_id' => $terminalObjective->objective_type_id,
            'visibility' => $terminalObjective->visibility,
            'order_id' => $terminalObjective->order_id,
        ]);
    }

    /**
     * @param $data
     * @param $medium
     * @return Curriculum
     */
    private function storeCurriculum($data, $medium): Curriculum
    {
        return new Curriculum([
            'title' => $data->title,
            'description' => $data->description,
            'author' => $data->author,
            'publisher' => $data->publisher,
            'city' => $data->city,
            'date' => $data->date,
            'color' => $data->color,
            'grade_id' => optional(Grade::where('title', $data->grade_id)->first())->id ?: 1,
            'subject_id' => optional(Subject::where('title', $data->subject_id)->first())->id ?: 1,
            'organization_type_id' => optional(OrganizationType::where('id', $data->organization_type_id)->first())->id ?: 1,
            'type_id' => $data->type_id,
            'country_id' => $data->country_id,
            'medium_id' => ($medium != null) ? $medium->id : null,
            'owner_id' => auth()->user()->id,
        ]);
    }
}
