<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Create a project with members.
     *
     * @param int $count The number of members to add
     * @return static
     */
    public function withMembers(int $count = 1)
    {
        return $this->afterCreating(function (Project $project) use ($count) {
            $members = User::factory()->count($count)->create();
            $project->members()->attach($members);
        });
    }

    /**
     * Create a project with tasks.
     *
     * @param int $count The number of tasks to create
     * @return static
     */
    public function withTasks(int $count = 3)
    {
        return $this->afterCreating(function (Project $project) use ($count) {
            \App\Models\Task::factory()
                ->count($count)
                ->create([
                    'user_id' => $project->user_id,
                    'project_id' => $project->id,
                ]);
        });
    }
}
