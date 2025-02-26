<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Project $project)
    {
        if ($project->user_id != auth()->id() && !$project->members->contains(auth()->id())) {
            abort(403);
        }
        
        $tasks = $project->tasks()->latest()->paginate(10);
        return view('projects.tasks.index', compact('project', 'tasks'));
    }

    public function create(Project $project)
    {
        if ($project->user_id != auth()->id() && !$project->members->contains(auth()->id())) {
            abort(403);
        }
        
        return view('projects.tasks.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        if ($project->user_id != auth()->id() && !$project->members->contains(auth()->id())) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['project_id'] = $project->id;
        
        Task::create($validated);
        
        return redirect()->route('projects.show', $project)->with('success', 'Công việc đã được tạo thành công!');
    }

    public function show(Project $project, Task $task)
    {
        if ($project->id != $task->project_id) {
            abort(404);
        }
        
        if ($task->user_id != auth()->id() && $project->user_id != auth()->id() && !$project->members->contains(auth()->id())) {
            abort(403);
        }
        
        return view('projects.tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, Task $task)
    {
        if ($project->id != $task->project_id) {
            abort(404);
        }
        
        if ($task->user_id != auth()->id() && $project->user_id != auth()->id()) {
            abort(403);
        }
        
        return view('projects.tasks.edit', compact('project', 'task'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        if ($project->id != $task->project_id) {
            abort(404);
        }
        
        if ($task->user_id != auth()->id() && $project->user_id != auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);
        
        $task->update($validated);
        
        return redirect()->route('projects.tasks.show', [$project, $task])->with('success', 'Công việc đã được cập nhật thành công!');
    }

    public function destroy(Project $project, Task $task)
    {
        if ($project->id != $task->project_id) {
            abort(404);
        }
        
        if ($task->user_id != auth()->id() && $project->user_id != auth()->id()) {
            abort(403);
        }
        
        $task->delete();
        
        return redirect()->route('projects.show', $project)->with('success', 'Công việc đã được xóa thành công!');
    }
}