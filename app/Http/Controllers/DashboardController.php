<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $tasks = Task::where('user_id', $user->id)
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
            
        $projects = $user->projects()->latest()->take(5)->get();
        $memberProjects = $user->memberProjects()->latest()->take(5)->get();
        
        return view('dashboard', compact('tasks', 'projects', 'memberProjects'));
    }
}