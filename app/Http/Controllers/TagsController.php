<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tags\StoreTagRequest;
use App\Http\Requests\Tags\UpdateTagRequest;
use App\Services\Tag\TagService;
use App\Tag;
use Illuminate\Http\JsonResponse;
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

    public function list()
    {
        abort_unless(\Gate::allows('tag_access'), 403);

        $tags = Tag::select([
            'id',
            'name',
        ]);

        return DataTables::of($tags)
            ->addColumn('translation', function ($tags) {
                return $tags->translation;
            })
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make();
    }

    public function store(StoreTagRequest $request)
    {
        $tag = Tag::findOrCreate(
            $request->get('name'),
            $request->get('type'),
        );

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

        return $tagService->formatCollectionForSelect2ByModel(Tag::getTranslatedTypes(), Tag::getTypes());
    }
}
