<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getUsersWithTasksPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return User::with('tasks')->paginate($perPage);
    }

    public function getUsersWithTaskStatsPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return User::withCount([
            'tasks as total_tasks',
            'tasks as completed_tasks' => fn ($query) => $query->where('status', 'completed'),
            'tasks as pending_tasks' => fn ($query) => $query->where('status', 'pending'),
        ])->paginate($perPage);
    }
}
