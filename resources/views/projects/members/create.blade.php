@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Quản lý thành viên dự án: {{ $project->name }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.members.store', $project) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Chọn thành viên:</label>
                            
                            @if(count($users) > 0)
                                <div class="list-group">
                                    @foreach($users as $user)
                                        <div class="list-group-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="members[]" value="{{ $user->id }}" id="user-{{ $user->id }}" {{ in_array($user->id, $currentMembers) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="user-{{ $user->id }}">
                                                    {{ $user->name }} ({{ $user->email }})
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @error('members')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            @else
                                <p>Không có người dùng nào khả dụng.</p>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Quay lại</a>
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection