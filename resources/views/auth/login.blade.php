<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIMAK-BAPENDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; --teal: #1f6f73; --teal-dark: #195c60; --text: #253041; --muted: #718096; --surface: #f5f8fc; --border: #dbe4ef; }
        * { box-sizing: border-box; }
        body { background: var(--surface); color: var(--text); margin: 0; min-height: 100vh; }
        .login-page { display: grid; grid-template-columns: minmax(420px, 1.05fr) minmax(480px, 1fr); min-height: 100vh; }
        .welcome-panel { background: radial-gradient(circle at 100% 0%, rgba(117, 214, 190, .28), transparent 35%), linear-gradient(145deg, #195c60 0%, var(--teal) 55%, #164f60 100%); color: #fff; display: flex; flex-direction: column; justify-content: center; overflow: hidden; padding: clamp(42px, 8vw, 96px); position: relative; }
        .welcome-panel::after { border: 1px solid rgba(255, 255, 255, .12); border-radius: 50%; content: ""; height: 460px; position: absolute; right: -220px; top: -160px; width: 460px; }
        .welcome-panel h1 { font-size: clamp(38px, 4.6vw, 62px); letter-spacing: -.8px; line-height: 1.08; margin: 0; max-width: 580px; position: relative; z-index: 1; }
        .welcome-panel h1 span { color: #b8f1ed; display: block; font-size: .56em; letter-spacing: 0; line-height: 1.2; margin-top: 12px; }
        .welcome-panel p { color: #d6f3f2; font-size: 17px; font-weight: 600; line-height: 1.45; margin: 24px 0 0; max-width: 460px; position: relative; z-index: 1; }
        .partner-logos { display: flex; gap: 20px; margin-bottom: 42px; position: relative; z-index: 1; }
        .partner-logo { align-items: center; background: #fff; border-radius: 18px; box-shadow: 0 10px 22px rgba(7, 47, 50, .16); display: flex; height: 112px; justify-content: center; padding: 12px; width: 112px; }
        .partner-logo img { display: block; max-height: 100%; max-width: 100%; object-fit: contain; }
        .form-panel { align-items: center; display: flex; justify-content: center; padding: 40px; }
        .login-card { max-width: 480px; width: 100%; }
        .form-eyebrow { color: var(--teal); font-size: 15px; font-weight: 800; letter-spacing: .4px; margin: 0 0 22px; text-align: center; text-transform: uppercase; }
        .login-card h2 { font-size: 37px; letter-spacing: -.5px; margin: 0; text-align: center; }
        .login-card > .subtitle { color: var(--muted); font-size: 16px; margin: 14px 0 32px; text-align: center; }
        form { display: grid; gap: 22px; }
        label { color: #344054; display: grid; font-size: 15px; gap: 8px; }
        input[type="email"], input[type="password"] { background: #fff; border: 1px solid var(--border); border-radius: 9px; color: var(--text); font: inherit; min-height: 54px; padding: 0 15px; width: 100%; }
        input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(31, 111, 115, .16); outline: none; }
        .remember { align-items: center; color: #475467; display: flex; font-size: 15px; gap: 10px; margin-top: -3px; }
        .remember input { accent-color: var(--teal); height: 18px; margin: 0; width: 18px; }
        button { background: var(--teal); border: 0; border-radius: 9px; color: #fff; cursor: pointer; font: inherit; font-weight: 800; min-height: 56px; transition: background .15s ease, transform .15s ease; }
        button:hover { background: var(--teal-dark); transform: translateY(-1px); }
        .login-note { color: var(--muted); font-size: 14px; line-height: 1.5; margin: 26px 0 0; text-align: center; }
        .notification-stack { align-items: center; background: rgba(15, 23, 42, .18); display: flex; inset: 0; justify-content: center; padding: 16px; position: fixed; z-index: 1000; }
        .notification { align-items: center; animation: notification-in .35s ease-out both; border: 1px solid transparent; border-radius: 14px; box-shadow: 0 18px 45px rgba(31, 41, 55, .2); display: flex; flex-direction: column; font-size: 14px; gap: 12px; max-width: 515px; min-height: 270px; padding: 24px; position: relative; text-align: center; width: 100%; }
        .notification.is-leaving { animation: notification-out .25s ease-in forwards; }
        .notification > i { align-items: center; background: #d1fad0; border-radius: 50%; color: #58ed38; display: flex; font-size: 48px; height: 124px; justify-content: center; margin-top: -8px; width: 124px; }
        .notification.info > i { background: #dbeafe; color: #3b82f6; }
        .notification-content { line-height: 1.45; }
        .notification.success .notification-content { color: #202020; font-size: 22px; font-weight: 500; text-transform: uppercase; }
        .notification.success .notification-content::after { color: #999; content: "Selamat datang di sistem"; display: block; font-size: 14px; margin-top: 8px; text-transform: none; }
        .notification-close { background: transparent; border: 0; color: currentColor; cursor: pointer; font-size: 22px; line-height: 1; min-height: auto; opacity: .45; padding: 0; position: absolute; right: 14px; top: 10px; }
        .notification-close:hover { background: transparent; opacity: 1; transform: none; }
        .notification.success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
        .notification.error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        .notification.info { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
        @keyframes notification-in { from { opacity: 0; transform: scale(.88) translateY(14px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes notification-out { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(.92); } }
        @media (prefers-reduced-motion: reduce) { .notification { animation: none; } }
        @media (max-width: 800px) { .login-page { display: block; } .welcome-panel { min-height: 300px; padding: 44px 28px; } .partner-logos { margin-bottom: 26px; } .partner-logo { height: 88px; width: 88px; } .welcome-panel h1 { font-size: 38px; } .welcome-panel p { font-size: 15px; margin-top: 14px; } .form-panel { padding: 44px 24px; } }
    </style>
</head>
<body>
    <main class="login-page">
        <section class="welcome-panel" aria-label="Informasi aplikasi">
            <div class="partner-logos" aria-label="Logo Bapenda Kota Bandung dan Itenas">
                <span class="partner-logo"><img src="{{ route('login.logo', ['logo' => 'logo-bapenda.png']) }}" alt="Bapenda Kota Bandung"></span>
                <span class="partner-logo"><img src="{{ route('login.logo', ['logo' => 'logo-itenas.png']) }}" alt="Itenas"></span>
            </div>
            <h1>SIMAK-BAPENDA <span>Sistem Informasi Monitoring Analisis Komentar</span></h1>
            <p>Pantau percakapan Instagram Bapenda Kota Bandung dalam satu dashboard.</p>
        </section>
        <section class="form-panel">
            <div class="login-card">
                <p class="form-eyebrow">Panel Admin</p>
                <h2>Masuk ke akun anda</h2>
                <p class="subtitle">Akses dashboard monitoring sosial media.</p>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
                    <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
                    <label class="remember"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    <button type="submit"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
                </form>
                <p class="login-note">Khusus untuk admin yang terdaftar.</p>
            </div>
        </section>
    </main>
    @if (session('success') || session('status') || $errors->any())
    <div class="notification-stack" aria-live="polite" aria-atomic="true">
        @if (session('success'))
            <div class="notification success" role="status">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <span class="notification-content">{{ session('success') }}</span>
                <button type="button" class="notification-close" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif
        @if (session('status'))
            <div class="notification info" role="status">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <span class="notification-content">{{ session('status') }}</span>
                <button type="button" class="notification-close" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif
        @if ($errors->any())
            <div class="notification error" role="alert">
                <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                <span class="notification-content">{{ $errors->first() }}</span>
                <button type="button" class="notification-close" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif
    </div>
    @endif
    <script>
        document.querySelectorAll('.notification').forEach((notification) => {
            const stack = notification.closest('.notification-stack');
            let dismissed = false;

            const removeNotification = () => {
                notification.remove();

                if (! stack?.querySelector('.notification')) {
                    stack?.remove();
                }
            };

            const dismiss = () => {
                if (dismissed) {
                    return;
                }

                dismissed = true;
                notification.classList.add('is-leaving');
                notification.addEventListener('animationend', removeNotification, { once: true });
                window.setTimeout(removeNotification, 350);
            };

            notification.querySelector('.notification-close').addEventListener('click', dismiss);
            window.setTimeout(dismiss, 4000);
        });
    </script>
</body>
</html>
