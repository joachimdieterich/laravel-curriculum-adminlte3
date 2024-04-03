<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\CurriculumSubscription;
use Illuminate\Http\Request;

class CurriculumSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            abort_unless((\Gate::allows('curriculum_access') and $model->isAccessible()), 403);

            $curriculum = $model->curriculum;

            return empty($curriculum) ? '' : DataTables::of($curriculum)
                ->setRowId('id')
                ->make(true);
        }
        else
        {
            if (request()->wantsJson())
            {

                $tokenscodes = CurriculumSubscription::where('curriculum_id', request('curriculum_id'))
                    ->where('sharing_token', "!=", null)
                    ->get();

                foreach ($tokenscodes as $token)
                {
                    $tokens[] = [
                        "token" => $token,
                        "qr"    => (new QRCodeHelper())
                            ->generateQRCodeByString(
                                env("APP_URL"). "/curricula/" . request('curriculum_id') ."/token?sharing_token=" .$token->sharing_token
                            )
                    ];
                }
                return [
                    'subscribers' => [
                        'tokens' => $tokens ?? [],
                        'subscriptions' => optional(
                            optional(
                                Curriculum::find(request('curriculum_id'))
                            )->subscriptions()
                        )->with('subscribable')
                            ->whereHasMorph('subscribable', '*', function ($q, $type) {
                                if ($type == 'App\\User') {
                                    $q->whereNot('id', env('GUEST_USER'));
                                }
                            })->get(),
                    ],
                ];
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        $curriculum = Curriculum::find($input['model_id']);
        abort_unless((\Gate::allows('curriculum_create') and $curriculum->isAccessible()), 403);

        $subscribe = CurriculumSubscription::updateOrCreate([
            'curriculum_id' => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => $curriculum->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CurriculumSubscription  $curriculumSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CurriculumSubscription $curriculumSubscription)
    {
        abort_unless((\Gate::allows('curriculum_edit') and $curriculumSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $curriculumSubscription->update([
            'editable'=> isset($input['editable']) ? $input['editable'] : false,
            'owner_id'=> auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $curriculumSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CurriculumSubscription  $curriculumSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(CurriculumSubscription $curriculumSubscription)
    {
        abort_unless((\Gate::allows('curriculum_delete') and $curriculumSubscription->isAccessible()), 403);

        if (request()->wantsJson()) {
            return ['message' => $curriculumSubscription->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes|integer',
            'editable'          => 'sometimes',
            'curriculum_id'=> 'sometimes',
        ]);
    }
}
