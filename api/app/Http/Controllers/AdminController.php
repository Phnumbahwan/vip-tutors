<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function usersWithTasks(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $users = $this->adminService->getUsersWithTasks($perPage);

        return response()->json($users);
    }

    public function usersWithTaskStats(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $users = $this->adminService->getUsersWithTaskStats($perPage);

        return response()->json($users);
    }
}
