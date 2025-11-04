<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tags\AttachTagRequest;
use App\Http\Requests\Tags\StoreTagRequest;
use App\Http\Requests\Tags\UpdateTagRequest;
use App\Role;
use App\Services\Tag\TagService;
use App\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
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
                'type'     => 'sometimes|string|nullable',
                'term'     => 'sometimes|string|max:255|nullable',
                'page'     => 'sometimes|integer',
            ]);

            $selected = $input['selected'] ?? null;

            return $service->getEntriesForSelect2ByModel(
                $selected !== null ? explode(",", $input['selected'] ?? '') : [],
                $input['type'] ?? '',
                $input['term'] ?? '',
                $input['page'] ?? 1,
            );
        }

        return view('tags.index');
    }

    public function list(DataTables $dt): JsonResponse
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        $locale = App::currentLocale();

        $builder = Tag::select([
            "id",
            "type",
            "name->{$locale} as translation",
        ])->where('user_id', auth()->user()->id);

        $filter = request()->input('filter');
        if ($filter) {
            $builder->where("type", $filter == 'global' ? null : $filter);
        }

        $data = $dt->eloquent($builder)
            ->filterColumn('translation', function (Builder $query, $keyword) use ($locale) {
                $query->orWhere("name->{$locale}", 'LIKE', "%{$keyword}%");
            })
            ->setRowId('id');

        return $data->make();
    }

    public function store(StoreTagRequest $request)
    {
        $tag = Tag::findOrCreate(
            $request->get('name'),
            $request->get('global') ? null : $request->get('type'),
        );

        if (request()->wantsJson()) {
            return $tag;
        }
    }

    public function attach(AttachTagRequest $request)
    {
        $tag = Tag::findOrCreate(
            $request->get('name'),
            $request->get('global') ? null : $request->get('type'),
        );

        /** @var Role $role */
        $role = ($request->get('type'))::findOrFail($request->get('taggable_id'));
        $role->attachTag($tag);

        if (request()->wantsJson()) {
            return $tag;
        }
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->all());

        if (request()->wantsJson()) {
            return $tag;
        }
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

    public function type(TagService $tagService): JsonResponse
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        if (request()->data_only) {
            $result = [];

            if (Tag::getTypesWithGlobal()->contains(null)) {
                $result[] = [
                    'label' => trans('global.tag.global'),
                    'type' => 'global',
                ];
            }

            $tagType = Tag::getTypes();
            foreach (Tag::getTranslatedTypes() as $index => $translatedType) {
                $result[] = [
                    'label' => $translatedType,
                    'type' => $tagType->pull($index),
                ];
            }

            return response()->json($result);
        }

        return $tagService->formatCollectionForSelect2ByModel(Tag::getTranslatedTypes(), Tag::getTypes());
    }
}
