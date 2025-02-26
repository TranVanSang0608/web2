<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\ProjectTaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route cho trang chủ - chuyển hướng tới dashboard nếu đã đăng nhập
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

// Route xác thực
Auth::routes();

// Tất cả các route yêu cầu xác thực
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Quản lý Task cá nhân
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    // Quản lý Project và các thành phần liên quan
    Route::prefix('projects')->name('projects.')->group(function () {
        // CRUD cơ bản cho Project
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
        
        // Quản lý thành viên Project
        Route::get('/{project}/members', [ProjectMemberController::class, 'create'])->name('members.create');
        Route::post('/{project}/members', [ProjectMemberController::class, 'store'])->name('members.store');
        Route::delete('/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('members.destroy');
        
        // Quản lý Task trong Project
        Route::get('/{project}/tasks', [ProjectTaskController::class, 'index'])->name('tasks.index');
        Route::get('/{project}/tasks/create', [ProjectTaskController::class, 'create'])->name('tasks.create');
        Route::post('/{project}/tasks', [ProjectTaskController::class, 'store'])->name('tasks.store');
        Route::get('/{project}/tasks/{task}', [ProjectTaskController::class, 'show'])->name('tasks.show');
        Route::get('/{project}/tasks/{task}/edit', [ProjectTaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/{project}/tasks/{task}', [ProjectTaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{project}/tasks/{task}', [ProjectTaskController::class, 'destroy'])->name('tasks.destroy');
    });
});