@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">            <div class="card border rounded shadow-sm">
                <div class="card-header" style="background-color: #f8f9fa;"><i class="bi bi-folder-plus me-2"></i>Tạo dự án mới</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên dự án <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>                        <div class="d-flex justify-content-between">
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tạo dự án</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection