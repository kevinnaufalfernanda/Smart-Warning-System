@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-12 gap-[24px] pb-4 items-stretch">
    
    <!-- Status Air Card -->
    <div class="col-span-1 md:col-span-12 xl:col-span-4 flex flex-col items-stretch">
        <div x-data="dashboardWidget()" class="bg-[#F3F3F3] dark:bg-[#20212a] rounded-[24px] p-[28px] md:p-[32px] flex flex-col relative h-full border border-transparent dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300">
            
            <div class="flex justify-between items-start relative z-50 w-full mb-[8px]">
                <div>
                    <h3 class="text-[22px] font-bold tracking-tight mb-[12px] text-black dark:text-white">Status Air</h3>
                    
                    <div class="relative inline-block mb-[12px] z-[100]">
                        <button @click="open = !open" @click.outside="open = false" class="bg-[#9292C5] text-white px-[16px] py-[6px] rounded-[10px] text-[13px] font-bold flex items-center gap-2 hover:bg-[#8585b8] transition-colors cursor-pointer shadow-sm">
                            <span x-text="device.name">EWS 1</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition style="display: none;" class="absolute left-0 mt-2 w-[140px] bg-white dark:bg-[#2e2f3a] rounded-[12px] shadow-[0_4px_20px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.4)] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] py-2 z-[100]">
                            <button @click="open = false" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-black dark:text-white hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors relative z-10">EWS 1</button>
                        </div>
                    </div>
                    
                    <p class="text-[#333] dark:text-[#a5a5d1] text-[16px] font-semibold leading-snug break-words pr-2 mt-1" x-text="device.lokasi">Soekarno-Hatta</p>
                </div>
                
                <div>
                    <div x-show="isOnline" class="bg-white dark:bg-[#344034] px-[12px] py-[3px] rounded-full border-[1.5px] border-[#e2f1e2] dark:border-[rgba(107,191,107,0.2)] flex items-center gap-1.5 shadow-[0_3px_10px_rgba(107,191,107,0.4)] shrink-0">
                        <div class="w-[7px] h-[7px] rounded-full bg-[#6BBF6B] animate-pulse"></div>
                        <span class="text-[#6BBF6B] font-bold text-[13px] tracking-wide">Online</span>
                    </div>
                </div>
            </div>

            <!-- Gauges Area -->
            <div class="flex-1 flex flex-row items-center justify-center gap-[24px] md:gap-[40px] z-10 w-full mt-[32px] mb-[16px]">
                
                <!-- Status Air Gauge -->
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center w-[130px] h-[130px] md:w-[150px] md:h-[150px] flex-shrink-0 transition-all duration-500">
                        <!-- Animasi Berdenyut (Pulse Ring) -->
                        <div class="absolute inset-0 rounded-full animate-ping opacity-20" :style="`background-color: ${getColor()}`" style="animation-duration: 2s;"></div>
                        <!-- Lingkaran Utama -->
                        <div class="absolute inset-0 rounded-full shadow-xl z-10" :style="`background-color: ${getColor()}; box-shadow: 0 0px 48px ${getColor()}66`"></div>
                        <div class="absolute inset-0 z-20 flex flex-col items-center justify-center mix-blend-normal text-white drop-shadow-md">
                            <div class="flex items-baseline">
                                <span class="text-[36px] md:text-[46px] font-[900] leading-none tracking-tight" x-text="device.levelAir">10</span>
                                <span class="text-[16px] md:text-[18px] font-bold leading-none tracking-tight ml-0.5">cm</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-[26px] md:text-[28px] font-[900] mt-[20px] tracking-wide transition-colors duration-500 z-10" :style="`color: ${getColor()}; text-shadow: 0 4px 12px ${getColor()}40`" x-text="device.status">Aman</p>
                    <p class="text-[13px] font-bold text-gray-500 dark:text-[#a5a5d1] mt-0.5 tracking-wider uppercase">Status Air</p>
                </div>

                <!-- Pemisah -->
                <div class="hidden md:block w-[1px] h-[120px] bg-gray-300 dark:bg-[rgba(255,255,255,0.08)] rounded-full"></div>

                <!-- Cuaca Gauge -->
                <div class="flex flex-col items-center">
                    <div class="relative flex items-center justify-center w-[130px] h-[130px] md:w-[150px] md:h-[150px] flex-shrink-0 transition-all duration-500">
                        <div class="absolute inset-0 rounded-full animate-ping opacity-20" :style="`background-color: ${getRainColor()}`" style="animation-duration: 2.5s;"></div>
                        <div class="absolute inset-0 rounded-full shadow-xl z-10" :style="`background-color: ${getRainColor()}; box-shadow: 0 0px 48px ${getRainColor()}66`"></div>
                        <div class="absolute inset-0 z-20 flex flex-col items-center justify-center mix-blend-normal text-white drop-shadow-md">
                            <!-- Hujan Icon -->
                            <template x-if="device.statusHujan === 'Hujan'">
                                <svg class="w-14 h-14 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 21v-4m-4 2v-4m8 2v-4"></path>
                                </svg>
                            </template>
                            <!-- Cerah Icon -->
                            <template x-if="device.statusHujan !== 'Hujan'">
                                <svg class="w-14 h-14 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </template>
                        </div>
                    </div>
                    <p class="text-[26px] md:text-[28px] font-[900] mt-[20px] tracking-wide transition-colors duration-500 z-10" :style="`color: ${getRainColor()}; text-shadow: 0 4px 12px ${getRainColor()}40`" x-text="device.statusHujan">Cerah</p>
                    <p class="text-[13px] font-bold text-gray-500 dark:text-[#a5a5d1] mt-0.5 tracking-wider uppercase">Cuaca</p>
                </div>

            </div>
            
            <!-- Real Chart Area with Y-Axis -->
            <div class="w-full h-[140px] mt-[24px] mb-[48px] relative z-0 flex items-stretch pr-[12px] pl-[4px]">
                <div id="realtimeChart" class="w-full h-full" wire:ignore></div>
            </div>

            <!-- Texts Bottom -->
            <div class="flex justify-center items-center z-10 px-[12px]">
                <div class="text-center bg-[#e7e7f1] dark:bg-[rgba(255,255,255,0.05)] px-5 py-2 rounded-full inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#9292C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-black dark:text-[#a5a5d1] font-medium text-[13px]">Update terakhir: <span x-text="device.terakhirUpdate" class="font-bold"></span></span>
                </div>
            </div>

        </div>
    </div>

    <!-- Right Column -->
    <div class="col-span-1 md:col-span-12 xl:col-span-8 flex flex-col gap-[24px]">
        
        <!-- Notifikasi Terkini -->
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] md:p-[28px] flex flex-col transition-colors duration-300 flex-1 relative z-10">
            <h3 class="text-[22px] font-bold tracking-tight mb-[24px] text-black dark:text-white">Notifikasi Terkini</h3>
            
            <div class="flex flex-col gap-[20px] flex-1 w-full max-w-full">
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
                <div class="bg-white hover:bg-gray-50 dark:bg-[rgba(255,255,255,0.03)] dark:hover:bg-[rgba(255,255,255,0.06)] border border-[#e5e7eb] dark:border-[rgba(255,255,255,0.05)] transition-all duration-300 min-h-[64px] rounded-[16px] w-full flex items-center px-[24px] text-[14px] text-black dark:text-white font-[600] gap-4 shadow-sm relative overflow-hidden group">
                    <div class="absolute left-0 top-0 bottom-0 w-[4px] {{ $bgColor }}"></div>
                    <div class="w-3 h-3 rounded-full {{ $bgColor }} shrink-0 group-hover:scale-125 transition-transform duration-300"></div>
                    <span class="truncate">{{ $msgText }}</span>
                    <span class="ml-auto text-[#9292C5] dark:text-[#a5a5d1] font-bold text-[12px] shrink-0 bg-[#f3f3f8] dark:bg-[rgba(146,146,197,0.1)] px-3 py-1 rounded-full group-hover:bg-[#e4e4ee] dark:group-hover:bg-[rgba(146,146,197,0.2)] transition-colors">{{ $timeStr }}</span>
                </div>
                @empty
                <div class="text-center w-full py-6 font-[600] text-[#a5a5d1]">Belum ada Notifikasi Terbaru</div>
                @endforelse
            </div>

            <div class="flex justify-end mt-[28px]">
                <a href="/peringatan" class="bg-gradient-to-r from-[#9292C5] to-[#7f7fae] text-white px-[24px] py-[10px] rounded-[14px] font-bold text-[14px] hover:shadow-[0_8px_20px_rgba(146,146,197,0.4)] hover:-translate-y-1 transition-all duration-300 text-center inline-flex items-center gap-2 cursor-pointer tracking-wide">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- 3 Stats Cards Row (auto height) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] shrink-0 min-h-[160px] relative z-0">
            <!-- Card Perangkat -->
            <div class="relative overflow-hidden bg-white dark:bg-[#20212a] border border-[#e5e7eb] dark:border-[rgba(255,255,255,0.05)] hover:border-[#9292C5] dark:hover:border-[#9292C5] transition-all duration-300 rounded-[24px] flex flex-col items-start justify-center p-[28px] shadow-sm hover:shadow-[0_8px_30px_rgba(146,146,197,0.15)] group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-[#9292C5]/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-[#f3f3f8] dark:bg-[#2a2b36] text-[#9292C5] flex items-center justify-center mb-4 shadow-sm group-hover:-translate-y-1 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                </div>
                <p class="text-[14px] font-bold text-gray-500 dark:text-[#a5a5d1] mb-1 uppercase tracking-wide">Perangkat Aktif</p>
                <p class="text-[36px] lg:text-[42px] font-[900] tracking-tight leading-none text-black dark:text-white">{{ $totalPerangkat }}</p>
            </div>
            
            <!-- Card Data -->
            <div class="relative overflow-hidden bg-white dark:bg-[#20212a] border border-[#e5e7eb] dark:border-[rgba(255,255,255,0.05)] hover:border-[#6BBF6B] dark:hover:border-[#6BBF6B] transition-all duration-300 rounded-[24px] flex flex-col items-start justify-center p-[28px] shadow-sm hover:shadow-[0_8px_30px_rgba(107,191,107,0.15)] group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-[#6BBF6B]/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-[#effaf0] dark:bg-[#26312a] text-[#6BBF6B] flex items-center justify-center mb-4 shadow-sm group-hover:-translate-y-1 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                </div>
                <p class="text-[14px] font-bold text-gray-500 dark:text-[#a5a5d1] mb-1 uppercase tracking-wide">Total Data Masuk</p>
                <p class="text-[36px] lg:text-[42px] font-[900] tracking-tight leading-none text-black dark:text-white">{{ $totalData }}</p>
            </div>

            <!-- Card Peringatan -->
            <div class="relative overflow-hidden bg-white dark:bg-[#20212a] border border-[#e5e7eb] dark:border-[rgba(255,255,255,0.05)] hover:border-[#e02424] dark:hover:border-[#e02424] transition-all duration-300 rounded-[24px] flex flex-col items-start justify-center p-[28px] shadow-sm hover:shadow-[0_8px_30px_rgba(224,36,36,0.15)] group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-[#e02424]/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-[#fef2f2] dark:bg-[#342426] text-[#e02424] flex items-center justify-center mb-4 shadow-sm group-hover:-translate-y-1 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <p class="text-[14px] font-bold text-gray-500 dark:text-[#a5a5d1] mb-1 uppercase tracking-wide">Peringatan Dini</p>
                <p class="text-[36px] lg:text-[42px] font-[900] tracking-tight leading-none text-black dark:text-white">{{ $totalPeringatan }}</p>
            </div>
        </div>

    </div>

    <!-- In-App Toast Notification (Bottom Right) -->
    <div x-show="showToast" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-10 opacity-0" style="display: none;" class="fixed bottom-[30px] right-[30px] bg-white dark:bg-[#20212a] border-l-[4px] border-[#e02424] shadow-[0_10px_40px_rgba(224,36,36,0.2)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] rounded-[16px] p-4 z-[9999] flex items-start gap-4 max-w-sm w-full">
        <div class="bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.15)] p-2 rounded-full mt-0.5 shrink-0">
            <svg class="w-5 h-5 text-[#c81e1e] dark:text-[#e02424]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="flex-1 pt-0.5">
            <h4 class="text-[#e02424] font-[800] text-[15px] mb-[2px] tracking-tight">🚨 BAHAYA BANJIR! EWS 1</h4>
            <p class="text-[13px] text-black dark:text-[#a5a5d1] font-[600] leading-snug" x-text="toastMessage">Air menyentuh level kritis!</p>
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
            toastMessage: '',
            isOnline: true,
            chartInstance: null,
            chartData: [],
            tick: 0,
            lastStatus: null,
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
                            height: 140,
                            animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } },
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            parentHeightOffset: 0
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3, colors: ['#9292C5'] },
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100], colorStops: [{ offset: 0, color: '#9292C5', opacity: 0.4 }, { offset: 100, color: '#9292C5', opacity: 0.05 }] } },
                        xaxis: { type: 'numeric', range: 20, labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false }, tooltip: { enabled: false } },
                        yaxis: { max: 400, min: 0, tickAmount: 4, labels: { style: { colors: textColor, fontWeight: 600 } } },
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
                            this.device.levelAir = jarak;
                            
                            // Penentuan Status (Sesuai Hardware Arduino)
                            if (jarak >= 0 && jarak <= 20) this.device.status = 'Bahaya';
                            else if (jarak > 20 && jarak <= 60) this.device.status = 'Waspada';
                            else this.device.status = 'Aman';
                            
                            // Logika Notifikasi Timbul
                            if (this.device.status === 'Bahaya' && this.lastStatus !== 'Bahaya') {
                                // Tampilkan In-App Toast di Pojok Kanan Bawah
                                this.toastMessage = `Air menyentuh level kritis (${jarak}cm)! Segera cek riwayat dan berikan tindakan.`;
                                this.showToast = true;
                                setTimeout(() => { this.showToast = false; }, 8000); // Hilang setelah 8 dtk

                                // (Opsional) Native OS Push Notification jika diizinkan & HTTPS
                                if ('Notification' in window && Notification.permission === 'granted') {
                                    new Notification('🚨 BAHAYA BANJIR! EWS 1', {
                                        body: this.toastMessage,
                                        icon: '/favicon.ico'
                                    });
                                }
                            }
                            this.lastStatus = this.device.status;

                            // Perbarui Chart Data
                            if (this.chartInstance) {
                                this.chartData.push({ x: this.tick, y: jarak });
                                if (this.chartData.length > 20) this.chartData.shift();
                                this.chartInstance.updateSeries([{ data: this.chartData }]);
                                this.tick++;
                            }

                            // Ambil Cuaca (Hujan / Cerah)
                            this.device.statusHujan = data.hujan || 'Cerah';
                            
                            // Waktu Update
                            let d = new Date();
                            this.device.terakhirUpdate = d.getHours().toString().padStart(2, '0') + '.' + d.getMinutes().toString().padStart(2, '0') + ' WIB';
                        })
                        .catch(err => {
                            this.isOnline = false;
                            console.error('Sensor fetch error:', err);
                        });
                };
                
                fetchSensor();
                setInterval(fetchSensor, 1000);
            }
        }));
    });
</script>
@endpush
