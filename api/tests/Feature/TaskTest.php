<?php

use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

test('user can list their tasks', function () {
    Task::factory()->count(3)->create([
        'user_id' => $this->user->id
    ]);

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'description',
                'status',
                'priority',
                'order',
                'user_id',
                'created_at',
                'updated_at'
            ]
        ]);
});

it('updates a task successfully', function () {
    $user = $this->user;

    Sanctum::actingAs($user);

    $task = Task::factory()->create([
        'user_id' => $user->id,
        'title' => 'Old Title',
    ]);

    $payload = [
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'status' => 'completed',
        'priority' => 'high',
        'order' => 2,
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $payload);

    $response->assertOk()
        ->assertJsonFragment([
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'status' => 'completed',
            'priority' => 'high',
            'order' => 2,
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'status' => 'completed',
        'priority' => 'high',
        'order' => 2,
        'user_id' => $user->id,
    ]);
});