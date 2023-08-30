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
        abort_unless(\Gate::allows('kanban_show'), 403);

        $new_comment = $this->validateRequest();

        $comment = new KanbanItemComment();
        $comment->comment = $new_comment['comment'];
        $comment->kanban_item_id = $request['model_id'];
        $comment->user_id = Auth()->user()->id;
        $comment->save();

        $comments = $this->getComments($request['model_id']);

        $kanbanItem = KanbanItem::where('id', $request['model_id'])->get()->first();
        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanItemCommentUpdatedEvent($kanbanItem)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' => KanbanItem::where('id', $request['model_id'])
                        ->with(['comments', 'comments.user'])->get()->first(),
                ];
            }
        }
        //return response()->json(['data' => $comments]);
    }

    public function destroy(KanbanItemComment $kanbanItemComment)
    {
        abort_unless(\Gate::allows('kanban_delete'), 403);
        $kanban_item_id = $kanbanItemComment->kanban_item_id;
        $kanbanItemComment->delete();

        $comments = $this->getComments($kanban_item_id);

        return response()->json(['data' => $comments]);
    }

    private function getComments($id)
    {
        return KanbanItemComment::where('kanban_item_id', $id)->with('user')->get();
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
