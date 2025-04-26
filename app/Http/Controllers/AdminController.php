<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    /**
     * Display all users and their tasks with statistics.
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        $usersWithTasks = $this->taskService->getUsersWithTaskStats();

        return response()->json([
            'completed_tasks_count' => $usersWithTasks['completed_tasks_count'],
            'pending_tasks_count' => $usersWithTasks['pending_tasks_count'],
        ]);
    }
}
