<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $ownedProjects = $user->projects()->latest()->get();
        $memberProjects = $user->memberProjects()->latest()->get();
        
        return view('projects.index', compact('ownedProjects', 'memberProjects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $validated['user_id'] = auth()->id();
        
        Project::create($validated);
        
        return redirect()->route('projects.index')->with('success', 'Dự án đã được tạo thành công!');
    }

    public function show(Project $project)
    {
        if ($project->user_id != auth()->id() && !$project->members->contains(auth()->id())) {
            abort(403);
        }
        
        $tasks = $project->tasks()->latest()->get();
        $members = $project->members()->get();
        
        return view('projects.show', compact('project', 'tasks', 'members'));
    }

    public function edit(Project $project)
    {
        if ($project->user_id != auth()->id()) {
            abort(403);
        }
        
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        if ($project->user_id != auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);
        
        $project->update($validated);
        
        return redirect()->route('projects.index')->with('success', 'Dự án đã được cập nhật thành công!');
    }

    public function destroy(Project $project)
    {
        if ($project->user_id != auth()->id()) {
            abort(403);
        }
        
        $project->delete();
        
        return redirect()->route('projects.index')->with('success', 'Dự án đã được xóa thành công!');
    }
}