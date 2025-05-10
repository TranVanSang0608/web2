@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Chi tiết công việc trong dự án: {{ $project->name }}</span>
                    <div>
                        @if($task->user_id == auth()->id() || $project->user_id == auth()->id())
                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-sm btn-primary">Chỉnh sửa</a>
                        @endif
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-secondary">Quay lại dự án</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <h2 class="mb-3">{{ $task->title }}</h2>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Trạng thái:</strong>
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
                        </div>
                        <div class="col-md-4">
                            <strong>Ngày đến hạn:</strong> {{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Không có' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Người tạo:</strong> {{ $task->user->name }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Mô tả:</strong>
                        <p class="mt-2">{{ $task->description ?? 'Không có mô tả' }}</p>
                    </div>
                    
                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <strong>Ngày tạo:</strong> {{ $task->created_at->format('d/m/Y H:i:s') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Cập nhật lần cuối:</strong> {{ $task->updated_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                    
                    @if($task->user_id == auth()->id() || $project->user_id == auth()->id())
                        <hr>
                        <form action="{{ route('projects.tasks.destroy', [$project, $task]) }}" method="POST" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa công việc này?')">Xóa công việc</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection