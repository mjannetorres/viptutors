<?php

namespace App\Jobs;

use App\Models\Task;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteOldTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $thresholdDate = CarbonImmutable::now()->subDays(30);
        $oldTasks = Task::where('created_at', '<', $thresholdDate)->get();

        $deletedCount = 0;

        foreach ($oldTasks as $task) {
            Log::info("Deleting Task ID {$task->id} (Title: {$task->title}) created at {$task->created_at}");
            $task->delete();
            $deletedCount++;
        }

        Log::info("Deleted {$deletedCount} old tasks.");
    }
}
