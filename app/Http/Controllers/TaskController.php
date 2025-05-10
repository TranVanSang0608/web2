<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tasks = auth()->user()->tasks()->latest()->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $user = auth()->user();
        $projects = Project::where('user_id', $user->id)
            ->orWhereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('name', 'id');
            
        return view('tasks.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $validated['user_id'] = auth()->id();
        
        Task::create($validated);
        
        return redirect()->route('tasks.index')->with('success', 'Công việc đã được tạo thành công!');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        
        $user = auth()->user();
        $projects = Project::where('user_id', $user->id)
            ->orWhereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('name', 'id');
            
        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'project_id' => 'nullable|exists:projects,id',
        ]);
        
        $task->update($validated);
        
        return redirect()->route('tasks.index')->with('success', 'Công việc đã được cập nhật thành công!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $task->delete();
        
        return redirect()->route('tasks.index')->with('success', 'Công việc đã được xóa thành công!');
    }

    public function complete(Task $task)
{
    $task->update(['status' => 'completed']);
    return redirect()->route('tasks.index')->with('success', 'Công việc đã được hoàn thành!');
}
}