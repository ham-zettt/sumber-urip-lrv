<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Toko Sumber Urip</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        (function() {
            try {
                if (localStorage.getItem('sumber_urip_theme') === 'light') {
                    document.documentElement.classList.add('light');
                } else {
                    document.documentElement.classList.remove('light');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-canvas text-ink font-sans antialiased flex items-center justify-center p-4 relative">
    <div class="absolute top-4 right-4">
        <button
            type="button"
            @click="$store.theme.toggle()"
            class="inline-flex items-center gap-2 rounded-lg bg-surface-1 border border-hairline px-3 py-1.5 text-xs font-medium text-ink-subtle hover:text-ink transition-colors cursor-pointer min-h-[36px]"
            :aria-label="$store.theme.mode === 'light' ? 'Beralih ke mode gelap' : 'Beralih ke mode terang'"
        >
            <template x-if="$store.theme.mode === 'light'">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <span>Mode Terang</span>
                </div>
            </template>
            <template x-if="$store.theme.mode !== 'light'">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <span>Mode Gelap</span>
                </div>
            </template>
        </button>
    </div>
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