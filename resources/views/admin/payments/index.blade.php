@extends('admin.layouts.app')

@section('title', 'Payments')
@section('page-title', 'Manual payments')
@section('page-subtitle', 'Verify or reject customer payment submissions')

@push('styles')
<style>
    /* Payments queue — page specific */
    .txn-code { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .84rem; display: block; }
    .cell-double strong { display: block; font-size: .86rem; }
    .cell-double span { font-size: .78rem; color: var(--muted); display: block; }
    .verifier { font-size: .74rem; color: var(--muted); display: block; margin-top: 4px; }
</style>
@endpush

@section('content')
    <div class="stat-grid mb-24">
        <div class="stat">
            <span class="lbl">Pending verification</span>
            <span class="val">{{ $stats['pending'] }}</span>
            <span class="note">Needs a decision</span>
        </div>
        <div class="stat">
            <span class="lbl">Verified</span>
            <span class="val">{{ $stats['verified'] }}</span>
            <span class="note">Bookings confirmed</span>
        </div>
        <div class="stat">
            <span class="lbl">Rejected</span>
            <span class="val">{{ $stats['rejected'] }}</span>
            <span class="note">Customer can resubmit</span>
        </div>
        <div class="stat highlight">
            <span class="lbl">Verified amount</span>
            <span class="val">{{ bdt($stats['verified_amount']) }}</span>
            <span class="note">Collected revenue</span>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.payments.index') }}">
            <div class="form-group">
                <label for="q">Search</label>
                <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                       placeholder="Transaction ID, booking code, customer">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Any status</option>
                    @foreach(\App\Models\Payment::STATUSES as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="method">Payment method</label>
                <select id="method" name="method" class="form-control">
                    <option value="">Any method</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" @selected((string) ($filters['method'] ?? '') === (string) $method->id)>{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <div class="flex" style="gap:10px;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-light">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h3>Payment submissions</h3>
                <p>{{ $payments->total() }} records</p>
            </div>
            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="btn btn-primary btn-sm">Show pending only</a>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Method</th>
                        <th>Transaction</th>
                        <th>Amount</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td data-label="Booking">
                                <strong>{{ $payment->booking->booking_code }}</strong>
                                <span class="muted small" style="display:block;">{{ $payment->booking->vehicle->name }}</span>
                            </td>
                            <td data-label="Customer">
                                <div class="cell-double">
                                    <strong>{{ $payment->user->name }}</strong>
                                    <span>{{ $payment->user->phone ?: '—' }}</span>
                                </div>
                            </td>
                            <td data-label="Method">{!! status_badge($payment->paymentMethod?->name ?? '—', 'badge-info') !!}</td>
                            <td data-label="Transaction">
                                <span class="txn-code">{{ $payment->transaction_id }}</span>
                                <span class="muted small">{{ $payment->created_at->format('d M Y, g:i A') }}</span>
                            </td>
                            <td data-label="Amount"><strong>{{ bdt($payment->amount) }}</strong></td>
                            <td data-label="Proof">
                                @if($payment->proofUrl())
                                    <a href="{{ $payment->proofUrl() }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">View</a>
                                @else
                                    <span class="muted small">None</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                {!! status_badge($payment->statusLabel(), $payment->statusClass()) !!}
                                @if($payment->verifier)
                                    <span class="verifier">by {{ $payment->verifier->name }}</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <div class="actions">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-outline btn-sm">
                                        {{ $payment->status === \App\Models\Payment::STATUS_PENDING ? 'Verify' : 'Open' }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center muted" style="padding:34px;">No payment submissions matched these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="card-foot pagination-wrap">{{ $payments->links() }}</div>
        @endif
    </div>
@endsection
