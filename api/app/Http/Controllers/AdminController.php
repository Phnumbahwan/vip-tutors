<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="API Endpoints for admin operations"
 * )
 */
class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users-tasks",
     *     tags={"Admin"},
     *     summary="Get all users with their tasks",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users with their tasks",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="user@test.com"),
     *                 @OA\Property(
     *                     property="tasks",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Task title"),
     *                         @OA\Property(property="status", type="string", example="pending")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin access required"
     *     )
     * )
     */
    public function usersWithTasks(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $users = $this->adminService->getUsersWithTasks($perPage);

        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users-task-stats",
     *     tags={"Admin"},
     *     summary="Get task statistics for all users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Task statistics for all users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="user_name", type="string", example="John Doe"),
     *                 @OA\Property(property="total_tasks", type="integer", example=10),
     *                 @OA\Property(property="completed_tasks", type="integer", example=5),
     *                 @OA\Property(property="pending_tasks", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin access required"
     *     )
     * )
     */
    public function usersWithTaskStats(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $users = $this->adminService->getUsersWithTaskStats($perPage);

        return response()->json($users);
    }
}
