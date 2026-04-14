<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tags\AttachTagRequest;
use App\Http\Requests\Tags\StoreTagRequest;
use App\Http\Requests\Tags\UpdateTagRequest;
use App\Role;
use App\Services\Tag\HasTags;
use App\Services\Tag\TagService;
use App\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\DataTables;

class TagsController extends Controller
{
    public function index(TagService $service)
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        if (request()->wantsJson()) {
            $input = request()->validate([
                'selected' => 'sometimes|nullable',
                'term'     => 'sometimes|string|max:255|nullable',
                'page'     => 'sometimes|integer',
            ]);

            $selected = $input['selected'] ?? null;

            return $service->getEntriesForSelect2ByModel(
                $selected !== null ? explode(",", $input['selected'] ?? '') : [],
                $input['term'] ?? '',
                $input['page'] ?? 1,
            );
        }

        return view('tags.index');
    }

    public function list(): JsonResponse
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        $locale = App::currentLocale();

        $builder = Tag::select("id", "name->{$locale} as translation");

        if (!is_admin()) $builder->where('user_id', auth()->user()->id);

        return DataTables::of($builder)
            ->filterColumn('translation', function (Builder $query, $keyword) use ($locale) {
                $query->orWhere("name->{$locale}", 'LIKE', "%{$keyword}%");
            })
            ->make(true);
    }

    public function store(StoreTagRequest $request)
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        $tag = Tag::findOrCreate(
            $request->input('name'),
        );

        if (request()->wantsJson()) {
            return $tag;
        }
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        $tag->update($request->all(['name']));

        return $tag->refresh();
    }

    public function show(Tag $tag)
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        return view('tags.show')
            ->with(compact('tag'));
    }

    public function destroy(Tag $tag)
    {
        abort_unless(\Gate::allows('tag_delete'), 403);

        $return = $tag->delete();

        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }

    public function attach(AttachTagRequest $request)
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        $tag = Tag::findOrCreate(
            $request->input('name'),
        );

        /** @var Role $role */
        $role = ($request->input('type'))::findOrFail($request->input('taggable_id'));
        $role->attachTag($tag);

        if (request()->wantsJson()) {
            return $tag;
        }
    }

    public function saveModelTags(Request $request)
    {
        abort_unless(\Gate::allows('tag_delete'), 403);

        /** @var HasTags|Model $model */
        $model = ($request->input('model'))::findOrFail($request->input('id'));
        $model->tags()->sync($request->input('tags'));

        return $model->fresh();
    }
}
