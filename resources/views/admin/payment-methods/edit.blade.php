@extends('admin.layouts.app')

@section('title', 'Edit '.$method->name)
@section('page-title', 'Edit payment method')
@section('page-subtitle', $method->name.' · '.$method->payments()->count().' payments recorded')

@push('styles')
<style>
    /* Edit payment method — page specific */
    .preview-card {
        border: 1.5px solid var(--primary); background: var(--primary-soft); border-radius: var(--radius); padding: 16px;
    }
    .preview-card strong { display: block; font-size: .95rem; }
    .preview-card .acc {
        display: block; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 700; font-size: .88rem;
        color: var(--dark);
    }
    .preview-card .who { display: block; font-size: .82rem; color: var(--muted); }
    .instructions-preview { font-size: .84rem; white-space: pre-line; color: var(--text); }
</style>
@endpush

@section('content')
    <div class="grid grid-sidebar">
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Method details</h3>
                    <p>Changes apply to new payments immediately</p>
                </div>
                <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-light btn-sm">Back</a>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.payment-methods.update', $method) }}" class="stack-16">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Method name <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $method->name) }}" required>
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="account_name">Account name <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="account_name" name="account_name" class="form-control" value="{{ old('account_name', $method->account_name) }}" required>
                            @error('account_name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group full">
                            <label for="account_number">Account number / bank details <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="account_number" name="account_number" class="form-control" value="{{ old('account_number', $method->account_number) }}" required>
                            @error('account_number')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group full">
                            <label for="instructions">Payment instructions</label>
                            <textarea id="instructions" name="instructions" class="form-control">{{ old('instructions', $method->instructions) }}</textarea>
                            @error('instructions')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span style="color:var(--danger)">*</span></label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" @selected(old('status', $method->status) === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $method->status) === 'inactive')>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Preview</h3>
                        <p>How customers see this method</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="preview-card">
                        <strong>{{ $method->name }}</strong>
                        <span class="who">Account name: {{ $method->account_name }}</span>
                        <span class="acc">{{ $method->account_number }}</span>
                    </div>

                    <div class="divider"></div>

                    <p class="instructions-preview">
                        {{ $method->instructions ?: 'No custom instructions set — customers will see a generic prompt.' }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Usage</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="summary-list">
                        <div class="summary-row"><span>Total payments</span><strong>{{ $method->payments()->count() }}</strong></div>
                        <div class="summary-row"><span>Verified</span><strong>{{ $method->payments()->where('status', 'verified')->count() }}</strong></div>
                        <div class="summary-row"><span>Pending</span><strong>{{ $method->payments()->where('status', 'pending')->count() }}</strong></div>
                        <div class="summary-row total"><span>Verified volume</span><span>{{ bdt($method->payments()->where('status', 'verified')->sum('amount')) }}</span></div>
                    </div>

                    @if($method->payments()->exists())
                        <div class="alert alert-info mt-16 mb-8">
                            <span>&#8505;</span>
                            <div>This method has payment records, so it cannot be deleted. Set it to inactive instead.</div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.payment-methods.destroy', $method) }}" class="mt-16"
                              data-confirm="Delete {{ $method->name }}?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger-soft btn-block">Delete method</button>
                        </form>
                    @endif
                </div>
            </div>
        </aside>
    </div>
@endsection
