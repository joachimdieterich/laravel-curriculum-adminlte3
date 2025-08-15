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
            return Note::where($this->validateRequest())
                ->where('user_id', auth()->id())
                ->with(['notable'])
                ->latest('updated_at')->get();
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
        $note = Note::create([
            'title' => $new_note['title'],
            'content' => $new_note['content'],
            'notable_id' => $new_note['notable_id'],
            'notable_type' => $new_note['notable_type'],
            'user_id' => auth()->id(),
        ]);

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
