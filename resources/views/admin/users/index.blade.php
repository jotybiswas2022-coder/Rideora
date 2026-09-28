@extends('admin.layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')
@section('page-subtitle', $customers->total().' registered customers')

@push('styles')
<style>
    /* Customers list — page specific */
    .avatar-mini {
        width: 38px; height: 38px; border-radius: 50%; background: var(--primary-soft); color: var(--primary-dark);
        display: inline-flex; align-items: center; justify-content: center; font-size: .76rem; font-weight: 700;
        flex-shrink: 0; overflow: hidden;
    }
    .avatar-mini img { width: 100%; height: 100%; object-fit: cover; }
    .cell-double strong { display: block; font-size: .87rem; }
    .cell-double span { font-size: .78rem; color: var(--muted); display: block; }
</style>
@endpush

@section('content')
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="form-group">
                <label for="q">Search</label>
                <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                       placeholder="Name, email or phone">
            </div>

            <div class="form-group">
                <label for="status">Account status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Any status</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sort">Sort by</label>
                <select id="sort" name="sort" class="form-control">
                    <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest first</option>
                    <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name A-Z</option>
                    <option value="bookings" @selected(($filters['sort'] ?? '') === 'bookings')>Most bookings</option>
                </select>
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <div class="flex" style="gap:10px;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h3>Customer accounts</h3>
                <p>Administrators are managed separately and are not listed here</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>City</th>
                        <th>Bookings</th>
                        <th>Reviews</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div class="flex-center">
                                    <span class="avatar-mini">
                                        @if($customer->avatar_path)
                                            <img src="{{ \App\Models\VehicleImage::publicUrl($customer->avatar_path) }}" alt="{{ $customer->name }}">
                                        @else
                                            {{ $customer->initials() }}
                                        @endif
                                    </span>
                                    <span class="cell-double">
                                        <strong>{{ $customer->name }}</strong>
                                        <span>ID #{{ $customer->id }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="small">
                                {{ $customer->email }}<br>
                                <span class="muted">{{ $customer->phone ?: '—' }}</span>
                            </td>
                            <td class="small">{{ $customer->city ?: '—' }}</td>
                            <td><strong>{{ $customer->bookings_count }}</strong></td>
                            <td>{{ $customer->reviews_count }}</td>
                            <td class="small">{{ $customer->created_at->format('d M Y') }}</td>
                            <td>
                                {!! status_badge(ucfirst($customer->status), $customer->status === 'active' ? 'badge-success' : 'badge-danger') !!}
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.users.show', $customer) }}" class="btn btn-outline btn-sm">Open</a>

                                    <form method="POST" action="{{ route('admin.users.toggle', $customer) }}">
                                        @csrf
                                        <button type="submit" class="btn {{ $customer->status === 'active' ? 'btn-warning-soft' : 'btn-success' }} btn-sm">
                                            {{ $customer->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.destroy', $customer) }}"
                                          data-confirm="Delete {{ $customer->name }}? Their bookings and payments are removed too.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger-soft btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center muted" style="padding:34px;">No customers matched these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="card-foot pagination-wrap">{{ $customers->links() }}</div>
        @endif
    </div>
@endsection
