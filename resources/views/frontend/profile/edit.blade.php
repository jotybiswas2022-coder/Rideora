@extends('frontend.layouts.app')

@section('title', 'Edit Profile — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .edit-layout { display: grid; grid-template-columns: 1.6fr 1fr; gap: 26px; align-items: start; }
    .panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 26px; }
    .panel + .panel { margin-top: 22px; }
    .panel h2 { font-size: 1.08rem; margin-bottom: 6px; }
    .panel p.sub { color: var(--muted); font-size: .87rem; margin-bottom: 20px; }
    .avatar-upload { display: flex; gap: 18px; align-items: center; flex-wrap: wrap; }
    .avatar-preview {
        width: 84px; height: 84px; border-radius: 50%; overflow: hidden; background: var(--primary-soft);
        color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.4rem;
        flex-shrink: 0;
    }
    .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
    @media (max-width: 900px) {
        .edit-layout { grid-template-columns: 1fr; gap: 18px; }
    }

    @media (max-width: 620px) {
        .panel { padding: 18px; }
        .panel + .panel { margin-top: 16px; }
        .avatar-upload { gap: 14px; }
        .avatar-preview { width: 70px; height: 70px; font-size: 1.2rem; }
    }

    @media (max-width: 420px) {
        .avatar-upload { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Edit profile</h1>
        <p>Keep your contact details up to date so we can reach you about bookings.</p>
    </div>

    <div class="edit-layout">
        <div>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="panel">
                    <h2>Personal information</h2>
                    <p class="sub">Fields marked required must be filled in.</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Full name <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email address <span style="color:var(--danger)">*</span></label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone number <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}" required>
                            @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $user->city) }}">
                            @error('city')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group full">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $user->address) }}">
                            @error('address')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="driving_license_no">Driving licence number</label>
                            <input type="text" id="driving_license_no" name="driving_license_no"
                                   class="form-control @error('driving_license_no') is-invalid @enderror"
                                   value="{{ old('driving_license_no', $user->driving_license_no) }}"
                                   placeholder="Required for car and bike rentals">
                            @error('driving_license_no')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="avatar">Profile photo</label>
                            <div class="avatar-upload">
                                <span class="avatar-preview" data-avatar-preview>
                                    @if($user->avatar_path)
                                        <img src="{{ \App\Models\VehicleImage::publicUrl($user->avatar_path) }}" alt="{{ $user->name }}" data-avatar-image>
                                    @else
                                        {{ $user->initials() }}
                                    @endif
                                </span>
                                <div style="flex:1; min-width:180px;">
                                    <input type="file" id="avatar" name="avatar" class="form-control"
                                           accept="image/png,image/jpeg,image/webp" data-avatar-input>
                                    <span class="form-hint">JPG, PNG or WEBP · maximum 2 MB.</span>
                                    @error('avatar')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <h2>Change password</h2>
                    <p class="sub">Leave both fields empty to keep your current password.</p>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="current_password">Current password</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                            @error('current_password')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="password">New password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                            <span class="form-hint">Minimum 8 characters with letters and numbers.</span>
                            @error('password')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm new password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                                   autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-24">
                    <button type="submit" class="btn btn-primary btn-lg">Save changes</button>
                    <a href="{{ route('profile.index') }}" class="btn btn-light btn-lg">Cancel</a>
                </div>
            </form>
        </div>

        <aside>
            <div class="panel">
                <h2>Why we ask</h2>
                <p class="muted small">
                    Your phone number and driving licence help us verify you at pickup. Rideora never shares your
                    details with third parties — they are only used to manage your rentals.
                </p>
                <div class="divider"></div>
                <p class="muted small">
                    Uploading a photo is optional, but it makes handover at the pickup point faster.
                </p>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.querySelector('[data-avatar-input]');
        var preview = document.querySelector('[data-avatar-preview]');
        if (!input || !preview) { return; }

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) { return; }

            if (!file.type.startsWith('image/')) {
                alert('Please choose an image file.');
                input.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('The image must be smaller than 2 MB.');
                input.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                preview.innerHTML = '<img src="' + event.target.result + '" alt="Profile preview">';
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush
