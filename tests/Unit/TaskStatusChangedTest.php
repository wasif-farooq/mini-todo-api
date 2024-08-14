<?php

namespace Tests\Unit;

use App\Events\TaskStatusChanged;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStatusChangedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the TaskStatusChanged event is instantiated correctly.
     *
     * @return void
     */
    public function testTaskStatusChangedEvent()
    {
        // Create a task
        $task = Task::factory()->create([
            'title' => 'Test Task',
            'status' => 'todo',
        ]);

        // Fire the event
        $event = new TaskStatusChanged($task);

        // Assert that the event contains the correct task
        $this->assertInstanceOf(Task::class, $event->task);
        $this->assertEquals($task->id, $event->task->id);
        $this->assertEquals('Test Task', $event->task->title);
        $this->assertEquals('todo', $event->task->status);
    }
}
