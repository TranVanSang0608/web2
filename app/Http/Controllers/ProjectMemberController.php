<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function create(Project $project)
    {
        if ($project->user_id != auth()->id()) {
            abort(403);
        }
        
        $users = User::where('id', '!=', auth()->id())->get();
        $currentMembers = $project->members->pluck('id')->toArray();
        
        return view('projects.members.create', compact('project', 'users', 'currentMembers'));
    }
    
    public function store(Request $request, Project $project)
    {
        if ($project->user_id != auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);
        
        $project->members()->sync($request->members);
        
        return redirect()->route('projects.show', $project)->with('success', 'Thành viên dự án đã được cập nhật!');
    }
    
    public function destroy(Project $project, User $user)
    {
        if ($project->user_id != auth()->id()) {
            abort(403);
        }
        
        $project->members()->detach($user->id);
        
        return redirect()->route('projects.show', $project)->with('success', 'Thành viên đã được xóa khỏi dự án!');
    }
}