<?php

namespace App\Http\Controllers;

use App\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //abort_unless(\Gate::allows('note_access'), 403);
        if (request()->wantsJson()) {
            $input = $this->validateRequest();

            if (isset($input['notable_type'], $input['notable_id'])) {
                return Note::where('notable_type', $input['notable_type'])
                    ->whereIn('notable_id', explode(',', $input['notable_id']))
                    ->where('user_id', auth()->id())
                    ->with(['notable'])
                    ->latest('updated_at')->get();
            } else { // get all written notes from this user
                return Note::where('user_id', auth()->id())
                    ->with(['notable'])
                    ->latest('updated_at')->get();
            } // TODO: get notes for user => a student should see notes that were created to a resource of his
        }

        return view('notes.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_note = $this->validateRequest();
        $id = $new_note['notable_id'];
        // handle every request as an array of ids
        if (gettype($id) != 'array') { $id = [$id]; }
        // to be able to iterate through them
        foreach ($id as $notable_id) {
            $note = Note::Create([
                'title' => $new_note['title'],
                'content' => $new_note['content'],
                'notable_id' => $notable_id,
                'notable_type' => $new_note['notable_type'],
                'user_id' => auth()->id(),
            ]);
        }
        // even if multiple notes get created, we can just send the last one, since they have the same content
        return $note->load('notable');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        abort_unless($note->user_id == auth()->user()->id, 403);

        $note->update($this->validateRequest());

        return $note->load('notable');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        abort_unless($note->user_id == auth()->user()->id, 403);

        if (request()->wantsJson()) {
            return ['message' => $note->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'content' => 'sometimes|required',
            'notable_id' => 'sometimes',
            'notable_type' => 'sometimes',
        ]);
    }
}
