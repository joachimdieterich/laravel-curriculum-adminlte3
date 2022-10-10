<?php

namespace App\Http\Controllers;

use App\Content;
use App\Curriculum;
use App\Medium;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CurriculumExportController extends Controller
{
    /*
     * Main function
     */
    public function export(Curriculum $curriculum)
    {
        $curriculumWithRelations = Curriculum::where('id', $curriculum->id)
            ->with([
                'state',
                'country',
                'grade',
                'organizationType',
                'subject',
                'type',
                'contents',
                'certificates',
                'medium',
                'media',
                'glossar',
                'glossar.contents',
                'predecessors.predecessor',
                'successors.successor',
                'terminalObjectives',
                'terminalObjectives.type',
                'terminalObjectives.media',
                'terminalObjectives.references',
                //'terminalObjectives.quotes',
                'terminalObjectives.contents',
                'terminalObjectives.predecessors.predecessor',
                'terminalObjectives.successors.successor',
                'terminalObjectives.enablingObjectives',
                'terminalObjectives.enablingObjectives.level',
                'terminalObjectives.enablingObjectives.media',
                'terminalObjectives.enablingObjectives.references',
                //'terminalObjectives.enablingObjectives.quotes',
                'terminalObjectives.enablingObjectives.contents',
                'terminalObjectives.enablingObjectives.predecessors.predecessor',
                'terminalObjectives.enablingObjectives.successors.successor',
            ])->get()->first();

        $exportFolder = 'export/'.$curriculum->id;
        $filename = date('Y-m-d_H-i-s').'_'.$curriculum->id;
        Storage::deleteDirectory($exportFolder);                                // remove potential artefacts of previous export

        Storage::disk('local')->put($exportFolder.'/'.$filename.'.json', json_encode($curriculumWithRelations));

        $this->exportEmbeddedFiles($curriculumWithRelations->description, $exportFolder);

        // image of curriculum
        if ($curriculumWithRelations->medium !== null) {
            if (Storage::exists($curriculumWithRelations->medium->relativePath()) and
                ! Storage::exists($exportFolder.'/media/'.$curriculumWithRelations->medium->medium_name)) {
                Storage::copy($curriculumWithRelations->medium->relativePath(), $exportFolder."/media/{$curriculumWithRelations->medium->id}/".$curriculumWithRelations->medium->medium_name);
            }
        }

        // curricula
        foreach ($curriculumWithRelations->media as $medium) {
            if (Storage::exists($medium->relativePath()) and
                ! Storage::exists($exportFolder."/media/{$medium->id}/".$medium->medium_name)) {
                Storage::copy($medium->relativePath(), $exportFolder."/media/{$medium->id}/".$medium->medium_name);
            }
        }

        //curricula.content
        if (isset($curriculumWithRelations->contents)) {
            $this->checkMediaLinksInContent($curriculumWithRelations->contents, $exportFolder);
        }
        if (isset($curriculumWithRelations->glossar->contents)) {
            $this->checkMediaLinksInContent($curriculumWithRelations->glossar->contents, $exportFolder);
        }

        //terminal objectives with enabling objectives
        $this->exportTerminalObjectives($curriculumWithRelations, $exportFolder);

        $this->exportCertificates($curriculumWithRelations, $exportFolder);

        //todo: export quotes

        $this->zipFiles($filename, $exportFolder);
        Storage::deleteDirectory($exportFolder);                                // clean up

        if (request()->wantsJson()) {
            return ['path' => $this->addFileToDb($filename.'.cur')];
        }
    }

    private function checkMediaLinksInContent($array, $exportFolder)
    {
        foreach ($array as $content) {
            $this->exportEmbeddedFiles($content->content, $exportFolder);
        }
    }

    private function exportEmbeddedFiles($entry, $exportFolder)
    {
        preg_match_all('/<img[^\>]*src="\/media\/(.+?)"[^\>]*>/s', $entry, $matches, PREG_SET_ORDER, 0);
        //dump($matches);
        foreach ($matches as $match) {
            $medium = Medium::find($match[1]);
            if (Storage::exists($medium->relativePath()) and
                ! Storage::exists($exportFolder."/media/embedded/{$medium->id}/{$medium->medium_name}")) {
                Storage::copy($medium->relativePath(), $exportFolder."/media/embedded/{$medium->id}/{$medium->medium_name}"); //use id as foldername to get link between filename and relative path link in content
            }
        }
    }

    /**
     * @param $curriculumWithRelations
     * @param  string  $exportFolder
     */
    private function exportTerminalObjectives($curriculumWithRelations, string $exportFolder): void
    {
        foreach ($curriculumWithRelations->terminalObjectives as $terminalObjective) {
            $this->exportEmbeddedFiles($terminalObjective->description, $exportFolder);

            foreach ($terminalObjective->media as $medium) {
                //todo: check if file exist , e.g. check all media before export starts.
                Storage::copy($medium->relativePath(), $exportFolder."/media/objectives/{$terminalObjective->id}/{$medium->id}/".$medium->medium_name);
            }

            $this->checkMediaLinksInContent($terminalObjective->contents, $exportFolder);

            $this->exportEnablingObjectives($terminalObjective, $exportFolder);
        }
    }

    /**
     * @param $terminalObjective
     * @param  string  $exportFolder
     */
    private function exportEnablingObjectives($terminalObjective, string $exportFolder): void
    {
        foreach ($terminalObjective->enablingObjectives as $enablingObjective) {
            $this->exportEmbeddedFiles($enablingObjective->description, $exportFolder);

            foreach ($enablingObjective->media as $medium) {
                //todo: check if file exist , e.g. check all media before export starts.
                Storage::copy($medium->relativePath(), $exportFolder."/media/objectives/{$terminalObjective->id}/{$enablingObjective->id}/{$medium->id}/".$medium->medium_name);
            }
            $this->checkMediaLinksInContent($enablingObjective->contents, $exportFolder);
        }
    }

    private function exportCertificates($curriculumWithRelations, string $exportFolder): void
    {
        foreach ($curriculumWithRelations->certificates as $certificate) {
            $this->exportEmbeddedFiles($certificate->description, $exportFolder);
        }
    }

    /**
     * @param  string  $filename
     * @param  string  $exportFolder
     */
    private function zipFiles(string $filename, string $exportFolder): void
    {
        $zip_file = storage_path('app/export/'.$filename.'.cur');

        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $path = storage_path('app/'.$exportFolder);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {
            // We're skipping all subfolders
            if (! $file->isDir()) {
                $filePath = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = substr($filePath, strlen($path) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
    }

    protected function addFileToDb($filename)
    {
        $media = new Medium([
            'path'          => '/export/',
            'title'         => $filename,
            'medium_name'   => $filename,
            'description'   => '',
            'author'        => auth()->user()->fullName(),
            'publisher'     => '',
            'city'          => '',
            'date'          => date('Y-m-d_H-i-s'),
            'size'          => File::size(Storage::disk('local')->path('export/'.$filename)),
            'mime_type'     => File::mimeType(Storage::disk('local')->path('export/'.$filename)),
            'license_id'    => 2,
            'public'        => 0,           //certificates can not be accessed without owership/subscription
            'owner_id'      => auth()->user()->id,
        ]);
        $media->save();

        /*
         * add subscription
         */
        $media->subscribe(auth()->user(), 4);

        return  $media->path();
    }
}
