@extends('admin.layouts.app')

@section('title', 'Booking '.$booking->booking_code)
@section('page-title', 'Booking '.$booking->booking_code)
@section('page-subtitle', 'Created '.$booking->created_at->format('d M Y, g:i A'))

@push('styles')
<style>
    .spec-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .spec { background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 13px 15px; }
    .spec span.lbl { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; }
    .spec strong { font-size: .9rem; color: var(--dark); }
    .pay-card { border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 12px; }
    .pay-card:last-child { margin-bottom: 0; }
    .customer-box { display: flex; gap: 14px; align-items: center; }
    .customer-avatar {
        width: 52px; height: 52px; border-radius: 50%; background: var(--primary-soft); color: var(--primary-dark);
        display: inline-flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;
    }
</style>
@endpush

@section('content')
    <div class="flex-between mb-24">
        <div class="flex-wrap flex" style="gap:8px;">
            {!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}
            {!! status_badge($booking->paymentStatusLabel(), $booking->paymentStatusClass()) !!}
            @if($booking->isOverdue())
                {!! status_badge('Return overdue', 'badge-danger') !!}
            @endif
        </div>
        <div class="flex" style="gap:8px;">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-light btn-sm">All bookings</a>
            @if($payment = $booking->payments->first())
                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-outline btn-sm">Open payment</a>
            @endif
        </div>
    </div>

    <div class="grid grid-sidebar">
        <div>
            <!-- ============ Rental details ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Rental details</h3>
                        <p>Dates, locations and duration</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="spec-grid">
                        <div class="spec"><span class="lbl">Pickup</span><strong>{{ $booking->pickup_date->format('D, d M Y') }}</strong>
                            <span class="muted small">{{ \Illuminate\Support\Str::substr((string) $booking->pickup_time, 0, 5) }} · {{ $booking->pickup_location }}</span>
                        </div>
                        <div class="spec"><span class="lbl">Return</span><strong>{{ $booking->return_date->format('D, d M Y') }}</strong>
                            <span class="muted small">{{ \Illuminate\Support\Str::substr((string) $booking->return_time, 0, 5) }} · {{ $booking->dropoff_location ?: $booking->pickup_location }}</span>
                        </div>
                        <div class="spec"><span class="lbl">Duration</span><strong>{{ $booking->durationLabel() }}</strong>
                            <span class="muted small">{{ $booking->rental_days }} days / {{ $booking->rental_hours }} hours</span>
                        </div>
                        <div class="spec"><span class="lbl">Pricing</span><strong>{{ bdt($booking->total_amount) }}</strong>
                            <span class="muted small">{{ bdt($booking->vehicle->price_per_day) }} per day</span>
                        </div>
                    </div>

                    @if($booking->customer_note)
                        <div class="alert alert-info mt-24 mb-8">
                            <span><i class="bi bi-info-circle-fill"></i></span>
                            <div><strong>Customer note:</strong> {{ $booking->customer_note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============ Customer ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Customer</h3>
                        <p>Account and contact details</p>
                    </div>
                    <a href="{{ route('admin.users.show', $booking->user) }}" class="btn btn-outline btn-sm">Open profile</a>
                </div>
                <div class="card-body">
                    <div class="customer-box">
                        <span class="customer-avatar">{{ $booking->user->initials() }}</span>
                        <div>
                            <strong style="display:block;">{{ $booking->user->name }}</strong>
                            <span class="muted small">{{ $booking->user->email }}</span>
                            <span class="muted small" style="display:block;">{{ $booking->user->phone ?: 'No phone on file' }}</span>
                            <span class="muted small" style="display:block;">
                                {{ $booking->user->bookings()->count() }} bookings ·
                                {{ bdt($booking->user->bookings()->where('payment_status', 'paid')->sum('total_amount')) }} paid
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ Vehicle ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Vehicle</h3>
                        <p>Reserved for this booking</p>
                    </div>
                    <a href="{{ route('admin.vehicles.show', $booking->vehicle) }}" class="btn btn-outline btn-sm">Open vehicle</a>
                </div>
                <div class="card-body">
                    <div class="cell-media">
                        <img src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}" style="width:110px; height:78px;">
                        <span>
                            <strong style="font-size:1rem;">{{ $booking->vehicle->name }}</strong>
                            <span>{{ $booking->vehicle->brand }} · {{ $booking->vehicle->registration_number }}</span>
                            <span>{{ $booking->vehicle->category?->name }} · {{ $booking->vehicle->transmission }} · {{ $booking->vehicle->seats }} seats</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ============ Payments ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Payment submissions</h3>
                        <p>{{ $booking->payments->count() }} submission(s) on record</p>
                    </div>
                    <a href="{{ route('admin.payments.index', ['q' => $booking->booking_code]) }}" class="btn btn-light btn-sm">All payments</a>
                </div>
                <div class="card-body">
                    @forelse($booking->payments as $payment)
                        <div class="pay-card">
                            <div class="flex-between mb-8">
                                <div>
                                    <strong>{{ $payment->paymentMethod?->name ?? 'Manual payment' }}</strong>
                                    <span class="muted small" style="display:block;">TrxID: {{ $payment->transaction_id }}</span>
                                </div>
                                <div class="text-right">
                                    <strong>{{ bdt($payment->amount) }}</strong>
                                    <div>{!! status_badge($payment->statusLabel(), $payment->statusClass()) !!}</div>
                                </div>
                            </div>
                            <div class="muted small">
                                Submitted {{ $payment->created_at->format('d M Y, g:i A') }}
                                @if($payment->verifier)
                                    · Reviewed by {{ $payment->verifier->name }} {{ $payment->verified_at?->format('d M Y, g:i A') }}
                                @endif
                            </div>
                            @if($payment->admin_note)
                                <div class="small mt-8"><strong>Admin note:</strong> {{ $payment->admin_note }}</div>
                            @endif
                            <div class="actions mt-8">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-outline btn-sm">Open payment</a>
                                @if($payment->proofUrl())
                                    <a href="{{ $payment->proofUrl() }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">View proof</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:26px 0;">
                            <div class="icon"><i class="bi bi-cash-stack"></i></div>
                            <p>No payment submitted for this booking yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ============ Sidebar ============ -->
        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Manage booking</h3>
                        <p>Statuses and internal notes</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="stack-16">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="booking_status">Booking status <span style="color:var(--danger)">*</span></label>
                            <select id="booking_status" name="booking_status" class="form-control" required>
                                @foreach(\App\Models\Booking::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(old('booking_status', $booking->booking_status) === $status)>
                                        {{ \Illuminate\Support\Str::headline($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="payment_status">Payment status <span style="color:var(--danger)">*</span></label>
                            <select id="payment_status" name="payment_status" class="form-control" required>
                                @foreach(\App\Models\Booking::PAYMENT_STATUSES as $paymentStatus)
                                    <option value="{{ $paymentStatus }}" @selected(old('payment_status', $booking->payment_status) === $paymentStatus)>
                                        {{ \Illuminate\Support\Str::headline($paymentStatus) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="admin_note">Admin note</label>
                            <textarea id="admin_note" name="admin_note" class="form-control"
                                      placeholder="Visible to the customer on their booking page">{{ old('admin_note', $booking->admin_note) }}</textarea>
                            <span class="form-hint">The customer sees this note on their booking page.</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Save booking</button>
                    </form>

                    <div class="divider"></div>

                    @if(! in_array($booking->booking_status, \App\Models\Booking::BLOCKING_STATUSES, true))
                        <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}"
                              data-confirm="Delete booking {{ $booking->booking_code }} permanently?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger-soft btn-block">Delete booking</button>
                        </form>
                    @else
                        <p class="muted small">This booking is active. Cancel it before deleting.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Amount breakdown</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary-list">
                        <div class="summary-row"><span>Base rental</span><strong>{{ bdt($booking->base_amount) }}</strong></div>
                        <div class="summary-row"><span>Security deposit</span><strong>{{ bdt($booking->security_deposit) }}</strong></div>
                        @if((float) $booking->discount > 0)
                            <div class="summary-row"><span>Discount</span><strong>-{{ bdt($booking->discount) }}</strong></div>
                        @endif
                        <div class="summary-row total"><span>Total</span><span>{{ bdt($booking->total_amount) }}</span></div>
                    </div>
                </div>
            </div>

            @if($booking->review)
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h3>Customer review</h3>
                            <p>{{ ucfirst($booking->review->status) }}</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="color:#F59E0B; font-size:1rem;">{!! star_row($booking->review->rating) !!}</div>
                        <p class="small mt-8">{{ $booking->review->comment }}</p>
                    </div>
                </div>
            @endif
        </aside>
    </div>
@endsection
