@php $bleed = $__bleed ?? false; @endphp

@if($bleed)
    <div class="container" style="padding-top: 20px;">
@endif

@if(session('success'))
    <div class="alert alert-success" role="status">
        <span><i class="bi bi-check-lg"></i></span>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" role="alert">
        <span><i class="bi bi-exclamation-triangle-fill"></i></span>
        <div>{{ session('error') }}</div>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning" role="alert">
        <span><i class="bi bi-exclamation-triangle-fill"></i></span>
        <div>{{ session('warning') }}</div>
    </div>
@endif

@if(session('status'))
    <div class="alert alert-info" role="status">
        <span><i class="bi bi-info-circle-fill"></i></span>
        <div>{{ session('status') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error" role="alert">
        <span><i class="bi bi-exclamation-triangle-fill"></i></span>
        <div>
            <strong>Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if($bleed)
    </div>
@endif
