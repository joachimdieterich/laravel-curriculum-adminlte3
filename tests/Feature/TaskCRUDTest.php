<?php

namespace Tests\Feature;

use App\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, tasks, tasks.index
     */
    public function an_user_can_see_tasks()
    {
        $tasks = Task::all();

        $this->get('tasks')
             ->assertStatus(200)
             ->assertViewHasAll(compact($tasks));
    }

    /** @test
     * Use Route: POST, tasks, tasks.index
     */
    public function an_administrator_create_a_task()
    {
        $attributes = factory('App\Task')->raw();

        $this->post('tasks', $attributes)
                ->assertStatus(302);

        $task = Task::where('title', $attributes['title'])->first();
        $this->assertDatabaseHas('tasks', $task->toArray());
    }

    /** @test
     * Use Route: DELETE, tasks/{task}, tasks.destroy
     */
    public function an_administrator_delete_a_task()
    {
        $this->post('tasks', $task = factory('App\Task')->raw());
        $id = Task::where('title', $task['title'])->first()->id;

        $this->followingRedirects()
                ->delete('tasks/'.$id)
                ->assertStatus(200);
    }

    /** @test
     * Use Route: GET|HEAD, tasks/{task}, tasks.show
     */
    public function an_administrator_see_details_of_an_task()
    {
        $this->post('tasks', $attributes = factory('App\Task')->raw());
        $task = Task::where('title', $attributes['title'])->get()->first();

        $this->get(route('tasks.show', $task->id))
             ->assertStatus(200)
             ->assertViewHasAll(compact($task));
    }

    /** @test
     * Use Route: PUT|PATCH, tasks/{task}, tasks.update
     */
    public function an_administrator_update_a_task()
    {
        $this->post('tasks', $attributes = factory('App\Task')->raw());
        $task = Task::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('tasks', $task);

        $this->patch('tasks/'.$task['id'], $new_attributes = factory('App\Task')->raw());
        $task_edit = Task::where('title', $new_attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('tasks', $task_edit);
    }
}
