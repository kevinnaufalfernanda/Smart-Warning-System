<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - FloodGuard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #E5E5EF; }
        
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
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #E5E5EF inset !important;
            -webkit-text-fill-color: black !important;
        }
    </style>
</head>
<body class="bg-[#E5E5EF] text-[#333] antialiased flex items-center justify-center min-h-screen relative overflow-hidden py-8">
    
    <div class="w-full max-w-[420px] bg-[#F3F3F3] rounded-[32px] p-[48px] mx-4 z-10 relative overflow-hidden shadow-[0_8px_40px_rgba(146,146,197,0.12)]">
        
        <!-- Header Logo -->
        <div class="text-center mb-[32px] mt-[8px]">
            <h1 class="text-[38px] text-[#9292C5] tracking-wide mb-[6px]" style="font-weight: 300;">FloodGuard</h1>
            <p class="text-[#9292C5] font-normal text-[14px] opacity-70">Daftar Akun Baru</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4 text-[13px]">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.process') }}" method="POST" class="flex flex-col gap-[24px] text-left">
            @csrf
            
            <!-- Input Nama -->
            <div class="flex flex-col gap-[8px]">
                <label class="text-[13px] font-semibold text-[#9292C5] flex items-center gap-[8px]">
                    Nama Lengkap
                </label>
                <input type="text" name="name" placeholder="Nama Warga" value="{{ old('name') }}" required class="w-full bg-[#E5E5EF] rounded-[12px] px-[18px] py-[14px] text-[14px] font-semibold text-black outline-none placeholder-[#999] border-none focus:ring-2 focus:ring-[#9292C5] transition-all">
            </div>

            <!-- Input Email -->
            <div class="flex flex-col gap-[8px]">
                <label class="text-[13px] font-semibold text-[#9292C5] flex items-center gap-[8px]">
                    Email
                </label>
                <input type="email" name="email" placeholder="email@warga.com" value="{{ old('email') }}" required class="w-full bg-[#E5E5EF] rounded-[12px] px-[18px] py-[14px] text-[14px] font-semibold text-black outline-none placeholder-[#999] border-none focus:ring-2 focus:ring-[#9292C5] transition-all">
            </div>

            <!-- Input Password -->
            <div class="flex flex-col gap-[8px]">
                <label class="text-[13px] font-semibold text-[#9292C5] flex items-center gap-[8px]">
                    Password
                </label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" required class="w-full bg-[#E5E5EF] rounded-[12px] px-[18px] py-[14px] text-[14px] font-semibold text-black outline-none placeholder-[#999] border-none focus:ring-2 focus:ring-[#9292C5] transition-all">
            </div>

            <!-- Submit -->
            <div class="text-center mt-[8px]">
                <button type="submit" class="w-[70%] bg-[#9292C5] text-white py-[14px] rounded-[14px] font-bold text-[15px] hover:bg-[#8585b8] transition-all shadow-sm hover:shadow-[0_4px_20px_rgba(146,146,197,0.4)]">
                    Daftar
                </button>
                <p class="text-[13px] font-medium text-[#999] mt-[16px] flex items-center justify-center gap-[6px]">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-[#9292C5] hover:underline font-semibold">Masuk</a>
                </p>
            </div>
        </form>
    </div>

</body>
</html>
