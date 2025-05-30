<?php

use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can list their tasks', function () {
    $user = $this->user;
    Sanctum::actingAs($user);

    Task::factory()->count(3)->create([
        'user_id' => $user->id
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

it('creates a task successfully', function () {
    $user = $user = $this->user;

    Sanctum::actingAs($user);

    $payload = [
        'title' => 'New Task',
        'description' => 'Description here',
        'status' => 'pending',
        'priority' => 'medium',
        'order' => 1,
    ];

    $response = $this->postJson('/api/tasks', $payload);

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'title' => 'New Task',
                 'status' => 'pending',
                 'priority' => 'medium',
             ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Task',
        'user_id' => $user->id,
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

it('deletes a task successfully', function () {
    $user = $this->user;
    Sanctum::actingAs($user);

    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

it('reorders tasks successfully using UUIDs', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $tasks = Task::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $newOrder = $tasks->pluck('id')->shuffle()->values()->toArray();

    $response = $this->postJson('/api/tasks/reorder', [
        'order' => $newOrder,
    ]);

    $response->assertStatus(200)
             ->assertJson(['success' => true]);

    foreach ($newOrder as $position => $uuid) {
        $this->assertDatabaseHas('tasks', [
            'id' => $uuid,
            'order' => $position,
            'user_id' => $user->id,
        ]);
    }
});

it('prevents unauthenticated task creation', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => 'Unauthorized Task',
        'status' => 'pending',
        'priority' => 'low',
        'order' => 1,
    ]);

    $response->assertStatus(401);
});