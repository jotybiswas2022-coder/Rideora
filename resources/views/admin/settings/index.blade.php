@extends('admin.layouts.app')

@section('title', 'Website Settings')
@section('page-title', 'Website settings')
@section('page-subtitle', 'Contact details, social links and site copy')

@push('styles')
<style>
    /* Website settings — page specific */
    .footer-preview { background: var(--dark); color: #CBD5E1; border-radius: var(--radius); padding: 18px; }
    .footer-preview strong { color: #fff; display: block; font-size: 1rem; }
    .footer-preview .tagline { font-size: .8rem; color: #93C5FD; }
    .footer-preview .rows { margin-top: 12px; font-size: .8rem; display: grid; gap: 6px; }
    .card-body code { background: #F1F5F9; padding: 1px 6px; border-radius: 5px; font-size: .78rem; }
</style>
@endpush

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="stack-16">
        @csrf
        @method('PUT')

        <div class="grid grid-sidebar">
            <div>
                <!-- ============ General ============ -->
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h3>General</h3>
                            <p>Brand name, tagline and about copy</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="site_name">Site name <span style="color:var(--danger)">*</span></label>
                                <input type="text" id="site_name" name="site_name" class="form-control @error('site_name') is-invalid @enderror"
                                       value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                                @error('site_name')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="site_tagline">Tagline</label>
                                <input type="text" id="site_tagline" name="site_tagline" class="form-control"
                                       value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
                                @error('site_tagline')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="currency_symbol">Currency symbol <span style="color:var(--danger)">*</span></label>
                                <input type="text" id="currency_symbol" name="currency_symbol" class="form-control"
                                       value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}" required>
                                @error('currency_symbol')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="booking_advance_percent">Advance notice (hours)</label>
                                <input type="number" min="0" max="100" id="booking_advance_percent" name="booking_advance_percent"
                                       class="form-control"
                                       value="{{ old('booking_advance_percent', $settings['booking_advance_percent'] ?? 0) }}">
                                <span class="form-hint">Shown as a policy note on the booking page.</span>
                                @error('booking_advance_percent')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group full">
                                <label for="about_content">About text (homepage / about page)</label>
                                <textarea id="about_content" name="about_content" class="form-control">{{ old('about_content', $settings['about_content'] ?? '') }}</textarea>
                                @error('about_content')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ Contact ============ -->
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h3>Contact information</h3>
                            <p>Used in the footer, contact page and notification emails</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="support_email">Support email <span style="color:var(--danger)">*</span></label>
                                <input type="email" id="support_email" name="support_email" class="form-control @error('support_email') is-invalid @enderror"
                                       value="{{ old('support_email', $settings['support_email'] ?? '') }}" required>
                                @error('support_email')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="support_phone">Support phone <span style="color:var(--danger)">*</span></label>
                                <input type="text" id="support_phone" name="support_phone" class="form-control @error('support_phone') is-invalid @enderror"
                                       value="{{ old('support_phone', $settings['support_phone'] ?? '') }}" required>
                                @error('support_phone')<span class="form-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group full">
                                <label for="office_address">Office address</label>
                                <input type="text" id="office_address" name="office_address" class="form-control"
                                       value="{{ old('office_address', $settings['office_address'] ?? '') }}">
                                @error('office_address')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ Social ============ -->
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h3>Social links</h3>
                            <p>Displayed in the website footer</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-grid">
                            @foreach(['facebook_url' => 'Facebook', 'instagram_url' => 'Instagram', 'twitter_url' => 'X (Twitter)', 'youtube_url' => 'YouTube'] as $key => $label)
                                <div class="form-group">
                                    <label for="{{ $key }}">{{ $label }}</label>
                                    <input type="url" id="{{ $key }}" name="{{ $key }}"
                                           class="form-control @error($key) is-invalid @enderror"
                                           value="{{ old($key, $settings[$key] ?? '') }}" placeholder="https://">
                                    @error($key)<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">Save settings</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg">Cancel</a>
                </div>
            </div>

            <!-- ============ Sidebar ============ -->
            <aside>
                <div class="card">
                    <div class="card-head">
                        <div>
                            <h3>Live preview</h3>
                            <p>Footer as customers see it</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="footer-preview">
                            <strong>{{ $settings['site_name'] ?? 'Rideora' }}</strong>
                            <span class="tagline">{{ $settings['site_tagline'] ?? '' }}</span>
                            <div class="rows">
                                <span>&#9742; {{ $settings['support_phone'] ?? '' }}</span>
                                <span>&#9993; {{ $settings['support_email'] ?? '' }}</span>
                                <span>&#128205; {{ \Illuminate\Support\Str::limit($settings['office_address'] ?? '', 80) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div>
                            <h3>Notes</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="small muted">
                            Settings are cached for performance and refreshed automatically whenever you save this form.
                        </p>
                        <div class="divider"></div>
                        <p class="small muted">
                            Payment account numbers are managed on the
                            <a href="{{ route('admin.payment-methods.index') }}">payment methods</a> page.
                        </p>
                        <div class="divider"></div>
                        <p class="small muted">
                            Admin credentials come from the <code>RIDEORA_ADMIN_EMAIL</code> and
                            <code>RIDEORA_ADMIN_PASSWORD</code> environment variables.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </form>
@endsection
