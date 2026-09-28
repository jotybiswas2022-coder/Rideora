@extends('admin.layouts.app')

@section('title', 'Edit '.$category->name)
@section('page-title', 'Edit category')
@section('page-subtitle', $category->name)

@section('content')
    <div class="grid grid-sidebar">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Category details</h3>
                    <p>{{ $category->vehicles()->count() }} vehicles use this category</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">Back</a>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="stack-16">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Category name <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $category->name) }}" required>
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    @include('admin.partials.icon-field', ['icon' => $category->icon])

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control">{{ old('description', $category->description) }}</textarea>
                        @error('description')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span style="color:var(--danger)">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" @selected(old('status', $category->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $category->status) === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Vehicles in this category</h3>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($category->vehicles()->take(8)->get() as $vehicle)
                        <div class="flex-between mb-16" style="padding-bottom:14px; border-bottom:1px solid var(--border);">
                            <div>
                                <strong style="display:block; font-size:.88rem;">{{ $vehicle->name }}</strong>
                                <span class="muted small">{{ $vehicle->registration_number }}</span>
                            </div>
                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-light btn-sm">Edit</a>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:24px 0;">
                            <p>No vehicles in this category yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
@endsection
