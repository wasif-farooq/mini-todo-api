<?php

namespace Tests\Unit;

use App\Jobs\SendTaskReminderEmail;
use App\Mail\TaskReminderEmail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendTaskReminderEmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the SendTaskReminderEmail job sends an email.
     *
     * @return void
     */
    public function testJobSendsTaskReminderEmail()
    {
        // Fake the Mail facade to prevent actual emails from being sent
        Mail::fake();

        // Create a user and a task
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
        ]);

        $task = Task::factory()->create([
            'title' => 'Test Task',
            'status' => 'in_progress',
            'user_id' => $user->id,
        ]);

        // Dispatch the SendTaskReminderEmail job
        $job = new SendTaskReminderEmail($task);
        $job->handle();

        // Assert that an email was sent to the task owner
        Mail::assertSent(TaskReminderEmail::class, function ($mail) use ($task) {
            return $mail->hasTo($task->user->email) && $mail->task->is($task);
        });
    }
}
