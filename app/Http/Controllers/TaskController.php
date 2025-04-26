<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReorderTaskRequest;
use App\Http\Requests\TaskFilterRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{

    public function __construct(private TaskService $taskService) {}

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $userId = Auth::user();
        $tasks = $this->taskService->getAllTasks($userId);

        return response()->json($tasks);
    }

    /**
     * Display a listing of the search resource.
     * @param TaskFilterRequest $request
     *
     * @return JsonResponse
     */
    public function search(TaskFilterRequest $request): JsonResponse
    {
        $userId = Auth::user();
        $tasks = $this->taskService->getFilteredTasks($request->validated(), $userId);

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     * @param TaskRequest $request
     *
     * @return JsonResponse
     */
    public function store(TaskRequest $request): JsonResponse
    {
        $task = $this->taskService->saveTask([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Task saved successfully',
            $task
        ]);
    }

    /**
     * Display the specified resource.
     * @param string $taskId
     * @return JsonResponse
     *
     * @throws ModelNotFoundException
     */
    public function show(string $id): JsonResponse
    {
        try {
            $task = $this->taskService->getTask($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e,
            ], 404);
        }

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTaskRequest $request
     * @param string $id
     *
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $this->taskService->updateTask($request->validated(), $id);

        return response()->json([
            'message' => 'Task updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return JsonResponse
     *
     * @throws ModelNotFoundException
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->taskService->delete($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e,
            ], 404);
        }

        return response()->json(null, 204);
    }

    /**
     * @param ReorderTaskRequest $request
     *
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function reorder(ReorderTaskRequest $request)
    {
        $taskId = $request->validated()['task_id'];
        $newOrder = $request->validated()['order'];
        $user = Auth::user();

        try {
            $result = $this->taskService->reorderTask($taskId, $newOrder, $user->id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Failed to reorder task.'], 500);
        }

        if ($result) {
            return response()->json(
                [
                    'message' => 'Task reordered successfully.',
                    $result
                ],
                200
            );
        }
    }
}
