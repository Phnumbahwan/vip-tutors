<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

// Helper to create user and tasks
function setupUserWithTasks(): array {
    $user = User::factory()->create();
    $tasks = [
        Task::factory()->create(['user_id' => $user->id, 'order' => 1]),
        Task::factory()->create(['user_id' => $user->id, 'order' => 2]),
        Task::factory()->create(['user_id' => $user->id, 'order' => 3]),
        Task::factory()->create(['user_id' => $user->id, 'order' => 4]),
    ];
    return [$user, $tasks];
}

test('it can reorder tasks without duplicate order values', function () {
    [$user, $tasks] = setupUserWithTasks();

    $taskIds = array_reverse(array_map(fn($task) => $task->id, $tasks));

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/tasks/reorder', [
            'taskIds' => $taskIds
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $updatedTasks = Task::whereIn('id', $taskIds)->orderBy('order')->get();

    expect($updatedTasks)->toHaveCount(count($taskIds));

    $orderValues = $updatedTasks->pluck('order')->toArray();
    expect($orderValues)->toBe(range(1, count($taskIds)));

    foreach ($updatedTasks as $index => $task) {
        expect($task->id)->toBe($taskIds[$index]);
    }
});

test('it validates required taskIds', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/tasks/reorder', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['taskIds']);
});

test('it validates taskIds are integers', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/tasks/reorder', [
            'taskIds' => ['not-an-integer']
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['taskIds.0']);
});

test('it validates all tasks belong to user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownTask = Task::factory()->create(['user_id' => $user->id]);
    $otherTask = Task::factory()->create(['user_id' => $otherUser->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/tasks/reorder', [
            'taskIds' => [$ownTask->id, $otherTask->id]
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['taskIds.1']);
});

test('it requires authentication', function () {
    $response = $this->postJson('/api/tasks/reorder', [
        'taskIds' => [1, 2, 3]
    ]);

    $response->assertStatus(401);
});
