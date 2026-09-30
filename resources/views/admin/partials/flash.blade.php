@php
    $sweetAlerts = [];

    if (session('success')) {
        $sweetAlerts[] = ['icon' => 'success', 'message' => session('success')];
    }

    if (session('error')) {
        $sweetAlerts[] = ['icon' => 'error', 'message' => session('error')];
    }

    if (session('warning')) {
        $sweetAlerts[] = ['icon' => 'warning', 'message' => session('warning')];
    }

    if (session('info')) {
        $sweetAlerts[] = ['icon' => 'info', 'message' => session('info')];
    }
@endphp

@if($sweetAlerts)
    <div data-flash-messages="{{ json_encode($sweetAlerts) }}" hidden></div>

    <noscript>
        @foreach($sweetAlerts as $alert)
            <div class="alert alert-{{ $alert['icon'] === 'success' ? 'success' : ($alert['icon'] === 'warning' ? 'warning' : ($alert['icon'] === 'error' ? 'error' : 'info')) }}" role="alert">
                <span><i class="bi bi-info-lg"></i></span>
                <div>{{ $alert['message'] }}</div>
            </div>
        @endforeach
    </noscript>
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
