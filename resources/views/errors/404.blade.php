<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>403 â€” Access Denied</title>
<style>
:root {
    --bg: #0c274b;
    --accent: #00a5e3;
    --text-main: #ffffff;
    --text-muted: #d0d0d0;
    --button-bg: #ffffff;
    --button-text: #0c274b;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background: var(--bg);
    color: var(--text-main);
    font-family: "Segoe UI", Arial, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    padding: 20px;
}

.icon {
    font-size: 6rem;
    margin-bottom: 1rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

h1 {
    font-size: 10rem;
    font-weight: bold;
    color: var(--accent);
    margin-bottom: 0.5rem;
}

h2 {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
}

p {
    font-size: 1.2rem;
    color: var(--text-muted);
    max-width: 500px;
    margin-bottom: 2rem;
}

a.btn {
    display: inline-block;
    background: var(--button-bg);
    color: var(--button-text);
    padding: 0.8rem 1.6rem;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s ease;
}

a.btn:hover {
    background: var(--accent);
    color: #ffffff;
    transform: translateY(-3px);
}
</style>
</head>
<body>


<h1>404</h1>
<h2>Page Not Found</h2>
<p>
    There is no page here.
</p>

<a href="{{ url('/') }}" class="btn">Return Home</a>

</body>
</html>