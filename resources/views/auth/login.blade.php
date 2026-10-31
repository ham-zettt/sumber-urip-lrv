<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Toko Sumber Urip</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-canvas text-ink font-sans antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="flex flex-col items-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-surface-1 border border-hairline flex items-center justify-center mb-4">
                <div class="w-5 h-5 rounded-full bg-primary"></div>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">Toko Sumber Urip</h1>
            <p class="text-sm text-ink-subtle mt-1">Masuk untuk mulai mencatat penjualan</p>
        </div>

        <div class="bg-surface-1 border border-hairline rounded-lg p-6 md:p-8">
            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="p-3 rounded-md bg-danger/10 border border-danger/20 text-danger text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label for="username" class="label">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username" class="input" placeholder="Masukkan username">
                </div>

                <div>
                    <label for="password" class="label">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="input" placeholder="Masukkan password">
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-on-primary font-medium text-sm rounded-md px-4 py-3 min-h-[44px] transition-colors">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-ink-tertiary mt-6">Aplikasi Kasir &amp; Pencatatan Penjualan</p>
    </div>
</body>
</html>