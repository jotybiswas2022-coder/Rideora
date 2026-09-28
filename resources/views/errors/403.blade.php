<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access denied · Rideora</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #FFF7F7; color: #1E293B; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        .wrap { max-width: 560px; width: 100%; text-align: center; background: #fff; border: 1px solid #FECACA; border-radius: 18px; padding: 48px 34px; box-shadow: 0 12px 34px rgba(220,38,38,.08); }
        .badge {
            width: 68px; height: 68px; border-radius: 50%; background: #FEF2F2; color: #DC2626; font-size: 1.9rem;
            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px;
        }
        .code { font-size: 3.4rem; font-weight: 800; letter-spacing: -.04em; color: #DC2626; line-height: 1; }
        h1 { font-size: 1.45rem; margin: 12px 0 8px; }
        p { color: #64748B; font-size: .95rem; margin-bottom: 26px; }
        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; border-radius: 8px; font-size: .92rem; font-weight: 600; text-decoration: none; border: 1px solid transparent; }
        .btn-primary { background: #2563EB; color: #fff; }
        .btn-outline { background: #fff; border-color: #E2E8F0; color: #1E293B; }
        .hint { margin-top: 26px; padding-top: 20px; border-top: 1px solid #E2E8F0; font-size: .82rem; color: #94A3B8; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="badge"><i class="bi bi-lock"></i></div>
        <div class="code">403</div>
        <h1>You do not have access to this area</h1>
        <p>This page is restricted. Sign in with the right account or return to a page you can access.</p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">Back to homepage</a>
            <a href="{{ url('/dashboard') }}" class="btn btn-outline">Go to my dashboard</a>
        </div>

        <div class="hint">Rideora — Your Ride, Your Way.</div>
    </div>
</body>
</html>
