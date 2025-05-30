<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsersWithTasks(int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getUsersWithTasksPaginated($perPage);
    }

    public function getUsersWithTaskStats(int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getUsersWithTaskStatsPaginated($perPage);
    }
}
