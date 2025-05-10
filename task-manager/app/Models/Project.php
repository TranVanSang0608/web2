<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members');
    }

    public function hasMember($user)
    {
        if ($this->user_id == $user->id) {
            return true;
        }
        
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function pendingTasks()
    {
        return $this->tasks()->where('status', 'pending');
    }

    public function completedTasks()
    {
        return $this->tasks()->where('status', 'completed');
    }

    public function inProgressTasks()
    {
        return $this->tasks()->where('status', 'in_progress');
    }

    public function completionPercentage()
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }
        
        $completed = $this->completedTasks()->count();
        return ($completed / $total) * 100;
    }
}