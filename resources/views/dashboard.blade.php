@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-12 gap-[24px] pb-4 items-start" x-data="dashboardWidget()">
    
    <!-- Left Column: Status Air -->
    <div class="col-span-1 xl:col-span-5 flex flex-col items-stretch">
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] rounded-[24px] p-[28px] md:p-[32px] flex flex-col relative h-full border border-transparent dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300">
            
            <!-- Header -->
            <div class="flex justify-between items-start relative z-50 w-full mb-[8px]">
                <div>
                    <h3 class="text-[22px] font-bold tracking-tight mb-[12px] text-black dark:text-white">Status Air</h3>
                    
                    <!-- EWS Dropdown -->
                    <div class="relative inline-block mb-[12px] z-[100]">
                        <button @click="open = !open" @click.outside="open = false" class="bg-[#E5E5EF] dark:bg-[rgba(255,255,255,0.05)] text-[#555] dark:text-[#a5a5d1] px-[16px] py-[6px] rounded-[10px] text-[13px] font-bold flex items-center gap-2 hover:bg-[#d8d8e8] dark:hover:bg-[rgba(255,255,255,0.08)] transition-colors cursor-pointer border border-[#d0d0e0] dark:border-[rgba(255,255,255,0.08)]">
                            <span x-text="device.name">EWS 1</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition style="display: none;" class="absolute left-0 mt-2 w-[140px] bg-white dark:bg-[#2e2f3a] rounded-[12px] shadow-[0_4px_20px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.4)] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] py-2 z-[100]">
                            <button @click="open = false" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-black dark:text-white hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors relative z-10">EWS 1</button>
                        </div>
                    </div>
                    
                    <p class="text-[#555] dark:text-[#a5a5d1] text-[14px] font-medium leading-snug break-words pr-2" x-text="'Sungai Brantas ' + device.lokasi">Sungai Brantas Soekarno-Hatta</p>
                </div>
                
                <!-- Online Badge -->
                <div>
                    <div x-show="isOnline" class="bg-white dark:bg-[#344034] px-[12px] py-[4px] rounded-full border border-[#e2f1e2] dark:border-[rgba(107,191,107,0.2)] flex items-center gap-1.5 shadow-sm shrink-0">
                        <div class="w-[7px] h-[7px] rounded-full bg-[#6BBF6B] animate-pulse"></div>
                        <span class="text-[#6BBF6B] font-bold text-[13px]">Online</span>
                    </div>
                </div>
            </div>

            <!-- Ring Gauge -->
            <div class="flex-1 flex flex-col items-center justify-center z-10 w-full mt-[24px] mb-[8px]">
                <div class="relative flex items-center justify-center w-[180px] h-[180px] md:w-[200px] md:h-[200px]">
                    <!-- Ring Circle (outline style like mockup) -->
                    <div class="absolute inset-0 rounded-full border-[8px] transition-colors duration-500" :style="`border-color: ${getColor()}`"></div>
                    <!-- Inner content -->
                    <div class="flex flex-col items-center justify-center">
                        <div class="flex items-baseline">
                            <span class="text-[42px] md:text-[52px] font-[900] leading-none tracking-tight transition-colors duration-500" :style="`color: ${getColor()}`" x-text="device.levelAir">10</span>
                            <span class="text-[18px] md:text-[20px] font-bold leading-none ml-0.5 transition-colors duration-500" :style="`color: ${getColor()}`">cm</span>
                        </div>
                    </div>
                </div>
                <p class="text-[28px] md:text-[32px] font-[900] mt-[16px] tracking-wide transition-colors duration-500 z-10" :style="`color: ${getColor()}`" x-text="device.status">Aman</p>
            </div>
            
            <!-- Real Chart Area -->
            <div class="w-full h-[120px] mt-[16px] mb-[16px] relative z-0 flex items-stretch pr-[12px] pl-[4px]">
                <div id="realtimeChart" class="w-full h-full" wire:ignore></div>
            </div>

            <!-- Bottom Info: Jarak Sensor & Terakhir Update -->
            <div class="flex justify-between items-center z-10 px-[8px] mt-[8px]">
                <div class="text-center">
                    <p class="text-[13px] font-bold text-[#555] dark:text-[#a5a5d1]">Jarak Sensor</p>
                    <p class="text-[15px] font-[800] text-black dark:text-white" x-text="device.levelAir + ' cm'">12 cm</p>
                </div>
                <div class="text-center">
                    <p class="text-[13px] font-bold text-[#555] dark:text-[#a5a5d1]">Terakhir Update</p>
                    <p class="text-[15px] font-[800] text-black dark:text-white" x-text="device.terakhirUpdate">10.24</p>
                </div>
            </div>

        </div>
    </div>

    <!-- Right Column -->
    <div class="col-span-1 xl:col-span-7 flex flex-col gap-[24px]">
        
        <!-- 4 Stat Cards: 2x2 Grid -->
        <div class="grid grid-cols-2 gap-[24px]">
            <!-- Cuaca Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[160px] transition-colors duration-300 relative">
                <div class="flex justify-between items-start">
                    <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Cuaca</p>
                    <a href="#" class="text-[#9292C5] hover:text-[#7b7bb2] transition-colors">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
                <div class="flex items-center justify-center flex-1">
                    <!-- Cerah Icon -->
                    <template x-if="device.statusHujan !== 'Hujan'">
                        <svg class="w-14 h-14 text-[#555] dark:text-[#a5a5d1]" fill="currentColor" viewBox="0 0 24 24"><path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z"/></svg>
                    </template>
                    <template x-if="device.statusHujan === 'Hujan'">
                        <svg class="w-14 h-14 text-[#3B82F6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21v-4m-4 2v-4m8 2v-4"></path></svg>
                    </template>
                </div>
                <p class="text-[16px] font-bold text-black dark:text-white text-center" x-text="device.statusHujan">Cerah</p>
            </div>

            <!-- Perangkat Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[160px] transition-colors duration-300">
                <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Perangkat</p>
                <p class="text-[56px] font-[900] text-black dark:text-white text-right leading-none">{{ $totalPerangkat }}/3</p>
            </div>

            <!-- Data Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[160px] transition-colors duration-300">
                <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Data</p>
                <p class="text-[56px] font-[900] text-black dark:text-white text-right leading-none">{{ $totalData }}</p>
            </div>

            <!-- Peringatan Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[160px] transition-colors duration-300">
                <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Peringatan</p>
                <p class="text-[56px] font-[900] text-black dark:text-white text-right leading-none">{{ $totalPeringatan }}</p>
            </div>
        </div>

        <!-- Notifikasi Terkini -->
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] md:p-[28px] flex flex-col transition-colors duration-300 flex-1 relative z-10">
            <h3 class="text-[22px] font-bold tracking-tight mb-[20px] text-black dark:text-white">Notifikasi Terkini</h3>
            
            <div class="flex flex-col gap-[12px] flex-1 w-full max-w-full">
                @forelse($notifikasi as $notif)
                @php
                    $statusType = ucfirst(strtolower($notif->alert_level));
                    
                    $bgColor = $statusType === 'Bahaya' ? 'bg-[#e02424]' : ($statusType === 'Waspada' ? 'bg-[#D8C726]' : 'bg-[#6BBF6B]');
                    $dateObj = \Carbon\Carbon::parse($notif->created_at)->setTimezone('Asia/Jakarta');
                    $timeStr = $dateObj->format('H:i') . ' WIB';

                    $jarakVal = '-';
                    if (preg_match('/(\d+)\s*cm/', $notif->message, $m)) $jarakVal = $m[1];
                    
                    $msgText = "EWS 1 berjalan normal, ketinggian air terpantau stabil pada {$jarakVal}cm.";
                    if ($statusType === 'Bahaya') $msgText = "Air menyentuh level kritis ({$jarakVal}cm)! Segera cek riwayat dan berikan tindakan.";
                    if ($statusType === 'Waspada') $msgText = "Peringatan! Air memasuki level Waspada pada {$jarakVal}cm.";
                @endphp
                <div class="bg-white hover:bg-gray-50 dark:bg-[rgba(255,255,255,0.03)] dark:hover:bg-[rgba(255,255,255,0.06)] border border-[#e5e7eb] dark:border-[rgba(255,255,255,0.05)] transition-all duration-300 min-h-[56px] rounded-[14px] w-full flex items-center px-[20px] text-[13px] text-black dark:text-white font-[600] gap-3 shadow-sm relative overflow-hidden group">
                    <div class="absolute left-0 top-0 bottom-0 w-[4px] {{ $bgColor }}"></div>
                    <div class="w-2.5 h-2.5 rounded-full {{ $bgColor }} shrink-0"></div>
                    <span class="truncate flex-1">{{ $msgText }}</span>
                    <span class="ml-auto text-[#9292C5] dark:text-[#a5a5d1] font-bold text-[11px] shrink-0">{{ $timeStr }}</span>
                </div>
                @empty
                <div class="text-center w-full py-6 font-[600] text-[#a5a5d1]">Belum ada Notifikasi Terbaru</div>
                @endforelse
            </div>

            <div class="flex justify-end mt-[16px]">
                <a href="/peringatan" class="text-[#9292C5] font-semibold text-[13px] hover:underline transition-all">
                    Selengkapnya
                </a>
            </div>
        </div>

    </div>

    <!-- In-App Toast Notification (Bottom Right) -->
    <div x-show="showToast" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-10 opacity-0" style="display: none;" class="fixed bottom-[30px] right-[30px] bg-white dark:bg-[#20212a] border-l-[4px] shadow-[0_10px_40px_rgba(0,0,0,0.15)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] rounded-[16px] p-4 z-[9999] flex items-start gap-4 max-w-sm w-full" :class="toastBorderColor">
        <div class="p-2 rounded-full mt-0.5 shrink-0" :class="toastIconBg">
            <svg class="w-5 h-5" :class="toastIconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="flex-1 pt-0.5">
            <h4 class="font-[800] text-[15px] mb-[2px] tracking-tight" :class="toastTitleColor" x-text="toastTitle">🚨 PERINGATAN DINI!</h4>
            <p class="text-[13px] text-black dark:text-[#a5a5d1] font-[600] leading-snug" x-text="toastMessage"></p>
        </div>
        <button @click="showToast = false" class="text-gray-400 hover:text-black dark:hover:text-white shrink-0 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardWidget', () => ({
            open: false, 
            device: {
                name: 'EWS 1',
                lokasi: 'Soekarno-Hatta',
                levelAir: 0,
                status: 'Menunggu',
                statusHujan: 'Cerah',
                terakhirUpdate: '...'
            },
            showToast: false,
            toastTitle: '',
            toastMessage: '',
            toastBorderColor: 'border-[#e02424]',
            toastIconBg: 'bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.15)]',
            toastIconColor: 'text-[#c81e1e] dark:text-[#e02424]',
            toastTitleColor: 'text-[#e02424]',
            isOnline: true,
            chartInstance: null,
            chartData: [],
            tick: 0,
            lastStatus: null,
            lastRainStatus: null,
            getColor() {
                let status = this.device.status;
                if (status === 'Bahaya') return '#e02424';
                if (status === 'Waspada') return '#f59e0b';
                if (status === 'Menunggu') return '#9292C5';
                return '#6BBF6B';
            },
            getRainColor() {
                let status = this.device.statusHujan;
                if (status === 'Hujan') return '#3B82F6';
                if (status === 'Menunggu') return '#9292C5';
                return '#6BBF6B';
            },
            init() {
                // Minta Izin Notification Browser
                if ('Notification' in window && Notification.permission !== 'denied') {
                    Notification.requestPermission();
                }

                // Inisiasi Chart saat DOM siap
                this.$nextTick(() => {
                    if (typeof ApexCharts === 'undefined') {
                        console.error('ApexCharts library is not loaded!');
                        return;
                    }

                    const isDark = document.documentElement.classList.contains('dark');
                    const textColor = isDark ? '#a5a5d1' : '#9292C5';
                    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
                    
                    var options = {
                        series: [{ name: 'Jarak', data: [] }],
                        chart: {
                            type: 'area',
                            height: 120,
                            animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } },
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            parentHeightOffset: 0
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3, colors: ['#9292C5'] },
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100], colorStops: [{ offset: 0, color: '#9292C5', opacity: 0.4 }, { offset: 100, color: '#9292C5', opacity: 0.05 }] } },
                        xaxis: { type: 'numeric', range: 20, labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false }, tooltip: { enabled: false } },
                        yaxis: { max: 400, min: 0, tickAmount: 4, labels: { style: { colors: textColor, fontWeight: 600, fontSize: '11px' }, formatter: function(val) { return Math.round(val) + 'cm'; } } },
                        grid: { borderColor: gridColor, strokeDashArray: 4 },
                        tooltip: { theme: isDark ? 'dark' : 'light' }
                    };

                    this.chartInstance = new ApexCharts(document.querySelector('#realtimeChart'), options);
                    this.chartInstance.render();
                });

                // Fetch Pertama & Polling tiap detik
                const fetchSensor = () => {
                    fetch('/data-sensor/latest')
                        .then(res => res.json())
                        .then(data => {
                            let jarak = data.jarak || 0;
                            jarak = Math.max(0, Math.min(400, parseFloat(jarak))); // Clamp 0-400cm (HC-SR04 max 4m)
                            this.device.levelAir = Math.round(jarak);
                            this.isOnline = true;
                            
                            // Penentuan Status dari backend yang menghitung dari DB Threshold
                            this.device.status = data.status || 'Aman';
                            
                            // Ambil Cuaca (Hujan / Cerah)
                            this.device.statusHujan = data.hujan || 'Cerah';
                            
                            // Fungsi untuk memunculkan notif popup
                            const triggerNotif = (title, message, colors) => {
                                this.toastTitle = title;
                                this.toastMessage = message;
                                this.toastBorderColor = colors.border;
                                this.toastIconBg = colors.iconBg;
                                this.toastIconColor = colors.iconColor;
                                this.toastTitleColor = colors.titleColor;
                                this.showToast = true;
                                setTimeout(() => { this.showToast = false; }, 8000);
                                if ('Notification' in window && Notification.permission === 'granted') {
                                    new Notification(title, { body: message, icon: '/favicon.ico' });
                                }
                            };

                            // Logika Notifikasi Timbul
                            if (this.device.status === 'Bahaya' && this.lastStatus !== 'Bahaya') {
                                triggerNotif('🚨 BAHAYA BANJIR!', `Air menyentuh level kritis (${jarak}cm)! Cuaca: ${this.device.statusHujan}. Segera ambil tindakan.`, {
                                    border: 'border-[#e02424]', iconBg: 'bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.15)]', iconColor: 'text-[#c81e1e] dark:text-[#e02424]', titleColor: 'text-[#e02424]'
                                });
                            } else if (this.device.status === 'Waspada' && this.lastStatus !== 'Waspada' && this.lastStatus !== 'Bahaya') {
                                triggerNotif('⚠️ SIAGA BANJIR!', `Air memasuki level waspada (${jarak}cm). Cuaca: ${this.device.statusHujan}. Harap berhati-hati.`, {
                                    border: 'border-[#f59e0b]', iconBg: 'bg-[#fef3c7] dark:bg-[rgba(245,158,11,0.15)]', iconColor: 'text-[#d97706] dark:text-[#f59e0b]', titleColor: 'text-[#f59e0b]'
                                });
                            } else if (this.device.statusHujan === 'Hujan' && this.lastRainStatus !== 'Hujan') {
                                triggerNotif('🌧️ PERINGATAN CUACA!', `Terdeteksi hujan turun. Jarak Air: ${jarak}cm. Pantau terus ketinggian air.`, {
                                    border: 'border-[#3B82F6]', iconBg: 'bg-[#dbeafe] dark:bg-[rgba(59,130,246,0.15)]', iconColor: 'text-[#2563eb] dark:text-[#3B82F6]', titleColor: 'text-[#3B82F6]'
                                });
                            }
                            
                            this.lastStatus = this.device.status;
                            this.lastRainStatus = this.device.statusHujan;

                            // Perbarui Chart Data
                            this.tick++;
                            this.chartData.push({ x: this.tick, y: jarak });
                            if (this.chartData.length > 30) this.chartData.shift();
                            if (this.chartInstance) {
                                this.chartInstance.updateSeries([{ name: 'Jarak', data: this.chartData }]);
                            }
                            
                            // Waktu Update
                            let d = new Date();
                            this.device.terakhirUpdate = d.getHours().toString().padStart(2, '0') + '.' + d.getMinutes().toString().padStart(2, '0');
                        })
                        .catch(err => {
                            this.isOnline = false;
                            console.error('Sensor fetch error:', err);
                        });
                };
                
                fetchSensor();
                setInterval(fetchSensor, 2000);
            }
        }));
    });
</script>
@endpush
