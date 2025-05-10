<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function project_owner_can_create_task_in_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('projects.tasks.store', $project), [
            'title' => 'Project Task',
            'description' => 'This is a project task',
            'status' => 'pending',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Project Task',
            'description' => 'This is a project task',
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function project_member_can_create_task_in_project()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);
        
        // Add member to project
        $project->members()->attach($member);

        $response = $this->actingAs($member)->post(route('projects.tasks.store', $project), [
            'title' => 'Member Task',
            'description' => 'This is a task created by member',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Member Task',
            'project_id' => $project->id,
            'user_id' => $member->id,
        ]);
    }

    /** @test */
    public function non_member_cannot_create_task_in_project()
    {
        $owner = User::factory()->create();
        $nonMember = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($nonMember)->post(route('projects.tasks.store', $project), [
            'title' => 'Unauthorized Task',
            'description' => 'This task should not be created',
            'status' => 'pending',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Unauthorized Task',
            'project_id' => $project->id,
        ]);
    }

    /** @test */
    public function project_owner_can_view_all_project_tasks()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);
        
        // Add member to project
        $project->members()->attach($member);
        
        // Create owner's task
        $ownerTask = Task::factory()->create([
            'title' => 'Owner Task',
            'user_id' => $owner->id,
            'project_id' => $project->id,
        ]);
        
        // Create member's task
        $memberTask = Task::factory()->create([
            'title' => 'Member Task',
            'user_id' => $member->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($owner)->get(route('projects.show', $project));
        
        $response->assertStatus(200);
        $response->assertSee('Owner Task');
        $response->assertSee('Member Task');
    }

    /** @test */
    public function project_member_can_view_all_project_tasks()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);
        
        // Add member to project
        $project->members()->attach($member);
        
        // Create tasks
        $ownerTask = Task::factory()->create([
            'title' => 'Owner Task',
            'user_id' => $owner->id,
            'project_id' => $project->id,
        ]);
        
        $memberTask = Task::factory()->create([
            'title' => 'Member Task',
            'user_id' => $member->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($member)->get(route('projects.show', $project));
        
        $response->assertStatus(200);
        $response->assertSee('Owner Task');
        $response->assertSee('Member Task');
    }

    /** @test */
    public function task_creator_can_update_their_project_task()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);
        
        // Add member to project
        $project->members()->attach($member);
        
        // Create member's task
        $task = Task::factory()->create([
            'title' => 'Original Task Title',
            'user_id' => $member->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($member)->put(route('projects.tasks.update', [$project, $task]), [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'status' => 'in_progress',
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'in_progress',
        ]);
    }

    /** @test */
    public function project_owner_can_update_any_task_in_their_project()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);
        
        // Add member to project
        $project->members()->attach($member);
        
        // Create member's task
        $task = Task::factory()->create([
            'title' => 'Member Task',
            'user_id' => $member->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($owner)->put(route('projects.tasks.update', [$project, $task]), [
            'title' => 'Owner Updated Task',
            'description' => 'The owner updated this task',
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Owner Updated Task',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function project_member_cannot_update_others_tasks()
    {
        $owner = User::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);
        
        // Add members to project
        $project->members()->attach([$member1->id, $member2->id]);
        
        // Create member1's task
        $task = Task::factory()->create([
            'title' => 'Member 1 Task',
            'user_id' => $member1->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($member2)->put(route('projects.tasks.update', [$project, $task]), [
            'title' => 'Unauthorized Update',
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'Unauthorized Update',
        ]);
    }
}
