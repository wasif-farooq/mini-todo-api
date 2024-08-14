<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the creation of a task.
     *
     * @return void
     */
    public function testCreateTask()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task description',
            'status' => 'todo',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Task',
                'description' => 'This is a test task description',
                'status' => 'todo',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task description',
            'status' => 'todo',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test updating a task's status to 'in_progress'.
     *
     * @return void
     */
    public function testUpdateTaskStatusToInProgress()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'status' => 'todo',
        ]);

        $response = $this->actingAs($user, 'api')->putJson("/api/tasks/{$task->id}/in-progress", [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'in_progress',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test updating a task's status to 'todo'.
     *
     * @return void
     */
    public function testUpdateTaskStatusToTodo()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($user, 'api')->putJson("/api/tasks/{$task->id}/todo");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'todo',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'todo',
        ]);
    }

    /**
     * Test updating a task's status to 'done'.
     *
     * @return void
     */
    public function testUpdateTaskStatusToDone()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($user, 'api')->putJson("/api/tasks/{$task->id}/done");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'done',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
        ]);
    }

    /**
     * Test changing a task's parent.
     *
     * @return void
     */
    public function testChangeTaskParent()
    {
        $user = User::factory()->create();
        $parentTask = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $childTask = Task::factory()->create([
            'user_id' => $user->id,
            'parent_id' => null,
        ]);

        $response = $this->actingAs($user, 'api')->putJson("/api/tasks/{$childTask->id}/change-parent", [
            'parent_id' => $parentTask->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $childTask->id,
                'parent_id' => $parentTask->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $childTask->id,
            'parent_id' => $parentTask->id,
        ]);
    }

    /**
     * Test deleting a task.
     *
     * @return void
     */
    public function testDeleteTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'api')->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
