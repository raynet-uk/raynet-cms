<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Suspended — {{ \App\Helpers\RaynetSetting::groupName() }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --navy: #003366;
            --red: #C8102E;
            --light: #F2F2F2;
            --border: #D0D0D0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            background: var(--light);
            color: var(--navy);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .card {
            background: white;
            border: 1px solid var(--border);
            border-top: 4px solid var(--red);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0,51,102,0.1);
        }
        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        h1 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: var(--red);
        }
        p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #444;
            margin-bottom: 1.5rem;
        }
        .message-box {
            background: #fff4f4;
            border: 1px solid #fca5a5;
            border-radius: 4px;
            padding: 1rem;
            font-size: 0.9rem;
            color: #7f1d1d;
            margin-bottom: 2rem;
            text-align: left;
        }
        form button {
            padding: 0.6rem 1.5rem;
            background: var(--navy);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-family: inherit;
        }
        form button:hover { background: #002244; }
        .contact {
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔒</div>
        <h1>Account Suspended</h1>
        <p>Your account has been temporarily suspended by an administrator.</p>
        <div class="message-box">
            {{ $message }}
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Sign out</button>
        </form>
        <div class="contact">
            If you believe this is an error, please contact a group administrator.
        </div>
    </div>
</body>
</html>
