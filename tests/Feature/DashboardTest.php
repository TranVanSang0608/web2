<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_view_dashboard()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Bảng điều khiển');
    }

    /** @test */
    public function dashboard_shows_user_tasks()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Personal Task'
        ]);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Personal Task');
    }

    /** @test */
    public function dashboard_shows_user_projects()
    {
        $user = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Project'
        ]);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('My Project');
    }

    /** @test */
    public function dashboard_shows_projects_user_is_member_of()
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
            'name' => 'Shared Project'
        ]);
        
        // Add user as project member
        $project->members()->attach($user);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Shared Project');
    }

    /** @test */
    public function dashboard_does_not_show_other_users_content()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        
        // Create another user's task
        $otherTask = Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Task'
        ]);
        
        // Create another user's project
        $otherProject = Project::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Project'
        ]);
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertDontSee('Other User Task');
        $response->assertDontSee('Other User Project');
    }
}
