<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - FloodGuard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #E5E5EF; }
        
        /* Checkbox kustom agar centangnya dipaksa putih (bukan hitam bawaan chrome) */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            background-color: transparent;
            border: 2px solid #C8C8E1;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .custom-checkbox:checked {
            background-color: #9292C5;
            border-color: #9292C5;
        }
        .custom-checkbox:checked::after {
            content: '';
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2.5px 2.5px 0;
            transform: rotate(45deg);
            margin-bottom: 2px;
        }

        /* Fix Chrome Autofill 'Belang' issue (memaksa bg menjadi putih) */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
            -webkit-text-fill-color: black !important;
        }
    </style>
</head>
<body class="bg-[#E5E5EF] text-[#333] antialiased flex items-center justify-center min-h-screen relative overflow-hidden">
    
    <div class="w-full max-w-[420px] bg-[#F3F3F3] rounded-[32px] p-[40px] mx-4 z-10 relative overflow-hidden text-center">
        
        <!-- Header Logo -->
        <div class="text-center mb-[40px] mt-[16px]">
            <h1 class="text-[36px] font-bold text-[#9292C5] tracking-wide mb-2">FloodGuard</h1>
            <p class="text-[#9292C5] font-semibold text-[14px] opacity-80">Silakan masuk ke akun Anda</p>
        </div>

        <form action="/dashboard" method="GET" class="flex flex-col gap-[20px] text-left">
            <!-- Input Email -->
            <div class="flex flex-col gap-[8px]">
                <label class="text-[13px] font-bold text-black">Email</label>
                <div class="flex items-center bg-white rounded-[16px] border-[2px] border-[#E5E5EF] focus-within:border-[#9292C5] transition-all overflow-hidden px-[16px]">
                    <svg class="w-[22px] h-[22px] text-[#9292C5]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    <input type="email" placeholder="admin@floodguard.com" required class="w-full bg-transparent px-[14px] py-[14px] text-[15px] font-bold text-black outline-none placeholder-[#C8C8E1]">
                </div>
            </div>

            <!-- Input Password -->
            <div class="flex flex-col gap-[8px] mt-[4px]">
                <label class="text-[13px] font-bold text-black">Password</label>
                <div class="flex items-center bg-white rounded-[16px] border-[2px] border-[#E5E5EF] focus-within:border-[#9292C5] transition-all overflow-hidden px-[16px]">
                    <svg class="w-[22px] h-[22px] text-[#9292C5]" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                    <input type="password" placeholder="••••••••" required class="w-full bg-transparent px-[14px] py-[14px] text-[15px] font-bold text-black outline-none placeholder-[#C8C8E1]">
                </div>
            </div>

            <!-- Extra -->
            <div class="flex items-center justify-between mt-[4px]">
                <label class="flex items-center gap-[8px] cursor-pointer">
                    <input type="checkbox" class="custom-checkbox w-[18px] h-[18px] shrink-0">
                    <span class="text-[13px] font-bold text-black">Ingat Saya</span>
                </label>
                <a href="#" class="text-[13px] font-bold text-[#9292C5] hover:underline">Lupa Password?</a>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-[#9292C5] text-white py-[16px] rounded-[16px] font-bold text-[15px] hover:bg-[#8585b8] mt-[16px] transition-all">
                Masuk ke Dashboard
            </button>
        </form>
    </div>

    <!-- Decorative Wavy Element in Background -->
    <div class="fixed bottom-0 left-0 right-0 h-[400px] pointer-events-none opacity-[0.5] z-0 overflow-hidden flex items-end">
        <svg viewBox="0 0 100 30" preserveAspectRatio="none" class="w-full h-full">
            <defs>
                <linearGradient id="waveGradLogin" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#9292C5" stop-opacity="0.8"></stop>
                    <stop offset="100%" stop-color="#9292C5" stop-opacity="0.0"></stop>
                </linearGradient>
            </defs>
            <!-- Wavy background to compliment FloodGuard branding -->
            <path d="M0,20 C15,20 25,10 50,15 C75,20 85,15 97,12 L100,12 L100,30 L0,30 Z" fill="url(#waveGradLogin)"/>
            <path d="M0,20 C15,20 25,10 50,15 C75,20 85,15 97,12 L100,12" fill="none" stroke="#6868af" stroke-width="0.2" stroke-linecap="round" />
        </svg>
    </div>
</body>
</html>
