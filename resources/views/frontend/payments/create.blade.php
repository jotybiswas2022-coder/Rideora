@extends('frontend.layouts.app')

@section('title', 'Payment for '.$booking->booking_code.' — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .pay-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 26px; align-items: start; }
    .panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 26px; }
    .panel + .panel { margin-top: 22px; }
    .panel h2 { font-size: 1.1rem; margin-bottom: 6px; }
    .panel p.sub { color: var(--muted); font-size: .87rem; margin-bottom: 18px; }
    .sticky-side { position: sticky; top: 88px; }

    .method-select { display: grid; gap: 12px; margin-bottom: 22px; }
    .method-option { position: relative; }
    .method-option input { position: absolute; opacity: 0; pointer-events: none; }
    .method-option label {
        display: flex; gap: 14px; align-items: flex-start; border: 1.5px solid var(--border); border-radius: var(--radius);
        padding: 15px 16px; cursor: pointer; background: #fff; transition: border-color .15s ease, background .15s ease;
    }
    .method-option label:hover { border-color: #BFDBFE; }
    .method-option input:checked + label { border-color: var(--primary); background: var(--primary-soft); }
    .method-logo {
        width: 46px; height: 46px; border-radius: 12px; background: var(--dark); color: #fff; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .78rem;
    }
    .method-body strong { display: block; font-size: .95rem; }
    .method-body .row { font-size: .84rem; color: var(--muted); }
    .method-body .acc { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 700; color: var(--dark); font-size: .9rem; }

    .instructions { background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; font-size: .87rem; white-space: pre-line; }

    .upload-box {
        border: 1.5px dashed var(--border); border-radius: var(--radius); padding: 22px; text-align: center;
        background: var(--light); cursor: pointer;
    }
    .upload-box:hover { border-color: var(--primary); }
    .upload-preview { margin-top: 14px; display: none; }
    .upload-preview img { max-height: 210px; border-radius: var(--radius); border: 1px solid var(--border); margin: 0 auto; }

    .status-steps { list-style: none; display: grid; gap: 14px; }
    .status-steps li { display: flex; gap: 12px; align-items: flex-start; font-size: .87rem; }
    .step-dot {
        width: 26px; height: 26px; border-radius: 50%; background: var(--light); border: 1px solid var(--border);
        display: inline-flex; align-items: center; justify-content: center; font-size: .74rem; font-weight: 700; color: var(--muted); flex-shrink: 0;
    }
    .step-dot.done { background: var(--success); border-color: var(--success); color: #fff; }
    .step-dot.current { background: var(--primary); border-color: var(--primary); color: #fff; }

    @media (max-width: 960px) {
        .pay-layout { grid-template-columns: 1fr; gap: 18px; }
        .sticky-side { position: static; }
    }

    @media (max-width: 620px) {
        .panel { padding: 18px; }
        .panel + .panel { margin-top: 16px; }
        .method-option label { padding: 13px; gap: 12px; }
        .method-logo { width: 42px; height: 42px; }
        .method-body .acc { font-size: .82rem; }
        .instructions { font-size: .83rem; padding: 14px; }
        .upload-box { padding: 18px; }
        .status-steps { gap: 12px; }
        .panel img[alt] { width: 100%; height: 170px; }
    }

    @media (max-width: 420px) {
        .method-logo { display: none; }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Manual payment</h1>
        <p>Send the total to the account below, then submit the transaction ID and a screenshot so we can verify it.</p>
    </div>

    <div class="pay-layout">
        <div>
            <!-- ===== Booking summary ===== -->
            <div class="panel">
                <h2>Booking summary</h2>
                <p class="sub">Booking ID <strong>{{ $booking->booking_code }}</strong></p>

                <div class="flex flex-wrap" style="gap:18px; align-items:center;">
                    <img src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}"
                         style="width:140px; height:96px; object-fit:cover; border-radius:12px; background:#EEF2F7;">
                    <div style="flex:1; min-width:220px;">
                        <strong style="font-size:1.05rem;">{{ $booking->vehicle->name }}</strong>
                        <p class="muted small">{{ $booking->vehicle->brand }} · {{ $booking->vehicle->registration_number }}</p>
                        <p class="small mt-8">
                            {{ $booking->pickup_date->format('d M Y') }} {{ \Illuminate\Support\Str::substr((string) $booking->pickup_time, 0, 5) }}
                            &rarr;
                            {{ $booking->return_date->format('d M Y') }} {{ \Illuminate\Support\Str::substr((string) $booking->return_time, 0, 5) }}
                        </p>
                        <p class="small">{{ $booking->durationLabel() }}</p>
                    </div>
                    {!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}
                </div>
            </div>

            <!-- ===== Payment methods ===== -->
            <div class="panel">
                <h2>Choose a payment method</h2>
                <p class="sub">All three options are verified manually by our team.</p>

                @if($paymentMethods->isEmpty())
                    <div class="alert alert-error">
                        <span>&#9888;</span>
                        <div>No payment method is available right now. Please contact support.</div>
                    </div>
                @else
                    <form method="POST" action="{{ route('payments.submit') }}" enctype="multipart/form-data" id="payment-form">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                        <div class="method-select">
                            @foreach($paymentMethods as $index => $method)
                                <div class="method-option">
                                    <input type="radio" id="method-{{ $method->id }}" name="payment_method_id" value="{{ $method->id }}"
                                           data-account="{{ $method->account_number }}" data-name="{{ $method->name }}"
                                           data-instructions="{{ $method->instructions }}"
                                           {{ old('payment_method_id', $paymentMethods->first()->id) == $method->id ? 'checked' : '' }} required>
                                    <label for="method-{{ $method->id }}">
                                        <span class="method-logo">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($method->name, 0, 3)) }}</span>
                                        <span class="method-body">
                                            <strong>{{ $method->name }}</strong>
                                            <span class="row">Account name: {{ $method->account_name }}</span>
                                            <span class="row">Send money to: <span class="acc">{{ $method->account_number }}</span></span>
                                        </span>
                                    </label>
                                    @error('payment_method_id')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            @endforeach
                        </div>

                        <h2 style="font-size:1rem;">Payment instructions</h2>
                        <div class="instructions mb-24" id="method-instructions">
                            {{ $paymentMethods->first()->instructions ?: 'Send the exact total and keep the transaction receipt.' }}
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="amount">Amount sent (৳) <span style="color:var(--danger)">*</span></label>
                                <input type="number" step="0.01" min="1" id="amount" name="amount"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       value="{{ old('amount', number_format((float) $booking->total_amount, 2, '.', '')) }}" required>
                                <span class="form-hint">Total payable: <strong>{{ bdt($booking->total_amount) }}</strong> (include the security deposit).</span>
                                @error('amount')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="transaction_id">Transaction ID / Reference <span style="color:var(--danger)">*</span></label>
                                <input type="text" id="transaction_id" name="transaction_id"
                                       class="form-control @error('transaction_id') is-invalid @enderror"
                                       value="{{ old('transaction_id') }}" placeholder="e.g. BKS7H2K91A" required>
                                <span class="form-hint">Copy it from your bKash, Nagad or bank confirmation message.</span>
                                @error('transaction_id')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group full">
                                <label for="payment_proof">Payment screenshot <span style="color:var(--danger)">*</span></label>

                                <label class="upload-box" for="payment_proof">
                                    <div style="font-size:1.6rem;">&#128247;</div>
                                    <strong style="display:block; margin:6px 0 2px; font-size:.92rem;">Click to upload a screenshot</strong>
                                    <span class="muted small">JPG, PNG or WEBP · maximum 3 MB</span>
                                </label>

                                <input type="file" id="payment_proof" name="payment_proof" accept="image/png,image/jpeg,image/webp"
                                       class="form-control mt-8 @error('payment_proof') is-invalid @enderror" required data-image-input>
                                @error('payment_proof')<span class="form-error">{{ $message }}</span>@enderror

                                <div class="upload-preview text-center" data-image-preview>
                                    <img src="" alt="Payment screenshot preview">
                                </div>
                            </div>
                        </div>

                        <label class="checkbox-row mt-24" for="agree_terms">
                            <input type="checkbox" id="agree_terms" name="agree_terms" value="1" required>
                            <span>I confirm that the transaction ID and screenshot are correct. I understand the booking is confirmed only after Rideora verifies this payment.</span>
                        </label>
                        @error('agree_terms')<span class="form-error">{{ $message }}</span>@enderror

                        <div class="form-actions mt-24">
                            <button type="submit" class="btn btn-primary btn-lg">Submit payment for verification</button>
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-light btn-lg">Back to booking</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- ===== Sidebar ===== -->
        <aside class="sticky-side">
            <div class="panel">
                <h2>Amount to pay</h2>
                <div class="summary-list">
                    <div class="summary-row"><span>Base rental</span><strong>{{ bdt($booking->base_amount) }}</strong></div>
                    <div class="summary-row"><span>Security deposit</span><strong>{{ bdt($booking->security_deposit) }}</strong></div>
                    @if((float) $booking->discount > 0)
                        <div class="summary-row"><span>Discount</span><strong>-{{ bdt($booking->discount) }}</strong></div>
                    @endif
                    <div class="summary-row total"><span>Total payable</span><span>{{ bdt($booking->total_amount) }}</span></div>
                </div>
                <p class="muted small mt-16">Send exactly this amount. Partial payments delay verification.</p>
            </div>

            <div class="panel">
                <h2>What happens next</h2>
                <ul class="status-steps">
                    <li>
                        <span class="step-dot done">1</span>
                        <div><strong>Booking created</strong><br><span class="muted small">Your dates are held.</span></div>
                    </li>
                    <li>
                        <span class="step-dot current">2</span>
                        <div><strong>Submit payment</strong><br><span class="muted small">Add the transaction ID and screenshot.</span></div>
                    </li>
                    <li>
                        <span class="step-dot">3</span>
                        <div><strong>Admin verification</strong><br><span class="muted small">Our team checks the payment manually.</span></div>
                    </li>
                    <li>
                        <span class="step-dot">4</span>
                        <div><strong>Booking confirmed</strong><br><span class="muted small">You receive a notification and can pick up the vehicle.</span></div>
                    </li>
                </ul>
            </div>

            @if($latestPayment && $latestPayment->status === \App\Models\Payment::STATUS_PENDING)
                <div class="panel">
                    <h2>Pending submission</h2>
                    <p class="small muted">
                        You already submitted transaction <strong>{{ $latestPayment->transaction_id }}</strong>
                        ({{ bdt($latestPayment->amount) }}) via {{ $latestPayment->paymentMethod?->name }} on
                        {{ $latestPayment->created_at->format('d M Y, g:i A') }}. It is awaiting verification.
                    </p>
                </div>
            @endif
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show the instructions for the selected method.
        var instructions = document.getElementById('method-instructions');
        function updateInstructions() {
            var checked = document.querySelector('input[name="payment_method_id"]:checked');
            if (!checked || !instructions) { return; }
            var text = checked.dataset.instructions;
            instructions.textContent = text && text.trim() !== ''
                ? text
                : 'Send exactly ' + {{ (float) $booking->total_amount }} + ' to ' + checked.dataset.account + ' (' + checked.dataset.name + ') and keep the receipt.';
        }
        document.querySelectorAll('input[name="payment_method_id"]').forEach(function (radio) {
            radio.addEventListener('change', updateInstructions);
        });
        updateInstructions();
    });
</script>
@endpush
