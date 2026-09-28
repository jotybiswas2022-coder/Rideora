@php
    $iconValue = old('icon', $icon ?? '');
@endphp

<div class="form-group">
    <label for="icon">Icon <span class="muted">(optional)</span></label>

    <div class="icon-field">
        <span class="icon-preview" data-icon-preview>{!! category_icon($iconValue) !!}</span>
        <input type="text" id="icon" name="icon" list="bootstrap-icon-names" autocomplete="off"
               data-icon-input
               class="form-control @error('icon') is-invalid @enderror"
               value="{{ $iconValue }}" placeholder="car-front">
    </div>

    <datalist id="bootstrap-icon-names">
        @foreach([
            'car-front', 'car-front-fill', 'truck', 'truck-front', 'truck-flatbed', 'truck-front-fill',
            'bus-front', 'van', 'scooter', 'motorcycle', 'bicycle', 'train-front',
            'gem', 'lightning-charge', 'stars', 'snow', 'tools', 'shield-check', 'speedometer2',
            'fuel-pump', 'gear', 'grid', 'circle',
        ] as $suggestion)
            <option value="{{ $suggestion }}"></option>
        @endforeach
    </datalist>

    <span class="form-hint">
        A <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">Bootstrap Icons</a>
        name without the <code>bi-</code> prefix, e.g. <code>car-front</code>. Leave empty to use the default car icon.
    </span>

    @error('icon')<span class="form-error">{{ $message }}</span>@enderror
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.querySelector('[data-icon-input]');
        var preview = document.querySelector('[data-icon-preview]');

        if (!input || !preview) { return; }

        input.addEventListener('input', function () {
            var name = input.value.trim().toLowerCase().replace(/^bi-/, '').replace(/[^a-z0-9-]/g, '') || 'car-front';
            preview.innerHTML = '<i class="bi bi-' + name + '"></i>';
        });
    });
</script>
@endpush
