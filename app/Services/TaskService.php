<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TaskService
{
    /**
     * @param null|int $userId
     *
     * @return Collection
     */
    public function getAllTasks(User $user): Collection
    {
        $cacheKey = $this->generateCacheKey($user->id);

        return Cache::remember($cacheKey, now()->addMinutes(1), function () use ($user) {
            $query = Task::with(['user']);

            // Filter by user if specified
            if (!$user->is_admin) {
                $query->where('user_id', $user->id);
            }
            return $query->orderBy('order')->get();
        });
    }

    /**
     * @param array $filters
     * @param null|int $userId
     *
     * @return Collection
     */
    public function getFilteredTasks(array $filters,  User $user): Collection
    {
        $query = Task::with(['user'])
            ->when($filters['query'], function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });

        // Filter by user if specified
        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        if (!empty($filters['status'])) {
            $query->status($filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->priority($filters['priority']);
        }

        return $query->orderBy('order')->get();
    }

    /**
     * @param array $filters
     * @param null|int $userId
     *
     * @return string
     */
    protected function generateCacheKey(int $userId): string
    {
        $user = $userId ?? 'all';

        return "tasks:user:{$user}";
    }

    /**
     * @param string $taskId
     * @return Task
     *
     * @throws ModelNotFoundException
     */
    public function getTask(string $taskId): Task
    {
        return Task::findOrFail($taskId);
    }

    /**
     * @param array $task
     * @return Task
     *
     */
    public function saveTask(array $taskData): Task
    {
        $nextOrder = Task::where('user_id', $taskData['user_id'])->count() + 1;

        $taskData['order'] = $nextOrder;

        return Task::create($taskData);
    }

    /**
     * @param array $data
     * @param string $taskId
     *
     * @return bool
     */
    public function updateTask(array $data, string $taskId): bool
    {
        $task = Task::where('id', $taskId)->update($data);

        return $task;
    }

    /**
     * @param string $taskId
     * @return void
     *
     * @throws ModelNotFoundException
     */
    public function delete(string $taskId): void
    {
        $task = Task::findOrFail($taskId);

        $task->delete();
    }

    /**
     * @param int $taskId
     * @param int $newOrder
     *
     * @return Task
     */
    public function reorderTask(int $taskId, int $newOrder, int $userId): Task
    {
        return DB::transaction(function () use ($taskId, $newOrder, $userId) {
            $task = Task::where('id', $taskId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $oldOrder = $task->order;

            if ($newOrder === $oldOrder) {
                return $task;
            }

            if ($newOrder < $oldOrder) {
                // Shift tasks between newOrder and oldOrder up by 1
                Task::where('user_id', $userId)
                    ->whereBetween('order', [$newOrder, $oldOrder - 1])
                    ->increment('order');
            } else {
                // Shift tasks between oldOrder and newOrder down by 1
                Task::where('user_id', $userId)
                    ->whereBetween('order', [$oldOrder + 1, $newOrder])
                    ->decrement('order');
            }

            // Update the task's order
            $task->update(['order' => $newOrder]);

            return $task;
        });
    }

    /**
     * Get all users with their tasks and task statistics.
     *
     *
     */
    public function getUsersWithTaskStats(): array
    {
        // Retrieve users with tasks count, completed count, and pending count
        $completedCount = Task::where('status', 'completed')->count();
        $pendingCount = Task::where('status', 'pending')->count();

        return [
            'completed_tasks_count' => $completedCount,
            'pending_tasks_count' => $pendingCount,
        ];
    }
}
