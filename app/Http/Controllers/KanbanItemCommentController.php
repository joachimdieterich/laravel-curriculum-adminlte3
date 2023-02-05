<?php

namespace App\Http\Controllers;

use App\KanbanItem;
use App\KanbanItemComment;
use Illuminate\Http\Request;

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

    private function validateRequest()
    {
        return request()->validate([
            'comment' => 'required',
            'model_id' => 'required',
        ]);
    }
}
