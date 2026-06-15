<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - FloodGuard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0f0f5; }
        
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 15s infinite alternate ease-in-out; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #E5E5EF inset !important;
            -webkit-text-fill-color: black !important;
        }
    </style>
</head>
<body class="bg-[#f4f4f9] text-[#333] antialiased flex items-center justify-center min-h-screen relative overflow-hidden">
    
    <!-- Particle Swarm Follower -->
    <canvas id="particle-canvas" class="absolute inset-0 z-0 w-full h-full pointer-events-none"></canvas>

    <!-- Minimalist Ambient Background -->
    <div class="absolute top-[0%] left-[-10%] w-[500px] h-[500px] bg-[#9292C5] rounded-full mix-blend-multiply filter blur-[120px] opacity-40 animate-blob z-0"></div>
    <div class="absolute top-[10%] right-[-5%] w-[400px] h-[400px] bg-[#C8C8E1] rounded-full mix-blend-multiply filter blur-[120px] opacity-50 animate-blob animation-delay-2000 z-0"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-[600px] h-[600px] bg-[#A5A5D1] rounded-full mix-blend-multiply filter blur-[120px] opacity-40 animate-blob animation-delay-4000 z-0"></div>

    <!-- Glassmorphism Box -->
    <div class="w-full max-w-[500px] bg-white/70 backdrop-blur-2xl border border-white/60 rounded-[32px] p-[48px] md:p-[56px] mx-4 z-10 relative overflow-hidden shadow-[0_20px_60px_rgba(146,146,197,0.25)]">
        
        <!-- Header Logo -->
        <div class="text-center mb-[40px] mt-[8px]">
            <h1 class="text-[32px] text-[#9292C5] tracking-wide mb-[6px]" style="font-weight: 300;">Reset Password</h1>
            <p class="text-[#9292C5] font-normal text-[13px] opacity-70">Masukkan email dan password baru Anda.</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4 text-[13px] text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-600 p-3 rounded-xl mb-4 text-[13px]">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('forgot-password.process') }}" method="POST" class="flex flex-col gap-[20px] text-left">
            @csrf
            
            <!-- Input Email -->
            <div class="flex flex-col gap-[8px]">
                <label class="text-[13px] font-semibold text-[#9292C5]">Email Akun</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@gmail.com" required class="w-full bg-[#F3F3F9] rounded-[12px] px-[18px] py-[14px] text-[14px] font-semibold text-[#333] outline-none placeholder-[#aaa] border border-[#E0E0ED] focus:border-[#9292C5] focus:bg-white focus:ring-4 focus:ring-[#9292C5]/20 transition-all shadow-sm">
            </div>

            <!-- Input New Password -->
            <div class="flex flex-col gap-[8px]">
                <label class="text-[13px] font-semibold text-[#9292C5]">Password Baru</label>
                <input type="password" name="password" placeholder="••••••••••••" required class="w-full bg-[#F3F3F9] rounded-[12px] px-[18px] py-[14px] text-[14px] font-semibold text-[#333] outline-none placeholder-[#aaa] border border-[#E0E0ED] focus:border-[#9292C5] focus:bg-white focus:ring-4 focus:ring-[#9292C5]/20 transition-all shadow-sm">
            </div>

            <!-- Input Confirm Password -->
            <div class="flex flex-col gap-[8px]">
                <label class="text-[13px] font-semibold text-[#9292C5]">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="••••••••••••" required class="w-full bg-[#F3F3F9] rounded-[12px] px-[18px] py-[14px] text-[14px] font-semibold text-[#333] outline-none placeholder-[#aaa] border border-[#E0E0ED] focus:border-[#9292C5] focus:bg-white focus:ring-4 focus:ring-[#9292C5]/20 transition-all shadow-sm">
            </div>

            <!-- Submit -->
            <div class="text-center mt-[12px]">
                <button type="submit" class="w-full bg-[#9292C5] text-white py-[14px] rounded-[14px] font-bold text-[15px] hover:bg-[#8585b8] transition-all shadow-sm hover:shadow-[0_4px_20px_rgba(146,146,197,0.4)]">
                    Simpan Password Baru
                </button>
                <p class="text-[13px] font-medium text-[#999] mt-[20px] flex items-center justify-center gap-[6px]">
                    Kembali ke halaman <a href="{{ route('login') }}" class="text-[#9292C5] hover:underline font-semibold">Login</a>
                </p>
            </div>
        </form>
    </div>

    <script>
        const canvas = document.getElementById('particle-canvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        let mouse = { x: window.innerWidth / 2, y: window.innerHeight / 2 };

        function init() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            particles = [];
            
            const totalRings = 16;
            for(let r = 1; r <= totalRings; r++) {
                let radius = r * 28; 
                let count = Math.floor((2 * Math.PI * radius) / 20); 
                
                for(let i=0; i<count; i++) {
                    let theta = (i / count) * Math.PI * 2;
                    let hue = 235 + (theta / (Math.PI * 2)) * 25; 
                    let lightness = 60 + Math.random() * 15;
                    
                    particles.push({
                        baseRadius: radius,
                        theta: theta, 
                        colorStr: `hsl(${hue}, 45%, ${lightness}%)`,
                        baseSize: Math.random() * 1.5 + 2, 
                        x: mouse.x + Math.cos(theta) * radius,
                        y: mouse.y + Math.sin(theta) * radius,
                        vx: 0,
                        vy: 0
                    });
                }
            }
        }

        function animate() {
            requestAnimationFrame(animate);
            ctx.clearRect(0, 0, width, height);
            
            let time = Date.now() * 0.002; 
            
            particles.forEach((p) => {
                p.theta += 0.001; 
                
                let waveOffset = Math.sin(p.theta * 6 - time * 2) * 15;
                let currentRadius = p.baseRadius + waveOffset;
                
                let bulgeRadius = 150; 
                let sizeMult = 1; 
                
                if (currentRadius < bulgeRadius) {
                    let bulgeStrength = Math.pow((bulgeRadius - currentRadius) / bulgeRadius, 1.5); 
                    currentRadius -= bulgeStrength * 80; 
                    if (currentRadius < 35) currentRadius = 35; 
                    
                    sizeMult = 1; 
                }

                let targetX = mouse.x + Math.cos(p.theta) * currentRadius;
                let targetY = mouse.y + Math.sin(p.theta) * currentRadius;
                
                let ease = Math.max(0.015, 0.12 - (p.baseRadius * 0.00025)); 
                
                p.x += (targetX - p.x) * ease;
                p.y += (targetY - p.y) * ease;
                
                let distFromCursor = Math.sqrt(Math.pow(p.x - mouse.x, 2) + Math.pow(p.y - mouse.y, 2));
                let maxDist = 400; 
                
                let outerOpacity = Math.max(0, 1 - (distFromCursor / maxDist));
                
                let innerFadeDist = 60;
                let innerOpacity = Math.min(1, Math.max(0, (distFromCursor - 25) / innerFadeDist));
                
                let opacity = outerOpacity * innerOpacity;
                
                if (opacity > 0.01) {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.baseSize * sizeMult, 0, Math.PI * 2); 
                    ctx.fillStyle = p.colorStr.replace(')', `, ${opacity})`).replace('hsl', 'hsla');
                    ctx.fill();
                }
            });
        }

        window.addEventListener('mousemove', e => { 
            mouse.x = e.clientX; 
            mouse.y = e.clientY; 
        });
        
        window.addEventListener('resize', init);
        
        init();
        animate();
    </script>
</body>
</html>
