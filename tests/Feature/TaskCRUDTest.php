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
        //todo: not working
        /*$tasks = Task::all();
        $this->get('tasks')
             ->assertStatus(200)
            ->assertViewHas($tasks->toArray());*/
    }

    /** @test
     * Use Route: POST, tasks, tasks.index
     */
    public function an_administrator_create_a_task()
    {
        $this->followingRedirects()
            ->post('tasks', $attributes = Task::factory()->raw())
            ->assertStatus(200);

        $task = Task::where('title', $attributes['title'])->first();
        $this->assertDatabaseHas('tasks', $task->toArray());
    }

    /** @test
     * Use Route: DELETE, tasks/{task}, tasks.destroy
     */
    public function an_administrator_delete_a_task()
    {
        $task = Task::factory()->create();

        $this->followingRedirects()
            ->delete('tasks/'.$task->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $task->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, tasks/{task}, tasks.show
     */
    public function an_administrator_see_details_of_an_task()
    {
        $task = Task::factory()->create();

        $this->get("tasks/{$task->id}")
            ->assertStatus(200)
            ->assertSee($task->toArray());
    }

    /** @test
     * Use Route: PUT|PATCH, tasks/{task}, tasks.update
     */
    public function an_administrator_update_a_task()
    {
        $this->post('tasks', $attributes = Task::factory()->raw());
        $task = Task::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('tasks', $task);

        $this->patch('tasks/'.$task['id'], $new_attributes = Task::factory()->raw());

        $task_edit = Task::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('tasks', $task_edit);
    }
}
