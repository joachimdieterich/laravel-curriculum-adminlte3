<?php

namespace App\Http\Controllers;

use App\KanbanItem;
use App\KanbanItemComment;
use Illuminate\Http\Request;
use Maize\Markable\Models\Like;

class KanbanItemCommentController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('kanban_edit'), 403);

        $input = $this->validateRequest();

        $comment = KanbanItemComment::Create([
            'comment' => $input['comment'],
            'kanban_item_id' => $input['model_id'],
            'user_id' => Auth()->user()->id
        ]);

        if (request()->wantsJson()) {
            return $comment->with(['user', 'likes'])->find($comment->id);
        }
    }

    public function destroy(KanbanItemComment $kanbanItemComment)
    {
        abort_unless(\Gate::allows('kanban_delete'), 403);

        return $kanbanItemComment->delete();
    }

    private function getComments($id)
    {
        return KanbanItemComment::where('kanban_item_id', $id)
            ->with(['user', 'likes'])->get();
    }

    /**
     * React to kanbanItem the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KanbanItem  $kanbanItem
     * @return \Illuminate\Http\Response
     */
    public function reaction(Request $request, KanbanItemComment $kanbanItemComment)
    {
        abort_unless( $kanbanItemComment->kanbanItem->isAccessible(), 403);

        $input = $this->validateRequest();
        //todo: use reaction_type 'like'...
        if (Like::has($kanbanItemComment, auth()->user())){
            Like::remove($kanbanItemComment, auth()->user()); // unmarks the $kanbanItem liked for the given user
        } else {
            Like::add($kanbanItemComment, auth()->user()); // marks the $kanbanItem liked for the given user
        }

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanItemUpdatedEvent(KanbanItem::find($kanbanItemComment->kanban_item_id))))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' => KanbanItemComment::where('id', $kanbanItemComment->id)
                        ->with([
                            'likes',
                        ])->get()->first(),
                ];
            }
        }
    }

    private function validateRequest()
    {
        return request()->validate([
            'comment' => 'sometimes',
            'model_id' => 'sometimes',
        ]);
    }
}
