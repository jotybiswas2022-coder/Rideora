@extends('admin.layouts.app')

@section('title', $user->name)
@section('page-title', $user->name)
@section('page-subtitle', $user->email)

@push('styles')
<style>
    .profile-box { display: flex; gap: 18px; align-items: center; }
    .profile-avatar {
        width: 78px; height: 78px; border-radius: 50%; background: linear-gradient(135deg, #2563EB, #1D4ED8);
        color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;
        overflow: hidden; flex-shrink: 0;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .spec-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .spec { background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 13px 15px; }
    .spec span.lbl { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; }
    .spec strong { font-size: .9rem; color: var(--dark); }
    @media (max-width: 900px) { .spec-grid { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('content')
    <div class="flex-between mb-24">
        <div class="flex-wrap flex" style="gap:8px;">
            {!! status_badge(ucfirst($user->status), $user->status === 'active' ? 'badge-success' : 'badge-danger') !!}
            {!! status_badge('Customer since '.$user->created_at->format('M Y'), 'badge-muted') !!}
        </div>
        <div class="flex" style="gap:8px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">All customers</a>
            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                @csrf
                <button type="submit" class="btn {{ $user->status === 'active' ? 'btn-warning-soft' : 'btn-success' }} btn-sm">
                    {{ $user->status === 'active' ? 'Deactivate account' : 'Activate account' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-sidebar">
        <div>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Profile</h3>
                        <p>Contact and identity details</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="profile-box mb-24">
                        <span class="profile-avatar">
                            @if($user->avatar_path)
                                <img src="{{ \App\Models\VehicleImage::publicUrl($user->avatar_path) }}" alt="{{ $user->name }}">
                            @else
                                {{ $user->initials() }}
                            @endif
                        </span>
                        <div>
                            <strong style="font-size:1.1rem; display:block;">{{ $user->name }}</strong>
                            <span class="muted small">{{ $user->email }}</span>
                            <span class="muted small" style="display:block;">Joined {{ $user->created_at->format('d M Y, g:i A') }}</span>
                        </div>
                    </div>

                    <div class="spec-grid">
                        <div class="spec"><span class="lbl">Phone</span><strong>{{ $user->phone ?: 'Not provided' }}</strong></div>
                        <div class="spec"><span class="lbl">City</span><strong>{{ $user->city ?: 'Not provided' }}</strong></div>
                        <div class="spec"><span class="lbl">Driving licence</span><strong>{{ $user->driving_license_no ?: 'Not provided' }}</strong></div>
                        <div class="spec"><span class="lbl">Address</span><strong>{{ $user->address ?: 'Not provided' }}</strong></div>
                        <div class="spec"><span class="lbl">Email verified</span><strong>{{ $user->email_verified_at ? 'Yes' : 'No' }}</strong></div>
                        <div class="spec"><span class="lbl">Last updated</span><strong>{{ $user->updated_at->format('d M Y') }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Booking history</h3>
                        <p>{{ $bookings->total() }} bookings</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Booking</th><th>Vehicle</th><th>Dates</th><th>Total</th><th>Status</th><th>Payment</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td><strong>{{ $booking->booking_code }}</strong></td>
                                    <td class="small">{{ $booking->vehicle->name }}</td>
                                    <td class="small">{{ $booking->pickup_date->format('d M Y') }} → {{ $booking->return_date->format('d M Y') }}</td>
                                    <td>{{ bdt($booking->total_amount) }}</td>
                                    <td>{!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}</td>
                                    <td>{!! status_badge($booking->paymentStatusLabel(), $booking->paymentStatusClass()) !!}</td>
                                    <td><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-light btn-sm">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center muted" style="padding:28px;">This customer has no bookings yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($bookings->hasPages())
                    <div class="card-foot pagination-wrap">{{ $bookings->links() }}</div>
                @endif
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Recent payment submissions</h3>
                        <p>Latest ten manual payments</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Booking</th><th>Method</th><th>Transaction</th><th>Amount</th><th>Status</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td><strong>{{ $payment->booking->booking_code }}</strong></td>
                                    <td class="small">{{ $payment->paymentMethod?->name ?? '—' }}</td>
                                    <td class="small">{{ $payment->transaction_id }}</td>
                                    <td>{{ bdt($payment->amount) }}</td>
                                    <td>{!! status_badge($payment->statusLabel(), $payment->statusClass()) !!}</td>
                                    <td><a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-light btn-sm">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center muted" style="padding:28px;">No payment submissions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Summary</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary-list">
                        <div class="summary-row"><span>Total bookings</span><strong>{{ $user->bookings_count }}</strong></div>
                        <div class="summary-row"><span>Payment submissions</span><strong>{{ $user->payments_count }}</strong></div>
                        <div class="summary-row"><span>Reviews written</span><strong>{{ $user->reviews_count }}</strong></div>
                        <div class="summary-row total"><span>Total paid</span><span>{{ bdt($spent) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Account actions</h3>
                    </div>
                </div>
                <div class="card-body">
                    <p class="muted small mb-16">
                        Deleting a customer removes their bookings and payments. Customers with active bookings cannot be deleted.
                    </p>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          data-confirm="Delete {{ $user->name }} permanently?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">Delete customer</button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
@endsection
