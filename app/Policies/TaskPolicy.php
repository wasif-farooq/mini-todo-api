<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

/**
 * TaskPolicy defines the authorization logic for actions on tasks,
 * ensuring that only the owner of the task can perform certain actions.
 */
class TaskPolicy
{
    /**
     * Determine if the given task can be updated by the user.
     *
     * @param User $user The user attempting to perform the update action.
     * @param Task $task The task instance that the user wants to update.
     * @return bool True if the user is the owner of the task, false otherwise.
     *
     * This method checks if the authenticated user is the owner of the task.
     * Only the user who created the task (i.e., the task owner) is authorized to update it.
     */
    public function update(User $user, Task $task): bool
    {
        // Authorize the update action if the user is the owner of the task
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the given task can be deleted by the user.
     *
     * @param User $user The user attempting to perform the delete action.
     * @param Task $task The task instance that the user wants to delete.
     * @return bool True if the user is the owner of the task, false otherwise.
     *
     * This method checks if the authenticated user is the owner of the task.
     * Only the user who created the task (i.e., the task owner) is authorized to delete it.
     */
    public function delete(User $user, Task $task): bool
    {
        // Authorize the delete action if the user is the owner of the task
        return $user->id === $task->user_id;
    }

    /**
     * Determine if the given task's status can be changed by the user.
     *
     * @param User $user The user attempting to change the task's status.
     * @param Task $task The task instance whose status the user wants to change.
     * @return bool True if the user is the owner of the task, false otherwise.
     *
     * This method checks if the authenticated user is the owner of the task.
     * Only the user who created the task (i.e., the task owner) is authorized to change its status.
     */
    public function changeStatus(User $user, Task $task): bool
    {
        // Authorize the status change action if the user is the owner of the task
        return $user->id === $task->user_id;
    }
}

