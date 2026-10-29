<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\CurriculumSubscription;
use App\Helpers\QRCodeHelper;
use Exception;
use Gate;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;

class CurriculumSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return string|Response|array
     *
     * @throws Exception
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['subscribable_type'], $input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            abort_unless((Gate::allows('curriculum_access') and $model->isAccessible()), 403);

            $curriculum = $model->curriculum;

            return empty($curriculum) ? '' : DataTables::of($curriculum)
                ->setRowId('id')
                ->make();
        }

        if (request()->wantsJson()) {
            $tokenCodes = CurriculumSubscription::where('curriculum_id', request('curriculum_id'))
                ->where('sharing_token', '!=', null)
                ->get();

            foreach ($tokenCodes as $token) {
                $tokens[] = [
                    'token' => $token,
                    'qr'    => (new QRCodeHelper)
                        ->generateQRCodeByString(
                            config('app.url') . '/curricula/' . request('curriculum_id') . '/token?sharing_token=' . $token->sharing_token
                        ),
                ];
            }

            return [
                'tokens'        => $tokens ?? [],
                'subscriptions' => Curriculum::find(request('curriculum_id'))
                    ?->subscriptions()
                    ?->with('subscribable')
                    ->whereHasMorph('subscribable', '*', function ($q, $type) {
                        if ($type == 'App\\User') {
                            $q->whereNot('id', config('app.guest_user_id'));
                        }
                    })
                    ->get(),
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return CurriculumSubscription
     */
    public function store()
    {
        $input      = $this->validateRequest();
        $curriculum = Curriculum::find($input['model_id']);
        abort_unless($curriculum->isEditable(), 403);

        $subscribe = CurriculumSubscription::updateOrCreate([
            'curriculum_id'     => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id'   => $input['subscribable_id'],
        ], [
            'editable' => $input['editable'] ?? false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if ($input['subscribable_type'] === 'App\\Group') {
            auth()->user()->groups()->syncWithoutDetaching([$input['subscribable_id'] => ['evaluator' => true]]);
        }

        return CurriculumSubscription::with('subscribable')
            ->whereHasMorph('subscribable', '*', function ($q, $type) {
                if ($type == 'App\\User') {
                    $q->whereNot('id', config('app.guest_user_id'));
                }
            })
            ->find($subscribe->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return array
     */
    public function update(CurriculumSubscription $curriculumSubscription)
    {
        abort_unless((Gate::allows('curriculum_edit') and $curriculumSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $curriculumSubscription->update([
            'editable' => $input['editable'] ?? false,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $curriculumSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return array
     */
    public function destroy(CurriculumSubscription $curriculumSubscription)
    {
        abort_unless((Gate::allows('curriculum_delete') and $curriculumSubscription->isAccessible()), 403);

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
            'curriculum_id'     => 'sometimes',
        ]);
    }
}
