<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task)
    {
        // Người tạo hoặc thành viên dự án có thể xem task
        if ($task->user_id === $user->id) {
            return true;
        }
        
        return $task->project && ($task->project->user_id === $user->id || $task->project->members->contains($user->id));
    }

    public function update(User $user, Task $task)
    {
        // Chỉ người tạo task có thể sửa
        return $task->user_id === $user->id;
    }

    public function delete(User $user, Task $task)
    {
        // Chỉ người tạo task có thể xóa
        return $task->user_id === $user->id;
    }
}