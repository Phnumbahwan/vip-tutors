<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'user']);
});

it('denies access to non-authenticated users', function () {
    $this->getJson('/api/admin/users-task-stats')
        ->assertUnauthorized();
});

it('denies access to non-admin users', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/users-task-stats')
        ->assertForbidden();
});

it('returns users with task statistics for admin users', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user1 = User::factory()->create(['name' => 'User One']);
    $user1->assignRole('user');

    $user2 = User::factory()->create(['name' => 'User Two']);
    $user2->assignRole('user');

    // Add tasks
    Task::factory()->count(3)->create(['user_id' => $user1->id, 'status' => 'completed']);
    Task::factory()->count(2)->create(['user_id' => $user1->id, 'status' => 'pending']);
    Task::factory()->count(1)->create(['user_id' => $user2->id, 'status' => 'pending']);

    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/admin/users-task-stats');

    $response->assertOk()
             ->assertJsonStructure([
                 'data' => [
                     '*' => ['id', 'name', 'email', 'total_tasks', 'completed_tasks', 'pending_tasks']
                 ]
             ]);

    // Assert correct counts
    $responseData = $response->json('data');

    $userOneStats = collect($responseData)->firstWhere('id', $user1->id);
    expect($userOneStats['total_tasks'])->toBe(5);
    expect($userOneStats['completed_tasks'])->toBe(3);
    expect($userOneStats['pending_tasks'])->toBe(2);

    $userTwoStats = collect($responseData)->firstWhere('id', $user2->id);
    expect($userTwoStats['total_tasks'])->toBe(1);
    expect($userTwoStats['completed_tasks'])->toBe(0);
    expect($userTwoStats['pending_tasks'])->toBe(1);
});
