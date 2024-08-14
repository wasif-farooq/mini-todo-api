<?php

namespace App\Listeners;

use App\Events\TaskStatusChanged;
use App\Jobs\SendTaskReminderEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * HandleTaskStatusChanged listens for the TaskStatusChanged event and performs actions based on the new status of the task.
 * If the task is marked as 'in_progress', it schedules a reminder email to be sent after a delay.
 */
class HandleTaskStatusChanged implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param TaskStatusChanged $event The event instance containing the task whose status has changed.
     *
     * This method handles the TaskStatusChanged event. If the task status is changed to 'in_progress',
     * it dispatches a job to send a reminder email. The email is scheduled to be sent after a delay of one minute.
     *
     * @return void
     */
    public function handle(TaskStatusChanged $event)
    {
        // If the task status is 'in_progress', schedule the reminder email to be sent after a delay
        if ($event->task->status === 'in_progress') {
            // Dispatch the SendTaskReminderEmail job with a delay of one minute
            SendTaskReminderEmail::dispatch($event->task)->delay(now()->addMinute());
        }
    }
}

