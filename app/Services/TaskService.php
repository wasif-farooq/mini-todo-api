<?php

namespace App\Services;

use App\Events\TaskStatusChanged;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * TaskService handles all business logic related to tasks, including creating, updating,
 * deleting, and retrieving tasks, as well as changing task parents and handling task status changes.
 */
class TaskService
{
    /**
     * Create a new task with the provided data.
     *
     * @param array $data The data for creating the task, including title, description, status, and parent_id.
     * @return Task The newly created task instance.
     *
     * This method sets the current authenticated user's ID as the owner of the task and
     * creates a new task in the database with the given data. It then loads the user and parent
     * relationships and returns the created task instance.
     */
    public function createTask(array $data): Task
    {
        // Set the user_id of the task to the ID of the currently authenticated user
        $data['user_id'] = Auth::id();

        // Create the task with the provided data and load related user and parent tasks
        return Task::create($data)->load('user', 'parent');
    }

    /**
     * Update an existing task with the provided data.
     *
     * @param Task $task The task to be updated.
     * @param array $data The data to update the task with, including possible status change.
     * @return Task The updated task instance.
     *
     * This method updates the task with the provided data and checks for any status changes.
     * If the status is being changed to 'in_progress', 'done', or 'todo', it validates that
     * all subtasks meet the necessary conditions before allowing the status change.
     * If the status is set to 'in_progress', a TaskStatusChanged event is fired.
     */
    public function updateTask(Task $task, array $data): Task
    {
        // Store the original status of the task for comparison
        $originalStatus = $task->status;

        // Initialize an array to track affected tasks (starts with the current task)
        $affectedTasks = [$task];

        // Check if status is changing to 'in_progress' and ensure all subtasks are also in progress
        if (isset($data['status']) && $data['status'] === 'in_progress' && !$task->allSubTasksInProgress()) {
            throw ValidationException::withMessages([
                'status' => 'All subtasks must be in progress before setting the task to in progress.',
            ]);
        }

        // Check if status is changing to 'done' and ensure all subtasks are also done
        if (isset($data['status']) && $data['status'] === 'done' && !$task->allSubTasksDone()) {
            throw ValidationException::withMessages([
                'status' => 'All subtasks must be done before setting the task to done.',
            ]);
        }

        // Check if status is changing to 'todo' and ensure all subtasks are also in todo status
        if (isset($data['status']) && $data['status'] === 'todo' && !$task->allSubTasksTodo()) {
            throw ValidationException::withMessages([
                'status' => 'All subtasks must be in todo before setting the task to todo.',
            ]);
        }

        // Update the task with the provided data
        $task->update($data);

        // If the status is set to 'in_progress', fire the TaskStatusChanged event
        if ($data['status'] === 'in_progress') {
            event(new TaskStatusChanged($task));
        }

        // Reload the task's user and parent relationships
        $task->load('user', 'parent');

        // Return the updated task instance
        return $task;
    }

    /**
     * Delete a specified task.
     *
     * @param Task $task The task to be deleted.
     *
     * This method deletes the given task from the database. It does not return any value.
     */
    public function deleteTask(Task $task): void
    {
        // Delete the specified task
        $task->delete();
    }

    /**
     * Retrieve a task by its ID.
     *
     * @param int $id The ID of the task to be retrieved.
     * @return Task The task instance with the specified ID, along with its user and parent relationships.
     *
     * This method fetches a task by its ID from the database, including the related user and parent task.
     * If the task is not found, an exception is thrown.
     */
    public function getTask(int $id): Task
    {
        // Find the task by its ID and load the user and parent relationships
        return Task::with('user', 'parent')->findOrFail($id);
    }

    /**
     * Retrieve all tasks.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of all tasks, each with its user and parent relationships.
     *
     * This method fetches all tasks from the database, including the related user and parent task for each task.
     */
    public function getAllTasks()
    {
        // Retrieve all tasks and load the user and parent relationships
        return Task::with('user', 'parent')->get();
    }

    /**
     * Change the parent of a specified task.
     *
     * @param Task $task The task whose parent is to be changed.
     * @param int|null $parentId The ID of the new parent task, or null to remove the parent.
     * @return Task The task instance with the updated parent relationship.
     *
     * This method updates the parent_id of the given task to the specified parent ID.
     * If a null parent ID is provided, the task's parent relationship is removed.
     * The updated task instance is returned.
     */
    public function changeTaskParent(Task $task, ?int $parentId): Task
    {
        // Set the parent_id of the task to the provided parent ID (or null)
        $task->parent_id = $parentId;

        // Save the changes to the database
        $task->save();

        // Return the task instance with the updated parent relationship
        return $task;
    }
}

