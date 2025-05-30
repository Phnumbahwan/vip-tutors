<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupOldTasks extends Command
{
    protected $signature = 'tasks:cleanup-old';
    protected $description = 'Delete tasks older than 30 days and log the deletions';

    public function handle(): int
    {
        $cutoffDate = Carbon::now()->subDays(30);

        $oldTasks = Task::where('created_at', '<', $cutoffDate)->get();

        $deletedCount = $oldTasks->count();

        if ($deletedCount === 0) {
            info('No old tasks to delete.');
            return 0;
        }

        foreach ($oldTasks as $task) {
            info("Deleting task ID: {$task->id}, Title: {$task->title}, Created at: {$task->created_at}");
            $task->delete();
        }

        info("Deleted {$deletedCount} tasks older than 30 days.");

        return 0;
    }
}
