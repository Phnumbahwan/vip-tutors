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