@extends('admin.layouts.app')

@section('title', 'Categories')
@section('page-title', 'Vehicle categories')
@section('page-subtitle', $categories->count().' categories configured')

@push('styles')
<style>
    /* Categories list — page specific */
    .cat-icon { font-size: 1.25rem; line-height: 1; }
    .cell-slug { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .78rem; color: var(--muted); }
</style>
@endpush

@section('content')
    <div class="card">
        <div class="card-head">
            <div>
                <h3>Categories</h3>
                <p>Categories group the fleet and power the homepage shortcuts</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">+ Add category</a>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Vehicles</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="flex-center">
                                    <span class="cat-icon">{!! $category->icon ?: '&#128664;' !!}</span>
                                    <strong>{{ $category->name }}</strong>
                                </div>
                            </td>
                            <td><span class="cell-slug">{{ $category->slug }}</span></td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($category->description, 70) ?: '—' }}</td>
                            <td><strong>{{ $category->vehicles_count }}</strong></td>
                            <td>
                                {!! status_badge(ucfirst($category->status), $category->isActive() ? 'badge-success' : 'badge-muted') !!}
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          data-confirm="Delete category {{ $category->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger-soft btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center muted" style="padding:34px;">No categories yet. Add your first one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
