<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FloodGuard - @yield('title', 'Beranda')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
            }
        </script>
    @endif
    <style>
        body { font-family: 'Poppins', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #9292C5; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #9292C5; }
        ::-webkit-scrollbar-thumb:hover { background: #7b7bb2; }
        /* Dropdown fade in */
        .dropdown-open { display: block; animation: fadeIn 0.15s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        /* Efek Shadow Melebar (Pulse Ring) */
        @keyframes pulse-shadow-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
            70% { box-shadow: 0 0 0 12px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        .animate-pulse-green {
            animation: pulse-shadow-green 1.5s infinite;
        }
        /* Animasi Tangki Air / Liquid Wave */
        @keyframes liquid-wave {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .animate-liquid {
            animation: liquid-wave 4s linear infinite;
        }
    </style>
    <!-- Library Eksternal (Chart.js / ApexCharts) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Dark Mode Init -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine JS for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    

</head>
<body class="bg-[#E5E5EF] dark:bg-[#1a1b24] text-[#333] dark:text-[#d1d1d6] antialiased transition-colors duration-300">
    <div class="flex h-screen overflow-hidden w-full">
        
        <!-- Sidebar -->
        @php
            $currentRoute = request()->path(); // or Route::currentRouteName()
        @endphp
        
        <aside class="w-[280px] bg-[#F3F3F3] dark:bg-[#20212a] flex flex-col sticky top-0 h-screen rounded-tr-[32px] rounded-br-[32px] overflow-hidden z-20 shrink-0 border-r border-transparent dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300">
            <!-- Logo -->
            <div class="px-8 pt-[40px] pb-[40px] flex items-center justify-start">
                <h1 class="text-[26px] font-bold text-[#9292C5] tracking-wide">FloodGuard</h1>
            </div>

            <!-- Navigation menus -->
            <div class="flex-1 px-[20px] py-[16px] overflow-y-auto w-full">
                <nav class="space-y-8 flex flex-col gap-[8px]">
                    <div>
                        <p class="px-[12px] text-[12px] font-bold text-[#9292C5] dark:text-[#a5a5d1] mb-[12px] tracking-widest uppercase opacity-80">Menu Utama</p>
                        <ul class="space-y-[4px]">
                            <li class="relative">
                                <div class="absolute left-[-20px] top-1/2 transform -translate-y-1/2 w-[6px] h-[34px] bg-[#9292C5] rounded-r-[6px] transition-all duration-300 {{ $currentRoute == 'dashboard' ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-0' }}"></div>
                                <a href="/dashboard" class="w-full flex items-center gap-[16px] px-[16px] py-[12px] rounded-[16px] font-semibold text-[15px] transition-all {{ $currentRoute == 'dashboard' ? 'bg-[#9292C5] text-white' : 'text-[#9292C5] dark:text-[#a5a5d1] hover:bg-[#9292C5]/10 dark:hover:bg-[rgba(255,255,255,0.05)] opacity-70 hover:opacity-100' }}">
                                    <svg class="h-[22px] w-[22px]" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                    Dashboard
                                </a>
                            </li>
                            <li class="relative">
                                <div class="absolute left-[-20px] top-1/2 transform -translate-y-1/2 w-[6px] h-[34px] bg-[#9292C5] rounded-r-[6px] transition-all duration-300 {{ $currentRoute == 'peringatan' ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-0' }}"></div>
                                <a href="/peringatan" class="w-full flex items-center gap-[16px] px-[16px] py-[12px] rounded-[16px] font-semibold text-[15px] transition-all {{ $currentRoute == 'peringatan' ? 'bg-[#9292C5] text-white' : 'text-[#9292C5] dark:text-[#a5a5d1] hover:bg-[#9292C5]/10 dark:hover:bg-[rgba(255,255,255,0.05)] opacity-70 hover:opacity-100' }}">
                                    <svg class="h-[22px] w-[22px] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                    Peringatan
                                </a>
                            </li>
                            <li class="relative">
                                <div class="absolute left-[-20px] top-1/2 transform -translate-y-1/2 w-[6px] h-[34px] bg-[#9292C5] rounded-r-[6px] transition-all duration-300 {{ $currentRoute == 'perangkat' ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-0' }}"></div>
                                <a href="/perangkat" class="w-full flex items-center gap-[16px] px-[16px] py-[12px] rounded-[16px] font-semibold text-[15px] transition-all {{ $currentRoute == 'perangkat' ? 'bg-[#9292C5] text-white' : 'text-[#9292C5] dark:text-[#a5a5d1] hover:bg-[#9292C5]/10 dark:hover:bg-[rgba(255,255,255,0.05)] opacity-70 hover:opacity-100' }}">
                                    <svg class="h-[22px] w-[22px] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16H3v-2h18v2zm0-5H3V9h18v2zm0-5H3V4h18v2z"/></svg>
                                    Perangkat
                                </a>
                            </li>
                            <li class="relative">
                                <div class="absolute left-[-20px] top-1/2 transform -translate-y-1/2 w-[6px] h-[34px] bg-[#9292C5] rounded-r-[6px] transition-all duration-300 {{ $currentRoute == 'riwayat' ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-0' }}"></div>
                                <a href="/riwayat" class="w-full flex items-center gap-[16px] px-[16px] py-[12px] rounded-[16px] font-semibold text-[15px] transition-all {{ $currentRoute == 'riwayat' ? 'bg-[#9292C5] text-white' : 'text-[#9292C5] dark:text-[#a5a5d1] hover:bg-[#9292C5]/10 dark:hover:bg-[rgba(255,255,255,0.05)] opacity-70 hover:opacity-100' }}">
                                    <svg class="h-[22px] w-[22px] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                                    Riwayat Data
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <p class="px-[12px] text-[12px] font-bold text-[#9292C5] dark:text-[#a5a5d1] mb-[12px] tracking-widest uppercase opacity-80">Konfigurasi</p>
                        <ul class="space-y-[4px]">
                            <li class="relative">
                                <div class="absolute left-[-20px] top-1/2 transform -translate-y-1/2 w-[6px] h-[34px] bg-[#9292C5] rounded-r-[6px] transition-all duration-300 {{ $currentRoute == 'pengaturan' ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-0' }}"></div>
                                <a href="/pengaturan" class="w-full flex items-center gap-[16px] px-[16px] py-[12px] rounded-[16px] font-semibold text-[15px] transition-all {{ $currentRoute == 'pengaturan' ? 'bg-[#9292C5] text-white' : 'text-[#9292C5] dark:text-[#a5a5d1] hover:bg-[#9292C5]/10 dark:hover:bg-[rgba(255,255,255,0.05)] opacity-70 hover:opacity-100' }}">
                                    <svg class="h-[22px] w-[22px] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.73 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .43-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.49-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                                    Pengaturan
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- Profile -->
            <div class="px-[24px] py-[28px] border-t border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] flex items-center justify-between mx-[16px] mt-4 transition-colors">
                <div class="flex items-center gap-[12px]">
                    <div class="w-[42px] h-[42px] bg-[#9292C5] text-[#F3F3F3] flex items-center justify-center font-bold text-[18px] rounded-[10px] shrink-0">A</div>
                    <div>
                        <p class="text-[15px] font-bold text-[#9292C5] dark:text-[#a5a5d1] leading-tight mb-0.5">Admin</p>
                        <p class="text-[12px] text-[#9292C5] dark:text-[#a5a5d1] font-medium opacity-70">Administrator</p>
                    </div>
                </div>
                <!-- Logout icon -->
                <a href="/" class="text-[#9292C5] dark:text-[#a5a5d1] hover:text-[#9292C5] transition-colors ml-2 opacity-70 hover:opacity-100">
                    <svg class="w-[20px] h-[20px]" fill="currentColor" viewBox="0 0 24 24"><path d="M16 13v-2H7V8l-5 4 5 4v-3h9zM20 3H9c-1.1 0-2 .9-2 2v3h2V5h11v14H9v-3H7v3c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 w-full flex flex-col relative z-0 h-screen overflow-hidden">
            
            <div class="px-[32px] md:px-[40px] pt-[32px] flex-shrink-0 transition-colors duration-300">
                <!-- Global Top Header -->
                <header class="flex items-center justify-between bg-[#F3F3F3] dark:bg-[#20212a] rounded-[20px] px-[28px] py-[16px] mb-[24px] transition-colors duration-300 border border-transparent dark:border-[rgba(255,255,255,0.05)]">
                    <h2 class="text-[22px] font-bold text-black dark:text-white tracking-tight">@yield('title')</h2>
                    
                    <div class="flex items-center gap-[20px] relative" x-data="{ open: false }">
                        
                        <!-- Theme Toggle Script using Vanilla JS inline -->
                        <button id="theme-toggle" class="w-[42px] h-[42px] bg-white dark:bg-[#2e2f3a] text-[#9292C5] dark:text-[#a5a5d1] rounded-[14px] flex items-center justify-center transition-all shadow-[0_4px_16px_rgba(146,146,197,0.35)] dark:shadow-[0_4px_16px_rgba(100,100,160,0.2)] hover:scale-105 cursor-pointer">
                            <svg id="theme-toggle-dark-icon" class="w-[22px] h-[22px] hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                            <svg id="theme-toggle-light-icon" class="w-[22px] h-[22px] hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        </button>

                        <!-- Aman Badge Indicator (Static) -->
                        <div class="px-[24px] py-[8px] bg-white dark:bg-[#344034] rounded-[14px] font-bold text-[#22c55e] tracking-wide text-[16px] select-none flex items-center gap-2 shadow-[0_4px_16px_rgba(34,197,94,0.4)] animate-pulse-green" title="Sistem Berjalan Normal">
                            Aman
                        </div>
                    </div>
                </header>
            </div>

            <!-- Content Area (Scrollable) -->
            <div class="px-[32px] md:px-[40px] pb-[32px] flex-1 overflow-y-auto w-full transition-colors duration-300 relative z-0">
                @yield('content')
            </div>

        </main>
    </div>

    <!-- Theme Script -->
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                // if NOT set via local storage previously
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            
        });
    </script>
    
    @stack('scripts')
</body>
</html>
