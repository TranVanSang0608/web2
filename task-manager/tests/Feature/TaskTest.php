<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_tasks()
    {
        $response = $this->get('/tasks');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_create_a_task()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_see_their_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'My Personal Task',
        ]);
        
        $otherUserTask = Task::factory()->create([
            'title' => 'Someone Else Task',
        ]);

        $response = $this->actingAs($user)->get('/tasks');
        
        $response->assertStatus(200);
        $response->assertSee('My Personal Task');
        $response->assertDontSee('Someone Else Task');
    }

    /** @test */
    public function user_can_update_their_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->put("/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $response->assertRedirect("/tasks");
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function user_cannot_update_others_task()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        
        $task = Task::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)->put("/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'status' => 'in_progress',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_their_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        $response->assertRedirect("/tasks");
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_task()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        
        $task = Task::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function task_requires_a_title()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'description' => 'This is a test task',
            'status' => 'pending',
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function task_requires_a_valid_status()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }
}
