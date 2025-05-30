<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class TaskRepository implements TaskRepositoryInterface
{
    public function all() {
        return Cache::remember('tasks', 60, fn() => Task::orderBy('order')->get());
    }

    public function find($id) {
        return Task::findOrFail($id);
    }

    public function create(array $data) {
        Cache::forget('tasks');

        $data['user_id'] = Auth::id();

        $lastOrder = Task::where('user_id', Auth::id())
            ->max('order');
        $data['order'] = $lastOrder ? $lastOrder + 1 : 1;

        return Task::create($data);
    }

    public function update($id, array $data) {
        Cache::forget('tasks');
        $task = Task::findOrFail($id);
        $task->update($data);
        return $task;
    }

    public function delete($id) {
        Cache::forget('tasks');
        $task = Task::findOrFail($id);
        return $task->delete();
    }

    public function reorder(array $orderData) {
        Cache::forget('tasks');
        foreach ($orderData as $order => $id) {
            Task::where('id', $id)->update(['order' => $order]);
        }
    }
}