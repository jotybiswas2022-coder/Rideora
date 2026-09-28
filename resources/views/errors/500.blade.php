<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Something went wrong · Rideora</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #F8FAFC; color: #1E293B; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        .wrap { max-width: 580px; width: 100%; text-align: center; background: #fff; border: 1px solid #E2E8F0; border-radius: 18px; padding: 48px 34px; box-shadow: 0 12px 34px rgba(15,23,42,.07); }
        .badge {
            width: 68px; height: 68px; border-radius: 50%; background: #EFF6FF; color: #2563EB; font-size: 1.9rem;
            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px;
        }
        .code { font-size: 3.4rem; font-weight: 800; letter-spacing: -.04em; color: #0F172A; line-height: 1; }
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
        <div class="badge">&#128736;</div>
        <div class="code">500</div>
        <h1>Something went wrong on our side</h1>
        <p>We have logged the problem and our team is looking into it. Please try again in a moment.</p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">Back to homepage</a>
            <a href="{{ url('/contact') }}" class="btn btn-outline">Report the issue</a>
        </div>

        <div class="hint">Rideora — Your Ride, Your Way.</div>
    </div>
</body>
</html>
