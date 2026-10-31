<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sumber Urip - @yield('title', 'Kasir')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-canvas text-ink font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        @php
            $menus = [
                ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>'],
                ['name' => 'Kasir', 'route' => 'kasir.index', 'icon' => '<path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path>'],
                ['name' => 'Transaksi', 'route' => 'transaksi.index', 'icon' => '<polyline points="16 2 16 11 12 16 8 11 8 2 20 2"></polyline><path d="M18 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>'],
                ['name' => 'Produk', 'route' => 'produk.index', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>'],
                ['name' => 'Stok', 'route' => 'stok.index', 'icon' => '<line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>'],
            ];
        @endphp

        <!-- Mobile topbar -->
        <header class="md:hidden sticky top-0 z-30 h-16 bg-surface-1 border-b border-hairline flex items-center justify-between px-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-primary"></div>
                <span class="text-sm font-semibold text-ink tracking-tight">Sumber Urip</span>
            </a>
            <button type="button" class="text-ink-subtle hover:text-ink transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" @click="sidebarOpen = true" aria-label="Buka menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </header>

        <div class="md:flex md:h-screen">
            <!-- Desktop sidebar -->
            <aside class="hidden md:flex w-64 bg-surface-1 border-r border-hairline flex-col shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 p-6 border-b border-hairline">
                    <div class="w-4 h-4 rounded-full bg-primary"></div>
                    <span class="text-sm font-semibold text-ink tracking-tight">Sumber Urip</span>
                </a>
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    @foreach ($menus as $menu)
                        <a href="{{ route($menu['route']) }}" class="{{ request()->routeIs(explode('.', $menu['route'])[0].'.*') ? 'bg-surface-2 text-ink' : 'text-ink-muted hover:text-ink hover:bg-surface-2' }} flex items-center gap-3 rounded-md px-3 py-3 font-medium text-sm transition-colors min-h-[44px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $menu['icon'] !!}</svg>
                            <span>{{ $menu['name'] }}</span>
                        </a>
                    @endforeach
                </nav>
                <div class="border-t border-hairline p-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-left text-sm font-medium text-ink-muted hover:text-ink transition-colors min-h-[44px]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Mobile drawer -->
            <div x-show="sidebarOpen" x-transition.opacity x-cloak class="md:hidden fixed inset-0 bg-black/80 z-40" @click="sidebarOpen = false"></div>
            <div x-show="sidebarOpen" x-cloak class="md:hidden fixed inset-y-0 left-0 z-50 w-64 bg-surface-1 border-r border-hairline flex flex-col transition-transform duration-300" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                <div class="flex items-center justify-between p-6 border-b border-hairline">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-primary"></div>
                        <span class="text-sm font-semibold text-ink tracking-tight">Sumber Urip</span>
                    </div>
                    <button type="button" class="text-ink-subtle hover:text-ink min-h-[44px] min-w-[44px] flex items-center justify-center" @click="sidebarOpen = false" aria-label="Tutup menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    @foreach ($menus as $menu)
                        <a href="{{ route($menu['route']) }}" class="{{ request()->routeIs(explode('.', $menu['route'])[0].'.*') ? 'bg-surface-2 text-ink' : 'text-ink-muted hover:text-ink hover:bg-surface-2' }} flex items-center gap-3 rounded-md px-3 py-3 font-medium text-sm transition-colors min-h-[44px]" @click="sidebarOpen = false">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $menu['icon'] !!}</svg>
                            <span>{{ $menu['name'] }}</span>
                        </a>
                    @endforeach
                </nav>
                <div class="border-t border-hairline p-4">
                    <form method="POST" action="{{ route('logout') }}" class="text-left">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-sm font-medium text-ink-muted hover:text-ink transition-colors min-h-[44px]" @click="sidebarOpen = false">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main content -->
            <div class="flex-1 flex flex-col min-w-0 md:overflow-hidden">
                <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                    @if (session('success'))
                        <div class="mb-4 p-3 rounded-md bg-success/10 border border-success/20 text-success text-sm flex items-center gap-2">
                            <span class="flex-shrink-0">✓</span>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-md bg-danger/10 border border-danger/20 text-danger text-sm">
                            <strong class="font-medium">Terjadi kesalahan:</strong>
                            <ul class="mt-1 list-disc list-inside text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>