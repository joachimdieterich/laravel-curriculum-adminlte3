<?php

namespace App\Http\Controllers;

use App\Organization;
use App\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MessagesController extends Controller
{
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        // All threads, ignore deleted/archived participants
        //$threads = Thread::getAllLatest()->get();

        // All threads that user is participating in
        $threads = Thread::forUser(Auth::id())->with(['messages' => function ($q) {
            $q->latest('created_at');
        }, 'messages.user'])->latest('updated_at')->get();

        $inbox = $threads->count();
        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages(Auth::id())->latest('updated_at')->get();

        if (request()->wantsJson()) {
            $users = (auth()->user()->role()->id == 1) ? DB::table('users')->select('id', 'username', 'firstname', 'lastname', 'medium_id') : Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users();

            return ['threads' => $threads,
                'users' => $users->get(), ];
        }

        return view('messenger.index', compact('threads', 'inbox'));
    }

    /**
     * Shows a message thread.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $threads = Thread::forUser(Auth::id())->latest('updated_at')->get();

        $inbox = $threads->count();
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: '.$id.' was not found.');

            return redirect()->route('messages');
        }

        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

        // don't show the current user in list
        $userId = Auth::id();
        $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();

        $thread->markAsRead($userId);

        return view('messenger.show', compact('thread', 'users', 'inbox'));
    }

    /**
     * Creates a new message thread.
     *
     * @return mixed
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $inbox = Thread::forUserWithNewMessages(Auth::id())->latest('updated_at')->get()->count();

        return view('messenger.create', compact('users', 'inbox'));
    }

    /**
     * Stores a new message thread.
     *
     * @return mixed
     */
    public function store()
    {
        $input = Request::all();

        $thread = Thread::create([
            'subject' => $input['subject'],
        ]);

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $input['message'],
        ]);

        // Sender
        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'last_read' => new Carbon,
        ]);

        // Recipients
        if (Request::has('recipients')) {
            if ($input['recipients'] != null) {
                $thread->addParticipant($input['recipients']);
            }
        }
        if (request()->wantsJson()) {
            $newEntry = Thread::where('id', $thread->id)->with(['messages' => function ($q) {
                $q->latest('created_at');
            }, 'messages.user'])
                ->latest('updated_at')->get();

            return ['thread' => $newEntry];
        }

        return redirect()->route('messages');
    }

    /**
     * Adds a new message to a current thread.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: '.$id.' was not found.');

            return redirect()->route('messages');
        }

        $thread->activateAllParticipants();

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => Request::input('message'),
        ]);

        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
        ]);
        $participant->last_read = new Carbon;
        $participant->save();

        // Recipients
        if (Request::has('recipients')) {
            if (Request::input('recipients') != null) {
                $thread->addParticipant(Request::input('recipients'));
            }
        }

        // axios call?
        if (request()->wantsJson()) {
            $newEntry = Thread::where('id', $id)->with(['messages' => function ($q) {
                $q->latest('created_at');
            }, 'messages.user'])
                ->latest('updated_at')->get();

            return ['thread' => $newEntry];
        }

        return redirect()->route('messages.show', $id);
    }

    public function destroy($id)
    {
        $message = Message::find($id);

        $return = $message->delete();
        //todo: concept to hard-delete messages
        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }
}
