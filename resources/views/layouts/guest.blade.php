<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Member login – Liverpool RAYNET</title>

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top, #020617 0, #000 60%);
            color: #e5e7eb;
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .card {
            background: #020617;
            border-radius: 0.75rem;
            border: 1px solid rgba(148,163,184,0.4);
            padding: 2rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        a {
            color: #38bdf8;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        {{ $slot }}
    </div>
</div>
</body>
</html>