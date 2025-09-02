<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.KanbanItem.{kanbanItemId}', function (User $user) {
    return array_merge($user->only(['id', 'firstname', 'lastname']), ['initials' => $user->initials()]);
});
Broadcast::channel('App.KanbanItemComments.{kanbanItemId}', function (User $user) {
    return array_merge($user->only(['id', 'firstname', 'lastname']), ['initials' => $user->initials()]);
});
Broadcast::channel('App.KanbanItemComments.Whisper.{kanbanItemId}', function (User $user) {
    return array_merge($user->only(['id', 'firstname', 'lastname']), ['initials' => $user->initials()]);
});
Broadcast::channel('App.Kanban.{kanbanId}', function (User $user) {
    return array_merge($user->only(['id', 'firstname', 'lastname']), ['initials' => $user->initials()]);
});
Broadcast::channel('App.KanbanStatus.{kanbanStatusId}', function (User $user) {
    return array_merge($user->only(['id', 'firstname', 'lastname']), ['initials' => $user->initials()]);
});


// Typical public channel
// Broadcast::channel('channel.name', function ($a, $b) {
//     return true;
// });
