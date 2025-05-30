<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getUsersWithTasksPaginated(int $perPage = 10): LengthAwarePaginator;
}
