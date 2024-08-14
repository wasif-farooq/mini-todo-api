<?php

namespace Tests\Unit;

use App\Events\TaskStatusChanged;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $taskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskService = app(TaskService::class); // Use dependency injection for the service
    }

    /**
     * Test the creation of a task.
     *
     * @return void
     */
    public function testCreateTask()
    {
        $user = User::factory()->create();
        $this->be($user);

        $data = [
            'title' => 'Test Task',
            'description' => 'This is a test task description',
            'status' => 'todo',
        ];

        $task = $this->taskService->createTask($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task description',
            'status' => 'todo',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test updating a task and triggering the TaskStatusChanged event.
     *
     * @return void
     */
    public function testUpdateTaskAndFireEvent()
    {
        $user = User::factory()->create();
        $this->be($user);

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'status' => 'todo',
        ]);

        Event::fake(); // Fake the event for testing

        $data = ['status' => 'in_progress'];
        $updatedTask = $this->taskService->updateTask($task, $data);

        $this->assertInstanceOf(Task::class, $updatedTask);
        $this->assertEquals('in_progress', $updatedTask->status);

        // Assert that the event was dispatched
        Event::assertDispatched(TaskStatusChanged::class, function ($event) use ($task) {
            return $event->task->id === $task->id;
        });
    }

    /**
     * Test updating a task's status to 'done' without all subtasks being done.
     *
     * @return void
     */
    public function testUpdateTaskToDoneWithIncompleteSubtasks()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $user = User::factory()->create();
        $this->be($user);

        $parentTask = Task::factory()->create([
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        $subTask = Task::factory()->create([
            'user_id' => $user->id,
            'parent_id' => $parentTask->id,
            'status' => 'in_progress', // Not 'done'
        ]);

        $data = ['status' => 'done'];
        $this->taskService->updateTask($parentTask, $data);
    }

    /**
     * Test deleting a task.
     *
     * @return void
     */
    public function testDeleteTask()
    {
        $user = User::factory()->create();
        $this->be($user);

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->taskService->deleteTask($task);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test getting a task by ID.
     *
     * @return void
     */
    public function testGetTask()
    {
        $user = User::factory()->create();
        $this->be($user);

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $fetchedTask = $this->taskService->getTask($task->id);

        $this->assertInstanceOf(Task::class, $fetchedTask);
        $this->assertEquals($task->id, $fetchedTask->id);
    }

    /**
     * Test getting all tasks.
     *
     * @return void
     */
    public function testGetAllTasks()
    {
        $user = User::factory()->create();
        $this->be($user);

        Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $tasks = $this->taskService->getAllTasks();

        $this->assertCount(3, $tasks);
    }

    /**
     * Test changing the parent of a task.
     *
     * @return void
     */
    public function testChangeTaskParent()
    {
        $user = User::factory()->create();
        $this->be($user);

        $parentTask = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $childTask = Task::factory()->create([
            'user_id' => $user->id,
            'parent_id' => null,
        ]);

        $updatedTask = $this->taskService->changeTaskParent($childTask, $parentTask->id);

        $this->assertInstanceOf(Task::class, $updatedTask);
        $this->assertEquals($parentTask->id, $updatedTask->parent_id);
    }
}
