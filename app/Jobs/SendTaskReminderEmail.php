<?php

namespace App\Jobs;

use App\Mail\TaskReminderEmail;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * SendTaskReminderEmail is a queued job responsible for sending a reminder email to the owner of a task.
 * The job is queued to ensure that the email is sent asynchronously, without blocking the main application flow.
 */
class SendTaskReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Task $task The task instance for which the reminder email is being sent.
     */
    protected $task;

    /**
     * Create a new job instance.
     *
     * @param Task $task The task instance to be used in the reminder email.
     *
     * This constructor initializes the SendTaskReminderEmail job with the task instance.
     * The task is used to populate the content of the reminder email.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        // Assign the provided task instance to the class property
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * This method handles the logic for sending the reminder email to the task owner.
     * It uses the Mail facade to send the TaskReminderEmail to the email address associated with the task's owner.
     *
     * @return void
     */
    public function handle()
    {
        // Send the reminder email to the task owner using the Mail facade
        Mail::to($this->task->user->email)->send(new TaskReminderEmail($this->task));
    }
}

