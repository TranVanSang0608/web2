@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">            <div class="card border rounded shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f1f3f5;">
                    <span class="fw-bold"><i class="bi bi-list-check me-2"></i>Danh sách công việc</span>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">Tạo công việc mới</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(count($tasks) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đến hạn</th>
                                        <th>Dự án</th>
                                        <th>Tùy chọn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td><a href="{{ route('tasks.show', $task) }}" style="text-decoration: none">{{ $task->title }}</a></td>
                                            <td>                                                @switch($task->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-hourglass me-1"></i>Chờ xử lý</span>
                                                        @break
                                                    @case('in_progress')
                                                        <span class="badge bg-info text-dark rounded-pill"><i class="bi bi-arrow-repeat me-1"></i>Đang thực hiện</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Hoàn thành</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $task->project ? $task->project->name : 'Không có dự án' }}</td>
                                            <td>
   <div class="btn-group" role="group">
    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-primary me-2">Sửa</a>
    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger me-2" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
    </form>
    @if($task->status != 'completed')
        <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-success">V</button>
        </form>
    @else
        <span class="btn btn-sm btn-success disabled">V</span>
    @endif
</div>
</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <p class="text-center">Bạn chưa có công việc nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection