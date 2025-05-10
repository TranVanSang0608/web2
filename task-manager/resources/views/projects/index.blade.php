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
              <div class="card mb-4 border rounded shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa;">
                    <span class="fw-bold"><i class="bi bi-folder2-open me-2"></i>Dự án của tôi</span>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">Tạo dự án mới</a>
                </div>
                <div class="card-body">
                    @if(count($ownedProjects) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên dự án</th>
                                        <th>Số công việc</th>
                                        <th>Số thành viên</th>
                                        <th>Ngày tạo</th>
                                        <th>Tùy chọn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ownedProjects as $project)
                                        <tr>
                                            <td><a href="{{ route('projects.show', $project) }}"style="text-decoration: none">{{ $project->name }}</a></td>
                                            <td>{{ $project->tasks->count() }}</td>
                                            <td>{{ $project->members->count() }}</td>
                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-primary">Sửa</a>
                                                    <a href="{{ route('projects.members.create', $project) }}" class="btn btn-sm btn-info">Thành viên</a>
                                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">Bạn chưa có dự án nào. <a href="{{ route('projects.create') }}">Tạo dự án mới</a></p>
                    @endif
                </div>
            </div>
              <div class="card border rounded shadow-sm">
                <div class="card-header" style="background-color: #f8f9fa;"><i class="bi bi-people me-2"></i>Dự án được mời tham gia</div>
                <div class="card-body">
                    @if(count($memberProjects) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên dự án</th>
                                        <th>Chủ dự án</th>
                                        <th>Số công việc</th>
                                        <th>Số thành viên</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($memberProjects as $project)
                                        <tr>
                                            <td><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></td>
                                            <td>{{ $project->user->name }}</td>
                                            <td>{{ $project->tasks->count() }}</td>
                                            <td>{{ $project->members->count() }}</td>
                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">Bạn chưa được mời tham gia dự án nào.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection