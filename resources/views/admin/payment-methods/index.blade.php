@extends('admin.layouts.app')

@section('title', 'Payment Methods')
@section('page-title', 'Payment methods')
@section('page-subtitle', 'Accounts customers pay into for manual verification')

@push('styles')
<style>
    /* Payment methods list — page specific */
    .method-logo-sm {
        width: 38px; height: 38px; border-radius: 10px; background: var(--dark); color: #fff; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 700;
        letter-spacing: .04em;
    }
    .cell-account { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .82rem; }
    .cell-note { font-size: .8rem; color: var(--muted); }
</style>
@endpush

@section('content')
    <div class="card">
        <div class="card-head">
            <div>
                <h3>Manual payment methods</h3>
                <p>These accounts are shown on the customer payment page</p>
            </div>
            <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary btn-sm">+ Add payment method</a>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Account name</th>
                        <th>Account number</th>
                        <th>Instructions</th>
                        <th>Payments</th>
                        <th>Pending</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($methods as $method)
                        <tr>
                            <td>
                                <div class="flex-center">
                                    <span class="method-logo-sm">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($method->name, 0, 3)) }}
                                    </span>
                                    <strong>{{ $method->name }}</strong>
                                </div>
                            </td>
                            <td class="cell-note">{{ $method->account_name ?: '—' }}</td>
                            <td><span class="cell-account">{{ $method->account_number ?: '—' }}</span></td>
                            <td class="cell-note">{{ \Illuminate\Support\Str::limit($method->instructions, 60) ?: '—' }}</td>
                            <td><strong>{{ $method->payments_count }}</strong></td>
                            <td>
                                @php $pending = $pendingPerMethod[$method->id] ?? 0; @endphp
                                @if($pending > 0)
                                    {!! status_badge($pending.' pending', 'badge-warning') !!}
                                @else
                                    <span class="muted small">None</span>
                                @endif
                            </td>
                            <td>{!! status_badge(ucfirst($method->status), $method->isActive() ? 'badge-success' : 'badge-muted') !!}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.payment-methods.edit', $method) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.payment-methods.destroy', $method) }}"
                                          data-confirm="Delete {{ $method->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger-soft btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center muted" style="padding:34px;">
                            No payment methods yet. Customers cannot pay until you add at least one.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
