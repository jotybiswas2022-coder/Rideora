@extends('admin.layouts.app')

@section('title', 'Payment #'.$payment->id)
@section('page-title', 'Payment verification')
@section('page-subtitle', 'Booking '.$payment->booking->booking_code.' · '.($payment->paymentMethod?->name ?? 'Manual payment'))

@push('styles')
<style>
    .proof-frame {
        border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 14px; background: #fff; text-align: center;
    }
    .proof-frame img { max-height: 460px; margin: 0 auto; border-radius: var(--radius); }
    .proof-missing {
        border: 1.5px dashed var(--border); border-radius: var(--radius); padding: 40px; text-align: center; color: var(--muted);
    }
    .check-list { list-style: none; display: grid; gap: 12px; font-size: .87rem; }
    .check-list li { display: flex; gap: 10px; align-items: flex-start; }
    .check-list .tick { color: var(--primary); }
    .spec-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .spec { background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 13px 15px; }
    .spec span.lbl { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; }
    .spec strong { font-size: .9rem; color: var(--dark); }
</style>
@endpush

@section('content')
    <div class="flex-between mb-24">
        <div class="flex-wrap flex" style="gap:8px;">
            {!! status_badge($payment->statusLabel(), $payment->statusClass()) !!}
            {!! status_badge($payment->paymentMethod?->name ?? 'Manual', 'badge-info') !!}
            <span class="muted small">Submitted {{ $payment->created_at->format('d M Y, g:i A') }}</span>
        </div>
        <div class="flex" style="gap:8px;">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-light btn-sm">All payments</a>
            <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="btn btn-outline btn-sm">Open booking</a>
        </div>
    </div>

    <div class="grid grid-sidebar">
        <div>
            <!-- ============ Submission details ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Submission details</h3>
                        <p>Match these values against your account statement</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="spec-grid">
                        <div class="spec"><span class="lbl">Amount submitted</span><strong>{{ bdt($payment->amount) }}</strong></div>
                        <div class="spec"><span class="lbl">Booking total</span><strong>{{ bdt($payment->booking->total_amount) }}</strong></div>
                        <div class="spec"><span class="lbl">Transaction ID</span><strong>{{ $payment->transaction_id }}</strong></div>
                        <div class="spec"><span class="lbl">Payment method</span><strong>{{ $payment->paymentMethod?->name ?? '—' }}</strong></div>
                        <div class="spec"><span class="lbl">Account number</span><strong>{{ $payment->paymentMethod?->account_number ?? '—' }}</strong></div>
                        <div class="spec"><span class="lbl">Account name</span><strong>{{ $payment->paymentMethod?->account_name ?? '—' }}</strong></div>
                    </div>

                    @if((float) $payment->amount !== (float) $payment->booking->total_amount)
                        <div class="alert alert-warning mt-24 mb-8">
                            <span>&#9888;</span>
                            <div>
                                The submitted amount ({{ bdt($payment->amount) }}) differs from the booking total
                                ({{ bdt($payment->booking->total_amount) }}). Verify carefully before approving.
                            </div>
                        </div>
                    @endif

                    @if($payment->admin_note)
                        <div class="alert alert-info mt-16 mb-8">
                            <span>&#8505;</span>
                            <div><strong>Admin note on record:</strong> {{ $payment->admin_note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============ Proof ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Payment proof</h3>
                        <p>Screenshot uploaded by the customer</p>
                    </div>
                    @if($payment->proofUrl())
                        <a href="{{ $payment->proofUrl() }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">Open full size</a>
                    @endif
                </div>
                <div class="card-body">
                    @if($payment->proofUrl())
                        <div class="proof-frame">
                            <img src="{{ $payment->proofUrl() }}" alt="Payment proof for {{ $payment->booking->booking_code }}">
                        </div>
                    @else
                        <div class="proof-missing">
                            <div style="font-size:1.8rem;">&#128247;</div>
                            <p>No screenshot was attached to this submission.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============ Booking recap ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Booking recap</h3>
                        <p>What this payment is confirming</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="cell-media">
                        <img src="{{ $payment->booking->vehicle->imageUrl() }}" alt="{{ $payment->booking->vehicle->name }}"
                             style="width:110px; height:78px;">
                        <span>
                            <strong style="font-size:1rem;">{{ $payment->booking->vehicle->name }}</strong>
                            <span>{{ $payment->booking->vehicle->registration_number }}</span>
                            <span>
                                {{ $payment->booking->pickup_date->format('d M Y') }}
                                → {{ $payment->booking->return_date->format('d M Y') }}
                            </span>
                        </span>
                    </div>

                    <div class="divider"></div>

                    <div class="summary-list">
                        <div class="summary-row"><span>Base rental</span><strong>{{ bdt($payment->booking->base_amount) }}</strong></div>
                        <div class="summary-row"><span>Security deposit</span><strong>{{ bdt($payment->booking->security_deposit) }}</strong></div>
                        @if((float) $payment->booking->discount > 0)
                            <div class="summary-row"><span>Discount</span><strong>-{{ bdt($payment->booking->discount) }}</strong></div>
                        @endif
                        <div class="summary-row total"><span>Booking total</span><span>{{ bdt($payment->booking->total_amount) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ Sidebar: decisions ============ -->
        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Verification decision</h3>
                        <p>{{ $payment->status === 'pending' ? 'Approve or reject this submission' : 'This payment is already reviewed' }}</p>
                    </div>
                </div>
                <div class="card-body">
                    @if($payment->status === \App\Models\Payment::STATUS_PENDING)
                        <ul class="check-list mb-24">
                            <li><span class="tick">&#10003;</span> Check the transaction ID in your bKash / Nagad / bank statement.</li>
                            <li><span class="tick">&#10003;</span> Confirm the amount matches the booking total.</li>
                            <li><span class="tick">&#10003;</span> Confirm the screenshot is a genuine receipt.</li>
                        </ul>

                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="stack-16"
                              data-confirm="Verify this payment and confirm booking {{ $payment->booking->booking_code }}?">
                            @csrf
                            <div class="form-group">
                                <label for="verify_note">Note (optional)</label>
                                <input type="text" id="verify_note" name="admin_note" class="form-control"
                                       placeholder="e.g. Matched with bank statement">
                            </div>
                            <button type="submit" class="btn btn-success btn-block btn-lg">&#10003; Verify payment</button>
                        </form>

                        <div class="divider"></div>

                        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="stack-16"
                              data-confirm="Reject this payment? The customer will be notified.">
                            @csrf
                            <div class="form-group">
                                <label for="reject_note">Rejection reason <span style="color:var(--danger)">*</span></label>
                                <textarea id="reject_note" name="admin_note" class="form-control" required
                                          placeholder="Tell the customer what was wrong so they can resubmit."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block">Reject payment</button>
                        </form>
                    @else
                        <div class="alert {{ $payment->status === 'verified' ? 'alert-success' : 'alert-error' }}">
                            <span>{{ $payment->status === 'verified' ? '&#10003;' : '&#9888;' }}</span>
                            <div>
                                This payment was <strong>{{ $payment->status }}</strong>
                                @if($payment->verifier) by {{ $payment->verifier->name }} @endif
                                @if($payment->verified_at) on {{ $payment->verified_at->format('d M Y, g:i A') }} @endif.
                            </div>
                        </div>

                        @if($payment->admin_note)
                            <p class="small"><strong>Note:</strong> {{ $payment->admin_note }}</p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Customer</h3>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>{{ $payment->user->name }}</strong></p>
                    <p class="muted small">{{ $payment->user->email }}</p>
                    <p class="muted small">{{ $payment->user->phone }}</p>
                    <div class="divider"></div>
                    <p class="muted small">
                        {{ $payment->user->bookings()->count() }} bookings ·
                        {{ $payment->user->payments()->count() }} payment submissions
                    </p>
                    <a href="{{ route('admin.users.show', $payment->user) }}" class="btn btn-outline btn-block mt-16">Open customer</a>
                </div>
            </div>
        </aside>
    </div>
@endsection
