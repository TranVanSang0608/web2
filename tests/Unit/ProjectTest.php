<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $project->user);
        $this->assertEquals($user->id, $project->user->id);
    }

    /** @test */
    public function it_has_many_tasks()
    {
        $project = Project::factory()->create();
        $task1 = Task::factory()->create(['project_id' => $project->id]);
        $task2 = Task::factory()->create(['project_id' => $project->id]);

        $this->assertCount(2, $project->tasks);
        $this->assertTrue($project->tasks->contains($task1));
        $this->assertTrue($project->tasks->contains($task2));
    }

    /** @test */
    public function it_has_many_members()
    {
        $project = Project::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();

        // Add members to project
        $project->members()->attach([$member1->id, $member2->id]);

        $this->assertCount(2, $project->members);
        $this->assertTrue($project->members->contains($member1));
        $this->assertTrue($project->members->contains($member2));
    }

    /** @test */
    public function it_can_check_if_user_is_a_member()
    {
        $project = Project::factory()->create();
        $member = User::factory()->create();
        $nonMember = User::factory()->create();

        // Add member to project
        $project->members()->attach($member);

        $this->assertTrue($project->hasMember($member));
        $this->assertFalse($project->hasMember($nonMember));
    }

    /** @test */
    public function owner_is_always_considered_a_member()
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue($project->hasMember($owner));
    }

    /** @test */
    public function it_has_tasks_with_specific_status()
    {
        $project = Project::factory()->create();
        
        $pendingTask = Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'pending'
        ]);
        
        $completedTask = Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'completed'
        ]);
        
        $inProgressTask = Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'in_progress'
        ]);

        $this->assertCount(1, $project->pendingTasks);
        $this->assertCount(1, $project->completedTasks);
        $this->assertCount(1, $project->inProgressTasks);
        
        $this->assertTrue($project->pendingTasks->contains($pendingTask));
        $this->assertTrue($project->completedTasks->contains($completedTask));
        $this->assertTrue($project->inProgressTasks->contains($inProgressTask));
    }

    /** @test */
    public function it_can_calculate_completion_percentage()
    {
        $project = Project::factory()->create();
        
        // Create 3 completed tasks and 1 pending task
        Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'status' => 'completed'
        ]);
        
        Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'pending'
        ]);
        
        // 3 out of 4 tasks completed = 75%
        $this->assertEquals(75, $project->completionPercentage());
        
        // Project with no tasks should return 0%
        $emptyProject = Project::factory()->create();
        $this->assertEquals(0, $emptyProject->completionPercentage());
    }
}
