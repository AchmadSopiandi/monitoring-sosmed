<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIMAK-BAPENDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
            --brand: #0f94d6;
            --brand-dark: #0b75aa;
            --text: #172033;
            --muted: #667085;
            --border: #dbe5f2;
        }

        * { box-sizing: border-box; }
        body {
            background:
                linear-gradient(rgba(246, 249, 252, .18), rgba(237, 244, 251, .18)),
                url('{{ route('login.pattern') }}') center / 280px auto repeat fixed;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            position: relative;
        }

        .login-card {
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(6px);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 18px 46px rgba(15, 23, 42, .10);
            max-width: 420px;
            padding: 28px;
            position: relative;
            width: 100%;
            z-index: 1;
        }

        .brand {
            align-items: center;
            display: flex;
            font-size: 20px;
            font-weight: 800;
            gap: 10px;
            margin-bottom: 22px;
        }

        .brand i { color: #2ee6a6; }
        .brand span span { color: var(--brand); }
        h1 { font-size: 26px; margin: 0 0 6px; }
        .subtitle { color: var(--muted); margin: 0 0 22px; }
        form { display: grid; gap: 14px; }
        label {
            color: #111827;
            display: grid;
            font-size: 14px;
            font-weight: 700;
            gap: 8px;
        }

        input {
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font: inherit;
            min-height: 44px;
            padding: 0 12px;
            width: 100%;
        }

        input:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .16);
            outline: none;
        }

        .remember {
            align-items: center;
            color: var(--muted);
            display: flex;
            font-size: 14px;
            font-weight: 600;
            gap: 9px;
        }

        .remember input {
            min-height: auto;
            width: auto;
        }

        button {
            align-items: center;
            background: var(--brand);
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-weight: 800;
            gap: 8px;
            justify-content: center;
            min-height: 44px;
            padding: 0 16px;
        }

        button:hover { background: var(--brand-dark); }
        .error, .alert {
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
            padding: 12px 14px;
        }

        .error { background: #fee2e2; color: #991b1b; }
        .alert { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="brand">
            <i class="bi bi-pie-chart-fill"></i>
            <span>SIMAK<span>-BAPENDA</span></span>
        </div>

        <h1>Login Dashboard</h1>
        <p class="subtitle">Masuk untuk mengakses monitoring sosial media.</p>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            </label>

            <label>
                Password
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                Ingat saya
            </label>

            <button type="submit"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
        </form>
    </main>
</body>
</html>
