@extends('admin.layouts.app')

@section('title', 'Add Category')
@section('page-title', 'Add category')
@section('page-subtitle', 'Group vehicles for easier browsing')

@push('styles')
<style>
    /* Add category — page specific */
    .narrow-card { max-width: 760px; }
    .form-hint code { background: #F1F5F9; padding: 1px 6px; border-radius: 5px; font-size: .78rem; }
</style>
@endpush

@section('content')
    <div class="card narrow-card">
        <div class="card-head">
            <div>
                <h3>Category details</h3>
                <p>The slug is generated automatically from the name</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">Back</a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="stack-16">
                @csrf

                <div class="form-group">
                    <label for="name">Category name <span style="color:var(--danger)">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required placeholder="e.g. Sedan">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="icon">Icon (HTML entity, optional)</label>
                    <input type="text" id="icon" name="icon" class="form-control @error('icon') is-invalid @enderror"
                           value="{{ old('icon') }}" placeholder="e.g. &#38;#128663; for a car icon">
                    <span class="form-hint">Paste an HTML numeric entity such as <code>&amp;#128663;</code> or leave empty.</span>
                    @error('icon')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              placeholder="What kind of vehicles belong to this category?">{{ old('description') }}</textarea>
                    @error('description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status <span style="color:var(--danger)">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                    </select>
                    <span class="form-hint">Inactive categories are hidden from the website filters.</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
