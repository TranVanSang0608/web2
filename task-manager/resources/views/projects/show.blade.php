@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Chi tiết dự án</span>
                    <div>
                        @if($project->user_id == auth()->id())
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-primary">Chỉnh sửa</a>
                            <a href="{{ route('projects.members.create', $project) }}" class="btn btn-sm btn-info">Quản lý thành viên</a>
                        @endif
                        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-secondary">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <h2 class="mb-3">{{ $project->name }}</h2>
                    
                    <div class="mb-3">
                        <strong>Mô tả:</strong>
                        <p class="mt-2">{{ $project->description ?? 'Không có mô tả' }}</p>
                    </div>
                    
                    <div class="row text-muted small mb-3">
                        <div class="col-md-4">
                            <strong>Chủ dự án:</strong> {{ $project->user->name }}
                        </div>
                        <div class="col-md-4">
                            <strong>Ngày tạo:</strong> {{ $project->created_at->format('d/m/Y') }}
                        </div>
                        <div class="col-md-4">
                            <strong>Số thành viên:</strong> {{ $members->count() }}
                        </div>
                    </div>
                    
                    @if($project->user_id == auth()->id())
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="mb-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này? Tất cả công việc trong dự án cũng sẽ bị xóa.')">Xóa dự án</button>
                        </form>
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <!-- Trong file projects/show.blade.php -->
<div class="card-header d-flex justify-content-between align-items-center">
    <span>Danh sách công việc</span>
    <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-sm btn-primary">Tạo công việc mới</a>
</div>
<div class="card-body">
    @if(count($tasks) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Ngày đến hạn</th>
                        <th>Người tạo</th>
                        <th>Tùy chọn</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td><a href="{{ route('projects.tasks.show', [$project, $task]) }}">{{ $task->title }}</a></td>
                            <td>
                                @switch($task->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-info">Đang thực hiện</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $task->user->name }}</td>
                            <td>
                                @if($task->user_id == auth()->id() || $project->user_id == auth()->id())
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-sm btn-primary">Sửa</a>
                                        <form action="{{ route('projects.tasks.destroy', [$project, $task]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center">Chưa có công việc nào trong dự án này.</p>
    @endif
</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Thành viên dự án</div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $project->user->name }} (Chủ dự án)
                                </li>
                                @foreach($members as $member)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $member->name }}
                                        @if($project->user_id == auth()->id())
                                            <form action="{{ route('projects.members.destroy', [$project, $member]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa thành viên này?')">Xóa</button>
                                            </form>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            
                            @if($project->user_id == auth()->id())
                                <div class="mt-3">
                                    <a href="{{ route('projects.members.create', $project) }}" class="btn btn-sm btn-primary w-100">Quản lý thành viên</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection