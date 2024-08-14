<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Task model represents a task within the application, which can be a parent task or a subtask.
 * It includes relationships to its parent task, subtasks, and the user to whom the task belongs.
 */
class Task extends Model
{
    use HasFactory;

    /**
     * @var array $fillable An array of attributes that are mass assignable.
     *
     * 'title'       - The title of the task, a brief summary of the task.
     * 'description' - A more detailed explanation of what the task involves.
     * 'status'      - The current status of the task, which can be 'todo', 'in_progress', or 'done'.
     * 'parent_id'   - The ID of the parent task, if this task is a subtask. Otherwise, it is null.
     * 'user_id'     - The ID of the user who owns the task.
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'parent_id',
        'user_id',
    ];

    /**
     * Define a relationship to the parent task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * This method establishes a many-to-one relationship where a task may have a parent task.
     * The parent task is another Task model instance identified by 'parent_id'.
     */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    /**
     * Define a relationship to the subtasks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * This method establishes a one-to-many relationship where a task may have multiple subtasks.
     * These subtasks are other Task model instances that reference this task as their parent, identified by 'parent_id'.
     */
    public function subTasks()
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }

    /**
     * Define a relationship to the user who owns the task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * This method establishes a many-to-one relationship where a task belongs to a specific user.
     * The user is represented by the User model instance, identified by 'user_id'.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if all subtasks are in 'in_progress' status.
     *
     * @return bool
     *
     * This method verifies whether all subtasks of the current task are in the 'in_progress' status.
     * It returns true if none of the subtasks have a status other than 'in_progress', indicating that all subtasks
     * are actively being worked on. Otherwise, it returns false.
     */
    public function allSubTasksInProgress(): bool
    {
        return $this->subTasks()->where('status', '<>', 'in_progress')->doesntExist();
    }

    /**
     * Check if all subtasks are in 'done' status.
     *
     * @return bool
     *
     * This method checks whether all subtasks of the current task are marked as 'done'.
     * It returns true if all subtasks have completed their work and are in the 'done' status.
     * If any subtask is not 'done', the method returns false.
     */
    public function allSubTasksDone(): bool
    {
        return $this->subTasks()->where('status', '<>', 'done')->doesntExist();
    }

    /**
     * Check if all subtasks are in 'todo' status.
     *
     * @return bool
     *
     * This method determines if all subtasks of the current task are in the 'todo' status.
     * It returns true if none of the subtasks have been started yet, meaning all subtasks are still
     * in the 'todo' phase. Otherwise, it returns false.
     */
    public function allSubTasksTodo(): bool
    {
        return $this->subTasks()->where('status', '<>', 'todo')->doesntExist();
    }
}

