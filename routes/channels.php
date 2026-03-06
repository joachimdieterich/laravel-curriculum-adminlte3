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

// Curriculum
Broadcast::channel('App.Curriculum.{curriculumId}', function (User $user) {
    return array_merge($user->only(['id', 'firstname', 'lastname']), ['initials' => $user->initials()]);
});

// Kanban
Broadcast::channel('App.Kanban.{kanbanId}', function (User $user) {
    return array_merge($user->only(['id', 'firstname', 'lastname']), ['initials' => $user->initials()]);
});
Broadcast::channel('App.KanbanStatus.{kanbanStatusId}', function () {
    return true;
});
Broadcast::channel('App.KanbanItem.{kanbanItemId}', function () {
    return true;
});
Broadcast::channel('App.KanbanItemComments.{kanbanItemId}', function () {
    return true;
});

// Typical public channel
// Broadcast::channel('channel.name', function ($a, $b) {
//     return true;
// });
