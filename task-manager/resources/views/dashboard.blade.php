@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Công việc gần đây</span>
                    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">Tạo mới</a>
                </div>
                <div class="card-body">
                    @if(count($tasks) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đến hạn</th>
                                        <th>Dự án</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td><a href="{{ route('tasks.show', $task) }}" style="text-decoration: none">{{ $task->title }}</a></td>
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
                                            <td>{{ $task->project ? $task->project->name : 'Không có dự án' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm">Xem tất cả công việc</a>
                    @else
                        <p class="text-center">Bạn chưa có công việc nào. <a href="{{ route('tasks.create') }}">Tạo công việc mới</a></p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Dự án của tôi</span>
                            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary">Tạo mới</a>
                        </div>
                        <div class="card-body">
                            @if(count($projects) > 0)
                                <ul class="list-group">
                                    @foreach($projects as $project)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="{{ route('projects.show', $project) }}" style="text-decoration: none">{{ $project->name }}</a>
                                            <span class="badge bg-primary rounded-pill">{{ $project->tasks->count() }} công việc</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-3">
                                    <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm">Xem tất cả dự án</a>
                                </div>
                            @else
                                <p class="text-center">Bạn chưa có dự án nào. <a href="{{ route('projects.create') }}">Tạo dự án mới</a></p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Dự án được mời tham gia</div>
                        <div class="card-body">
                            @if(count($memberProjects) > 0)
                                <ul class="list-group">
                                    @foreach($memberProjects as $project)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="{{ route('projects.show', $project) }}" style="text-decoration: none">{{ $project->name }}</a>
                                            <small>Chủ dự án: {{ $project->user->name }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-3">
                                    <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm">Xem tất cả dự án</a>
                                </div>
                            @else
                                <p class="text-center">Bạn chưa được mời tham gia dự án nào.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection