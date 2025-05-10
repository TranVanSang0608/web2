<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_projects()
    {
        $response = $this->get('/projects');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_create_a_project()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Test Project',
            'description' => 'This is a test project',
        ]);

        $response->assertRedirect('/projects');
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'This is a test project',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_see_their_projects()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Project',
        ]);
        
        $otherUserProject = Project::factory()->create([
            'name' => 'Someone Else Project',
        ]);

        $response = $this->actingAs($user)->get('/projects');
        
        $response->assertStatus(200);
        $response->assertSee('My Project');
        // User can't see other user's projects unless invited
        $response->assertDontSee('Someone Else Project');
    }

    /** @test */
    public function user_can_update_their_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Project Name',
        ]);

        $response = $this->actingAs($user)->put("/projects/{$project->id}", [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect("/projects");
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function user_cannot_update_others_project()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)->put("/projects/{$project->id}", [
            'name' => 'Updated Project',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_their_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("/projects/{$project->id}");

        $response->assertRedirect("/projects");
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_project()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)->delete("/projects/{$project->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
        ]);
    }

    /** @test */
    public function project_requires_a_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'description' => 'This is a test project',
        ]);

        $response->assertSessionHasErrors('name');
    }    /** @test */
    public function user_can_add_members_to_their_project()
    {
        $user = User::factory()->create();
        $member = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('projects.members.store', $project), [
            'members' => [$member->id],
        ]);

        $response->assertRedirect();
        $this->assertTrue($project->members->contains($member->id));
    }

    /** @test */
    public function user_can_remove_members_from_their_project()
    {
        $user = User::factory()->create();
        $member = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);
        
        $project->members()->attach($member);

        $response = $this->actingAs($user)->delete(route('projects.members.destroy', [$project, $member]));

        $response->assertRedirect();
        $this->assertFalse($project->members()->where('user_id', $member->id)->exists());
    }

    /** @test */
    public function only_project_owner_can_manage_members()
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $member = User::factory()->create();
        
        $project = Project::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($user)->post(route('projects.members.store', $project), [
            'member_ids' => [$member->id],
        ]);

        $response->assertStatus(403);
    }
}
