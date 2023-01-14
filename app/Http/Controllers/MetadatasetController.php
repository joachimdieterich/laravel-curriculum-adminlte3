<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\Metadataset;
use App\ObjectiveType;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class MetadatasetController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('metadataset_access'), 403);

        return view('metadatasets.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('metadataset_access'), 403);
        $metadataset = Metadataset::select([
            'id',
            'version',
            'created_at',
        ])->get();

        $show_gate = \Gate::allows('metadataset_show');
        $delete_gate = \Gate::allows('metadataset_delete');

        return DataTables::of($metadataset)
            ->addColumn('action', function ($metadataset) use ($show_gate, $delete_gate) {
                $actions = '';
                if ($show_gate) {
                    $actions .= '<a href="/metadatasets/'.$metadataset->id.'?csv=true" '
                        .'id="show-user-'.$metadataset->id.'" '
                        .'class="btn">'
                        .'<i class="fa fa-file-csv"></i>'
                        .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'metadatasets\','.$metadataset->id.')">'
                        .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    public function create()
    {
        abort_unless(\Gate::allows('metadataset_create'), 403);

        return view('metadatasets.create');
    }

    public function store()
    {
        abort_unless(\Gate::allows('metadataset_create'), 403);

        $metadata = []; //{'id', 'title'}
        $curricula = Curriculum::where('type_id', 1)->get();

        foreach ($curricula as $curriculum) {
            $current_metadata = $this->processCurriculum($curriculum);
            $metadata[] = $current_metadata;
        }

        $new_metadataset = $this->validateRequest();

        Metadataset::create([
            'version' => $new_metadataset['version'],
            'metadataset' => json_encode($metadata),

        ]);

        return redirect()->route('metadatasets.index');
    }

    public function show(Metadataset $metadataset)
    {
        abort_unless(\Gate::allows('metadataset_show'), 403);

        if (request('csv')) {
            $this->downloadCsv($metadataset);
        } else {
            dd($metadataset); //todo: enhanced output
        }
    }

    public function destroy(Metadataset $metadataset)
    {
        abort_unless(\Gate::allows('metadataset_delete'), 403);

        $metadataset->delete();

        return back();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes|required',
            'version' => 'sometimes|required',
        ]);
    }

    private function processCurriculum($curriculum)
    {
        $metadata = []; //{'id', 'title'}

        // generate curriculum part of identifier
        $this->setModelUuid($curriculum);
        /*$metadata[] = [
            'id'        => $curriculum->uuid,
            'old_id'    => $curriculum->ui,
            'title'     => $curriculum->title,
            'parent_id' => null
        ];*/
        //hack for rlp until new edusharing-version is active
        $metadata[] = [
            'id' => ($curriculum->ui != null) ? $curriculum->ui : $curriculum->uuid,
            'title' => $curriculum->title,
            'parent_id' => null,
        ];

        $previous_objectiveType = '';

        foreach ($curriculum->terminalObjectives()->get() as $terminalObjective) {            // foreach terminal_type
            // terminal objective type
            $current_objectiveType = ObjectiveType::where('id', $terminalObjective->objective_type_id)->get()->first();
            $this->setModelUuid($current_objectiveType);
            if ($previous_objectiveType != $current_objectiveType->uuid) {
                $previous_objectiveType = $current_objectiveType->uuid;

                /* $metadata[] = [
                     'id' => $curriculum->uuid.$current_objectiveType->uuid, //concat curriculum->uuid and objectiveType->uuid to get it unique for every curriculum
                     'old_id'    => 'null',
                     'title' => $this->format_data(ObjectiveType::where('id', $terminalObjective->objective_type_id)->get()->first()->title),
                     'parent_id' => $curriculum->uuid
                 ];*/
                $metadata[] = [
                    'id' => $curriculum->uuid.$current_objectiveType->uuid, //concat curriculum->uuid and objectiveType->uuid to get it unique for every curriculum
                    'old_id' => 'null',
                    'title' => $this->format_data(ObjectiveType::where('id', $terminalObjective->objective_type_id)->get()->first()->title),
                    'parent_id' => ($curriculum->ui != null) ? $curriculum->ui : $curriculum->uuid,
                ];
            }

            // terminal objective
            $this->setModelUuid($terminalObjective);
            /*$metadata[] = [
                'id'        => $terminalObjective->uuid,
                'old_id'    => $terminalObjective->ui,
                'title'     => $this->format_data($terminalObjective->title),
                'parent_id' => $curriculum->uuid.$current_objectiveType->uuid, // combined uuids
            ];*/
            //hack for rlp until new edusharing-version is active
            $metadata[] = [
                'id' => ($terminalObjective->ui != null) ? $terminalObjective->ui : $terminalObjective->uuid,
                'title' => $this->format_data($terminalObjective->title),
                'parent_id' => $curriculum->uuid.$current_objectiveType->uuid, // combined uuids
            ];

            // enabling objective
            foreach ($terminalObjective->enablingObjectives()->get() as $enablingObjective) {
                $this->setModelUuid($enablingObjective);
                /* $metadata[] = [
                     'id'        => $enablingObjective->uuid,
                     'old_id'    => $enablingObjective->ui,
                     'title'     => $this->format_data($enablingObjective->title),
                     'parent_id' => $terminalObjective->uuid,
                 ];*/
                //hack for rlp until new edusharing-version is active
                $metadata[] = [
                    'id' => ($enablingObjective->ui != null) ? $enablingObjective->ui : $enablingObjective->uuid,
                    'title' => $this->format_data($enablingObjective->title),
                    'parent_id' => ($terminalObjective->ui != null) ? $terminalObjective->ui : $terminalObjective->uuid,
                ];
            }
        }

        return $metadata;
    }

    /**
     * @param $model
     */
    private function setModelUuid($model): void
    {
        if ($model->uuid == null) {
            $model->uuid = Str::uuid();
            $model->save();
        }
    }

    private function format_data($input)
    {
        $entry_limiter = 150;   //todo: should be set dynamic

        $input = preg_replace('/<br>/', ' ', $input);
        $input = strip_tags($input);
        $input = strlen($input) > $entry_limiter ? substr($input, 0, $entry_limiter).'...' : $input;  // limit text

        return mb_ereg_replace('\s+', ' ', mb_convert_encoding($input, 'UTF-8', 'UTF-8')); // replace multiple spaces, tabs, or linebrakes with one single space
    }

    /**
     * @param  array  $csv
     * @param  Metadataset  $metadataset
     */
    private function downloadCsv(Metadataset $metadataset): void
    {
        $csv = [];
        $csv[] = ['oldValue', 'newValue'];
        foreach (json_decode($metadataset->metadataset, true) as $curriculumEntries) {
            foreach ($curriculumEntries as $entries) {
                $csv[] = [
                    (isset($entries['old_id']) ? $entries['old_id'] : null),
                    $entries['id'],
                ];
            }
        }

        $fileName = 'Metadataset_version_'.$metadataset->version.'.csv';

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename={$fileName}");
        header('Expires: 0');
        header('Pragma: public');

        $fh = @fopen('php://output', 'w');

        foreach ($csv as $data) {
            fputcsv($fh, $data);
        }

        fclose($fh);                // Close the file
    }
}
