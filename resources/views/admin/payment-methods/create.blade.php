@extends('admin.layouts.app')

@section('title', 'Add Payment Method')
@section('page-title', 'Add payment method')
@section('page-subtitle', 'Customers will send money to this account')

@push('styles')
<style>
    /* Add payment method — page specific */
    .aside-note {
        background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 16px;
    }
    .aside-note p { font-size: .83rem; color: var(--muted); }
    .aside-note p + p { margin-top: 10px; }
</style>
@endpush

@section('content')
    <div class="grid grid-sidebar">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Method details</h3>
                    <p>Account details appear on the payment page exactly as entered</p>
                </div>
                <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-light btn-sm">Back</a>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.payment-methods.store') }}" class="stack-16">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Method name <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required placeholder="e.g. bKash">
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="account_name">Account name <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="account_name" name="account_name"
                                   class="form-control @error('account_name') is-invalid @enderror"
                                   value="{{ old('account_name') }}" required placeholder="e.g. Rideora Rentals Ltd.">
                            @error('account_name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group full">
                            <label for="account_number">Account number / bank details <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="account_number" name="account_number"
                                   class="form-control @error('account_number') is-invalid @enderror"
                                   value="{{ old('account_number') }}" required placeholder="e.g. 01711-000001 or 1234 5678 9012 (City Bank)">
                            @error('account_number')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group full">
                            <label for="instructions">Payment instructions</label>
                            <textarea id="instructions" name="instructions" class="form-control"
                                      placeholder="Step by step instructions shown to the customer">{{ old('instructions') }}</textarea>
                            <span class="form-hint">Line breaks are preserved on the payment page.</span>
                            @error('instructions')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span style="color:var(--danger)">*</span></label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                            </select>
                            <span class="form-hint">Inactive methods are hidden from customers.</span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Create payment method</button>
                        <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Tips</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="aside-note">
                        <p>
                            Write instructions as numbered steps. Customers copy the account number into their bKash,
                            Nagad or bank app, then submit the transaction ID together with a screenshot.
                        </p>
                        <p>Keep the account number as text only — Rideora never collects card numbers or PINs.</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
