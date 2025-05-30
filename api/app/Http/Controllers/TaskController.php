<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    public function index()
    {
        return response()->json($this->taskService->getAll());
    }

    public function store(TaskRequest $request)
    {
        return response()->json($this->taskService->create($request->validated()));
    }

    public function update(TaskRequest $request, $id)
    {
        return response()->json($this->taskService->update($id, $request->validated()));
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->taskService->delete($id)]);
    }

    public function reorder(ReorderTasksRequest $request)
    {
        $this->taskService->reorder($request->validated()['order']);

        return response()->json(['success' => true]);
    }
}
