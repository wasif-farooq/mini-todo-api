<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeTaskParentRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Support\Facades\Gate;

/**
 * TaskController handles HTTP requests related to tasks, including CRUD operations,
 * status changes, and changing the parent task. It leverages the TaskService to
 * execute business logic and uses Gates for authorization.
 */
class TaskController extends Controller
{
    /**
     * @var TaskService $taskService The service layer that handles the business logic for tasks.
     */
    protected $taskService;

    /**
     * TaskController constructor.
     *
     * @param TaskService $taskService The service that handles task-related logic.
     *
     * This constructor initializes the TaskController with an instance of TaskService.
     * The TaskService is injected via dependency injection.
     */
    public function __construct(TaskService $taskService)
    {
        // Assign the injected TaskService to the class property for use in other methods
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of all tasks.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with a collection of all tasks.
     *
     * This method retrieves all tasks using the TaskService and returns them in a JSON response.
     */
    public function index()
    {
        // Retrieve all tasks using the TaskService
        $tasks = $this->taskService->getAllTasks();

        // Return a JSON response with the tasks
        return response()->json($tasks);
    }

    /**
     * Store a newly created task.
     *
     * @param TaskRequest $request The validated request object containing task data.
     * @return \Illuminate\Http\JsonResponse A JSON response with the created task.
     *
     * This method creates a new task using the TaskService with the validated data from the request.
     * Upon successful creation, it returns a JSON response containing the task with a 201 status code.
     */
    public function store(TaskRequest $request)
    {
        // Create the task using the TaskService and get the task instance
        $task = $this->taskService->createTask($request->validated());

        // Return a JSON response with the created task, with a 201 status code
        return response()->json($task, 201);
    }

    /**
     * Display the specified task.
     *
     * @param int $id The ID of the task to be displayed.
     * @return \Illuminate\Http\JsonResponse A JSON response with the specified task.
     *
     * This method retrieves a task by its ID using the TaskService and returns it in a JSON response.
     */
    public function show($id)
    {
        // Retrieve the task by its ID using the TaskService
        $task = $this->taskService->getTask($id);

        // Return a JSON response with the task
        return response()->json($task);
    }

    /**
     * Update the specified task.
     *
     * @param TaskRequest $request The validated request object containing updated task data.
     * @param Task $task The task instance to be updated.
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated task.
     *
     * This method updates a task using the TaskService with the validated data from the request.
     * Before updating, it authorizes the action using a Gate. The updated task is returned in a JSON response.
     */
    public function update(TaskRequest $request, Task $task)
    {
        // Authorize the update action using a Gate
        Gate::authorize('update-task', $task);

        // Update the task using the TaskService and get the updated task instance
        $task = $this->taskService->updateTask($task, $request->validated());

        // Return a JSON response with the updated task
        return response()->json($task);
    }

    /**
     * Remove the specified task.
     *
     * @param Task $task The task instance to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating successful deletion.
     *
     * This method deletes a task using the TaskService. Before deleting, it authorizes the action using a Gate.
     * If authorization fails, it aborts with a 402 status code. Otherwise, it deletes the task and returns a 204 response.
     */
    public function destroy(Task $task)
    {
        // Authorize the delete action using a Gate; abort with 402 if not authorized
        if (!Gate::allows('delete-task', $task)) {
            abort(402);
        }

        // Delete the task using the TaskService
        $this->taskService->deleteTask($task);

        // Return a JSON response with a 204 status code indicating successful deletion
        return response()->json(null, 204);
    }

    /**
     * Mark the specified task as "todo".
     *
     * @param Task $task The task instance to be marked as "todo".
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated task.
     *
     * This method changes the status of a task to "todo" using the TaskService.
     * Before updating, it authorizes the action using a Gate. The updated task is returned in a JSON response.
     */
    public function markAsTodo(Task $task)
    {
        // Authorize the status change action using a Gate
        Gate::authorize('change-status-task', $task);

        // Update the task status to "todo" using the TaskService
        $task = $this->taskService->updateTask($task, ['status' => 'todo']);

        // Return a JSON response with the updated task
        return response()->json($task);
    }

    /**
     * Mark the specified task as "in_progress".
     *
     * @param Task $task The task instance to be marked as "in_progress".
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated task.
     *
     * This method changes the status of a task to "in_progress" using the TaskService.
     * Before updating, it authorizes the action using a Gate. The updated task is returned in a JSON response.
     */
    public function markAsInProgress(Task $task)
    {
        // Authorize the status change action using a Gate
        Gate::authorize('change-status-task', $task);

        // Update the task status to "in_progress" using the TaskService
        $task = $this->taskService->updateTask($task, ['status' => 'in_progress']);

        // Return a JSON response with the updated task
        return response()->json($task);
    }

    /**
     * Mark the specified task as "done".
     *
     * @param Task $task The task instance to be marked as "done".
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated task.
     *
     * This method changes the status of a task to "done" using the TaskService.
     * Before updating, it authorizes the action using a Gate. The updated task is returned in a JSON response.
     */
    public function markAsDone(Task $task)
    {
        // Authorize the status change action using a Gate
        Gate::authorize('change-status-task', $task);

        // Update the task status to "done" using the TaskService
        $task = $this->taskService->updateTask($task, ['status' => 'done']);

        // Return a JSON response with the updated task
        return response()->json($task);
    }

    /**
     * Change the parent of the specified task.
     *
     * @param ChangeTaskParentRequest $request The validated request object containing the new parent ID.
     * @param Task $task The task instance whose parent is to be changed.
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated task.
     *
     * This method changes the parent task of a given task using the TaskService.
     * Before updating, it authorizes the action using a Gate. The updated task is returned in a JSON response.
     */
    public function changeParent(ChangeTaskParentRequest $request, Task $task)
    {
        // Authorize the update action using a Gate
        Gate::authorize('update-task', $task);

        // Change the parent of the task using the TaskService and get the updated task instance
        $updatedTask = $this->taskService->changeTaskParent($task, $request->input('parent_id'));

        // Return a JSON response with the updated task
        return response()->json($updatedTask);
    }
}

