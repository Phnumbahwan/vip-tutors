<?php

namespace App\Services;

use App\Repositories\Interfaces\TaskRepositoryInterface;

class TaskService
{
    public function __construct(protected TaskRepositoryInterface $taskRepo) {}

    public function getAll()
    {
        return $this->taskRepo->all();
    }

    public function create(array $data)
    {
        return $this->taskRepo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->taskRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->taskRepo->delete($id);
    }

    public function reorder(array $data)
    {
        return $this->taskRepo->reorder($data);
    }
}
