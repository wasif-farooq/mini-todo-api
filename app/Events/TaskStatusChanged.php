<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * TaskStatusChanged event is triggered whenever the status of a task changes.
 * This event can be listened to by any event listeners to perform actions when a task's status is updated.
 */
class TaskStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Task $task The task instance whose status has changed.
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param Task $task The task instance whose status has changed.
     *
     * This constructor initializes the TaskStatusChanged event with the task instance.
     * The task is passed to any event listeners that handle this event.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        // Assign the provided task instance to the class property
        $this->task = $task;
    }
}

