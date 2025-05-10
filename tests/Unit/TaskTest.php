<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    /** @test */
    public function it_may_belong_to_a_project()
    {
        // Task with project
        $project = Project::factory()->create();
        $taskWithProject = Task::factory()->create(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $taskWithProject->project);
        $this->assertEquals($project->id, $taskWithProject->project->id);

        // Task without project
        $taskWithoutProject = Task::factory()->create(['project_id' => null]);

        $this->assertNull($taskWithoutProject->project);
    }

    /** @test */
    public function it_has_status_scopes()
    {
        // Create tasks with different statuses
        $pendingTask = Task::factory()->create(['status' => 'pending']);
        $inProgressTask = Task::factory()->create(['status' => 'in_progress']);
        $completedTask = Task::factory()->create(['status' => 'completed']);
        $cancelledTask = Task::factory()->create(['status' => 'cancelled']);

        // Test pending scope
        $this->assertTrue(Task::pending()->get()->contains($pendingTask));
        $this->assertFalse(Task::pending()->get()->contains($completedTask));

        // Test inProgress scope
        $this->assertTrue(Task::inProgress()->get()->contains($inProgressTask));
        $this->assertFalse(Task::inProgress()->get()->contains($pendingTask));

        // Test completed scope
        $this->assertTrue(Task::completed()->get()->contains($completedTask));
        $this->assertFalse(Task::completed()->get()->contains($inProgressTask));

        // Test cancelled scope
        $this->assertTrue(Task::cancelled()->get()->contains($cancelledTask));
        $this->assertFalse(Task::cancelled()->get()->contains($completedTask));
    }

    /** @test */
    public function it_has_overdue_scope()
    {
        // Past due task
        $overdue = Task::factory()->create([
            'due_date' => now()->subDays(2),
            'status' => 'pending'
        ]);

        // Future due task
        $upcoming = Task::factory()->create([
            'due_date' => now()->addDays(2),
            'status' => 'pending'
        ]);

        // Completed task with past due date (should not be overdue)
        $completedPastDue = Task::factory()->create([
            'due_date' => now()->subDays(3),
            'status' => 'completed'
        ]);

        $this->assertTrue(Task::overdue()->get()->contains($overdue));
        $this->assertFalse(Task::overdue()->get()->contains($upcoming));
        $this->assertFalse(Task::overdue()->get()->contains($completedPastDue));
    }

    /** @test */
    public function it_has_due_today_scope()
    {
        // Due today
        $dueToday = Task::factory()->create([
            'due_date' => now()->startOfDay(),
            'status' => 'pending'
        ]);

        // Due tomorrow
        $dueTomorrow = Task::factory()->create([
            'due_date' => now()->addDay(),
            'status' => 'pending'
        ]);

        $this->assertTrue(Task::dueToday()->get()->contains($dueToday));
        $this->assertFalse(Task::dueToday()->get()->contains($dueTomorrow));
    }

    /** @test */
    public function can_check_if_is_completed()
    {
        $completedTask = Task::factory()->create(['status' => 'completed']);
        $pendingTask = Task::factory()->create(['status' => 'pending']);

        $this->assertTrue($completedTask->isCompleted());
        $this->assertFalse($pendingTask->isCompleted());
    }

    /** @test */
    public function can_check_if_is_overdue()
    {
        $overdueTask = Task::factory()->create([
            'due_date' => now()->subDay(),
            'status' => 'pending'
        ]);
        
        $futureTask = Task::factory()->create([
            'due_date' => now()->addDay(),
            'status' => 'pending'
        ]);

        $this->assertTrue($overdueTask->isOverdue());
        $this->assertFalse($futureTask->isOverdue());
    }
}
