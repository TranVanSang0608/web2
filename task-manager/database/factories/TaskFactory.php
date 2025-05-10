<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'due_date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks'),
            'user_id' => User::factory(),
            'project_id' => null,
        ];
    }

    /**
     * Define a task with a project.
     *
     * @return static
     */
    public function withProject()
    {
        return $this->state(function (array $attributes) {
            return [
                'project_id' => Project::factory(),
            ];
        });
    }

    /**
     * Define a task with a specific status.
     *
     * @return static
     */
    public function withStatus(string $status)
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status,
            ];
        });
    }

    /**
     * Define a completed task.
     *
     * @return static
     */
    public function completed()
    {
        return $this->withStatus('completed');
    }

    /**
     * Define a task in progress.
     *
     * @return static
     */
    public function inProgress()
    {
        return $this->withStatus('in_progress');
    }

    /**
     * Define a pending task.
     *
     * @return static
     */
    public function pending()
    {
        return $this->withStatus('pending');
    }

    /**
     * Define a cancelled task.
     *
     * @return static
     */
    public function cancelled()
    {
        return $this->withStatus('cancelled');
    }
}
