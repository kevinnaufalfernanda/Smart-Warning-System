@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<style>
    /* Menghilangkan CSS hide marker karena mengganggu hover tooltip */
</style>
<div class="grid grid-cols-1 xl:grid-cols-12 gap-[24px] pb-4 items-stretch" x-data="dashboardWidget()">
    
    <!-- Left Column -->
    <div class="col-span-1 xl:col-span-5 flex flex-col gap-[24px] items-stretch animate-fade-in-up stagger-1 h-full">
        <!-- Status Air Card -->
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] rounded-[24px] p-[24px] md:p-[28px] flex flex-col relative border border-transparent dark:border-[rgba(255,255,255,0.05)] modern-card flex-1 overflow-hidden">
            
            <!-- Header -->
            <div class="flex justify-between items-start relative z-50 w-full mb-[4px]">
                <div>
                    <h3 class="text-[20px] font-bold tracking-tight mb-[10px] text-black dark:text-white">Status Air</h3>
                    
                    <!-- EWS Dropdown -->
                    <div class="relative inline-block mb-[12px] z-[100]">
                        <button @click="open = !open" @click.outside="open = false" class="bg-[#9292C5] text-white px-[16px] py-[8px] rounded-[12px] text-[13px] font-bold flex items-center gap-2 hover:bg-[#8585b8] hover:shadow-md transition-all duration-300 cursor-pointer border border-transparent shadow-sm">
                            <span x-text="device.name">EWS 1</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition style="display: none;" class="absolute left-0 mt-2 w-[180px] bg-white dark:bg-[#2e2f3a] rounded-[12px] shadow-[0_4px_20px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.4)] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] py-2 z-[100]">
                            @foreach($devices as $dev)
                            <button @click="changeDevice({{ $dev->id }}, '{{ $dev->name }}', '{{ $dev->station ? $dev->station->location : '-' }}'); open = false" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-black dark:text-white hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors relative z-10">{{ $dev->name }}</button>
                            @endforeach
                        </div>
                    </div>
                    
                    <p class="text-[#555] dark:text-[#a5a5d1] text-[14px] font-medium leading-snug break-words pr-2" x-text="device.lokasi"></p>
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
            <div class="flex-1 flex flex-col items-center justify-center z-10 w-full mt-[16px] mb-[4px]">
                <div class="relative flex items-center justify-center w-[160px] h-[160px] md:w-[180px] md:h-[180px]">
                    <!-- Ring Circle (outline style like mockup) -->
                    <div class="absolute inset-0 rounded-full border-[8px] transition-all duration-500" :style="`border-color: ${getColor()}; box-shadow: 0 0 20px ${getColor()}40 inset, 0 0 20px ${getColor()}40;`"></div>
                    <!-- Inner content -->
                    <div class="flex flex-col items-center justify-center">
                        <div class="flex items-baseline">
                            <span class="text-[42px] md:text-[52px] font-[900] leading-none tracking-tight transition-colors duration-500" :style="`color: ${getColor()}`" x-text="device.levelAir">10</span>
                            <span class="text-[18px] md:text-[20px] font-bold leading-none ml-0.5 transition-colors duration-500" :style="`color: ${getColor()}`">cm</span>
                        </div>
                    </div>
                </div>
                <p class="text-[26px] md:text-[28px] font-[900] mt-[12px] tracking-wide transition-colors duration-500 z-10" :style="`color: ${getColor()}`" x-text="device.status">Aman</p>
            </div>
            
            <!-- Filter Ranges (Google Finance Style) -->
            <div class="flex justify-end items-center gap-[6px] mt-[12px] mb-[-4px] z-10 relative px-[16px]">
                <template x-for="r in ['Live', '1 Jam', '5 Jam', '1 Hari', '1 Minggu', '1 Bulan']">
                    <button @click="fetchChartHistory(r)" 
                            class="px-[12px] py-[4px] rounded-full text-[11px] font-bold transition-all duration-200 whitespace-nowrap"
                            :class="currentRange === r ? 'bg-[#9292C5] text-white shadow-sm' : 'text-[#9292C5] hover:bg-[#9292C5]/10 dark:hover:bg-[#9292C5]/20'"
                            x-text="r">
                    </button>
                </template>
            </div>

            <!-- Real Chart Area (Modern ApexChart) -->
            <div class="flex-1 min-h-[140px] mt-[8px] mb-[16px] -mx-[24px] md:-mx-[28px] px-[12px] relative z-0 flex flex-col justify-end bg-gradient-to-b from-transparent via-[#9292C5]/10 to-transparent dark:via-[#9292C5]/20">
                <div id="realtimeChart" class="w-full h-[140px]" wire:ignore></div>
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
        <div class="grid grid-cols-2 gap-[20px]">
            <!-- Cuaca Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] flex flex-col justify-between h-[130px] modern-card relative animate-fade-in-up stagger-2">
                <div class="flex justify-between items-start">
                    <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Cuaca</p>
                    <a href="https://www.bmkg.go.id/" target="_blank" rel="noopener noreferrer" title="Buka website BMKG" class="text-[#9292C5] hover:text-[#7b7bb2] transition-colors">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
                <div class="flex items-center justify-center gap-[16px] flex-1">
                    <!-- Icon Container -->
                    <div class="shrink-0">
                        <!-- Cerah Icon -->
                        <template x-if="device.statusHujan !== 'Hujan'">
                            <svg class="w-[46px] h-[46px] drop-shadow-[0_6px_12px_rgba(245,158,11,0.35)]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="5" fill="#f59e0b" fill-opacity="0.25" stroke="#f59e0b" stroke-width="2.2"/>
                                <path d="M12 2V4M12 20V22M4 12H2M22 12H20M5.636 5.636L7.05 7.05M18.364 18.364L16.95 16.95M5.636 18.364L7.05 16.95M18.364 5.636L16.95 7.05" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round"/>
                            </svg>
                        </template>
                        <template x-if="device.statusHujan === 'Hujan'">
                            <svg class="w-[46px] h-[46px] text-[#9292C5] drop-shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                <path d="M8 14v4"></path>
                                <path d="M12 16v4"></path>
                                <path d="M16 14v4"></path>
                            </svg>
                        </template>
                    </div>
                    <!-- Text Container -->
                    <p class="text-[28px] font-[900] text-black dark:text-white leading-none" x-text="device.statusHujan">Cerah</p>
                </div>
            </div>

            <!-- Perangkat Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] flex flex-col justify-between h-[130px] modern-card animate-fade-in-up stagger-3">
                <p class="text-[16px] font-bold tracking-tight text-black dark:text-white">Perangkat</p>
                <p class="text-[48px] font-[900] text-black dark:text-white text-right leading-none">{{ $onlineCount }}/{{ $totalPerangkat }}</p>
            </div>

            <!-- Data Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] flex flex-col justify-between h-[130px] modern-card animate-fade-in-up stagger-4">
                <p class="text-[16px] font-bold tracking-tight text-black dark:text-white">Data</p>
                <p class="text-[48px] font-[900] text-black dark:text-white text-right leading-none">{{ $totalData }}</p>
            </div>

            <!-- Peringatan Card -->
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] flex flex-col justify-between h-[130px] modern-card animate-fade-in-up stagger-5">
                <p class="text-[16px] font-bold tracking-tight text-black dark:text-white">Peringatan</p>
                <p class="text-[48px] font-[900] text-black dark:text-white text-right leading-none">{{ $totalPeringatan }}</p>
            </div>
        </div>

        <!-- Notifikasi Terkini -->
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] md:p-[24px] flex flex-col flex-1 relative z-10 modern-card animate-fade-in-up stagger-6">
            <h3 class="text-[20px] font-bold tracking-tight mb-[16px] text-black dark:text-white">Notifikasi Terkini</h3>
            
            <div class="flex flex-col gap-[12px] flex-1 w-full max-w-full">
                @foreach($notifikasi as $notif)
                @php
                    $statusType = ucfirst(strtolower($notif->alert_level));
                    
                    $bgColor = $statusType === 'Bahaya' ? 'bg-[#e02424]' : ($statusType === 'Waspada' ? 'bg-[#D8C726]' : 'bg-[#6BBF6B]');
                    $dateObj = \Carbon\Carbon::parse($notif->created_at, 'UTC')->setTimezone('Asia/Jakarta');
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
                @endforeach
                
                @for($i = $notifikasi->count(); $i < 3; $i++)
                <div class="bg-transparent border border-dashed border-[#d1d5db] dark:border-[rgba(255,255,255,0.1)] min-h-[56px] rounded-[14px] w-full flex items-center justify-center text-[13px] text-[#a5a5d1] font-[600] opacity-50">
                    Slot Notifikasi Kosong
                </div>
                @endfor
            </div>

            <div class="flex justify-end mt-auto pt-[16px]">
                <a href="/peringatan" class="bg-[#9292C5] text-white px-5 py-2 rounded-[10px] font-bold text-[13px] hover:bg-[#8585b8] hover:shadow-md transition-all shadow-sm">
                    Selengkapnya
                </a>
            </div>
        </div>

    </div>

    <!-- Card Kontrol Aktuator (Sync with Pengaturan) - Full Width -->
    <div class="col-span-1 xl:col-span-12 bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] md:p-[28px] shadow-sm flex flex-col transition-colors duration-300 modern-card animate-fade-in-up stagger-7"
         x-data="{
             buzzer: {{ $actuatorStates['buzzer'] ? 'true' : 'false' }},
             pompa: {{ $actuatorStates['pompa'] ? 'true' : 'false' }},
             led: {{ $actuatorStates['led'] ? 'true' : 'false' }},
             isSaving: false,
             saveActuators() {
                 this.isSaving = true;
                 fetch('/api/actuators', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json' },
                     body: JSON.stringify({ buzzer: this.buzzer, pompa: this.pompa, led: this.led })
                 })
                 .then(res => res.json())
                 .then(data => {
                     this.isSaving = false;
                 })
                 .catch(err => {
                     this.isSaving = false;
                     console.error('Gagal menghubungi server!');
                 });
             }
         }">
        <h3 class="text-[20px] font-bold tracking-tight text-black dark:text-white mb-[20px]">Kontrol Aktuator Live</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-[20px]">
            <div class="flex items-center justify-between bg-white dark:bg-[#1a1b24] p-5 rounded-[16px] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] shadow-sm">
                <div>
                    <p class="font-[800] text-[15px] text-black dark:text-white mb-1">Alarm Buzzer</p>
                    <p class="text-[13px] font-[600] text-[#a5a5d1]">Berbunyi saat bahaya</p>
                </div>
                <div @click="buzzer = !buzzer; saveActuators()" class="w-[42px] h-[24px] rounded-full relative cursor-pointer shadow-sm transition-colors duration-300 shrink-0" :class="buzzer ? 'bg-[#9292C5]' : 'bg-[#C8C8E1]'">
                    <div class="w-[18px] h-[18px] bg-white rounded-full absolute top-[3px] left-[3px] shadow-sm transition-transform duration-300 ease-in-out" :class="buzzer ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-white dark:bg-[#1a1b24] p-5 rounded-[16px] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] shadow-sm">
                <div>
                    <p class="font-[800] text-[15px] text-black dark:text-white mb-1">Pompa Air</p>
                    <p class="text-[13px] font-[600] text-[#a5a5d1]">Menyedot air otomatis</p>
                </div>
                <div @click="pompa = !pompa; saveActuators()" class="w-[42px] h-[24px] rounded-full relative cursor-pointer shadow-sm transition-colors duration-300 shrink-0" :class="pompa ? 'bg-[#9292C5]' : 'bg-[#C8C8E1]'">
                    <div class="w-[18px] h-[18px] bg-white rounded-full absolute top-[3px] left-[3px] shadow-sm transition-transform duration-300 ease-in-out" :class="pompa ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                </div>
            </div>

            <div class="flex items-center justify-between bg-white dark:bg-[#1a1b24] p-5 rounded-[16px] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] shadow-sm">
                <div>
                    <p class="font-[800] text-[15px] text-black dark:text-white mb-1">Lampu LED</p>
                    <p class="text-[13px] font-[600] text-[#a5a5d1]">Indikator visual</p>
                </div>
                <div @click="led = !led; saveActuators()" class="w-[42px] h-[24px] rounded-full relative cursor-pointer shadow-sm transition-colors duration-300 shrink-0" :class="led ? 'bg-[#9292C5]' : 'bg-[#C8C8E1]'">
                    <div class="w-[18px] h-[18px] bg-white rounded-full absolute top-[3px] left-[3px] shadow-sm transition-transform duration-300 ease-in-out" :class="led ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- In-App Toast Notification (Bottom Right) -->
    <div x-show="showToast" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-10 opacity-0" style="display: none;" class="fixed bottom-[30px] right-[30px] bg-white dark:bg-[#20212a] border-l-[4px] shadow-[0_10px_40px_rgba(0,0,0,0.15)] dark:shadow-[0_10px_40px_rgba(0,0,0,0.5)] rounded-[16px] p-4 z-[9999] flex items-start gap-4 max-w-sm w-full" :class="toastBorderColor">
        <div class="flex-1 pt-0.5">
            <h4 class="font-[800] text-[15px] mb-[2px] tracking-tight" :class="toastTitleColor" x-text="toastTitle">ðŸš¨ PERINGATAN DINI!</h4>
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
                id: {{ $devices->first()->id ?? 0 }},
                name: '{{ $devices->first()->name ?? "EWS Belum Tersedia" }}',
                lokasi: '{{ ($devices->first() && $devices->first()->station) ? $devices->first()->station->location : "Lokasi Belum Diatur" }}',
                levelAir: 0,
                status: 'Menunggu',
                statusHujan: 'Cerah',
                terakhirUpdate: '...'
            },
            changeDevice(id, name, lokasi) {
                this.device.id = id;
                this.device.name = name;
                this.device.lokasi = lokasi;
                // When API is ready, we would fetch data specifically for this.device.id
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
            chartData: {!! json_encode($historyData) !!},
            currentRange: 'Live',
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
                if (status === 'Hujan') return '#9292C5';
                if (status === 'Menunggu') return '#9292C5';
                return '#6BBF6B';
            },
            recalculateYAxis() {
                if (!this.chartData || this.chartData.length === 0 || !this.chartInstance) return;
                let maxDataVal = Math.max(...this.chartData.map(d => d.y));
                let dynamicMax = 50;
                if (maxDataVal <= 50) dynamicMax = 100;
                else if (maxDataVal <= 100) dynamicMax = 200;
                else dynamicMax = 400;
                
                if (this.lastDynamicMax !== dynamicMax) {
                    this.chartInstance.updateOptions({ yaxis: { max: dynamicMax, min: 0, tickAmount: 2 } }, false, false);
                    this.lastDynamicMax = dynamicMax;
                }
            },
            fetchChartHistory(range) {
                if(this.currentRange === range) return;
                this.currentRange = range;
                
                if(range === 'Live') {
                    fetch(`/api/chart-history?range=live`)
                        .then(res => res.json())
                        .then(res => {
                            if(res.data) {
                                this.chartData = res.data;
                                
                                let maxDataVal = Math.max(...this.chartData.map(d => d.y));
                                let dynamicMax = 50;
                                if (maxDataVal <= 50) dynamicMax = 100;
                                else if (maxDataVal <= 100) dynamicMax = 200;
                                else dynamicMax = 400;
                                this.lastDynamicMax = dynamicMax;
                                
                                this.chartInstance.updateOptions({ 
                                    yaxis: { max: dynamicMax, min: 0, tickAmount: 2 },
                                    xaxis: { range: {{ ($intervalMs > 0 ? $intervalMs : 2000) * 20 }} } 
                                }, false, false);
                                
                                this.chartInstance.updateSeries([{ data: this.chartData }], false);
                            }
                        })
                        .catch(e => console.error("Gagal menarik live history", e));
                    return;
                }
                
                // Ambil history
                let mapping = {'1 Jam':'1h', '5 Jam':'5h', '1 Hari':'1d', '1 Minggu':'1w', '1 Bulan':'1m'};
                let apiRange = mapping[range] || '1h';
                
                fetch(`/api/chart-history?range=${apiRange}`)
                    .then(res => res.json())
                    .then(res => {
                        if(res.data) {
                            this.chartData = res.data;
                            
                            // Kalkulasi sumbu Y
                            let maxDataVal = Math.max(...this.chartData.map(d => d.y));
                            let dynamicMax = 50;
                            if (maxDataVal <= 50) dynamicMax = 100;
                            else if (maxDataVal <= 100) dynamicMax = 200;
                            else dynamicMax = 400;
                            this.lastDynamicMax = dynamicMax;
                            
                            // Update opsi grafik sekaligus (Sumbu Y dan X) untuk mencegah overwrite
                            this.chartInstance.updateOptions({ 
                                yaxis: { max: dynamicMax, min: 0, tickAmount: 2 },
                                xaxis: { min: undefined, max: undefined, range: undefined } 
                            }, false, false);
                            
                            this.chartInstance.updateSeries([{ data: this.chartData }], false);
                        }
                    })
                    .catch(e => console.error("Gagal menarik history", e));
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
                        series: [{ name: 'Jarak (cm)', data: this.chartData }],
                        chart: {
                            type: 'area',
                            height: 140,
                            animations: { enabled: true, easing: 'easeinout', speed: 500, dynamicAnimation: { speed: 500 } },
                            toolbar: { show: false },
                            zoom: { enabled: false },
                            parentHeightOffset: 0,
                            sparkline: { enabled: false }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 4, colors: ['#9292C5'] },
                        fill: { 
                            type: 'gradient', 
                            gradient: { 
                                shadeIntensity: 1, 
                                opacityFrom: 0.6, 
                                opacityTo: 0.0, 
                                stops: [0, 100], 
                                colorStops: [
                                    { offset: 0, color: '#9292C5', opacity: 0.5 }, 
                                    { offset: 100, color: '#9292C5', opacity: 0.0 }
                                ] 
                            } 
                        },
                        xaxis: { 
                            type: 'datetime', 
                            range: {{ ($intervalMs > 0 ? $intervalMs : 2000) * 20 }}, 
                            labels: { show: false }, 
                            axisBorder: { show: false }, 
                            axisTicks: { show: false }, 
                            tooltip: { enabled: false }, 
                            crosshairs: { 
                                show: true,
                                position: 'back',
                                stroke: { color: '#9292C5', width: 1, dashArray: 4 }
                            } 
                        },
                        markers: {
                            size: 0,
                            colors: ['#fff'],
                            strokeColors: '#9292C5',
                            strokeWidth: 3,
                            hover: { size: 6, sizeOffset: 2 }
                        },
                        yaxis: { 
                            max: 50, 
                            min: 0, 
                            tickAmount: 2,
                            labels: { 
                                show: true, 
                                style: { colors: textColor, fontWeight: 700, fontSize: '11px', fontFamily: '"Plus Jakarta Sans", sans-serif' },
                                formatter: function(val) { return Math.round(val); }
                            },
                            crosshairs: {
                                show: true,
                                position: 'back',
                                stroke: { color: '#9292C5', width: 1, dashArray: 4 }
                            }
                        },
                        grid: { 
                            show: true, 
                            borderColor: gridColor, 
                            strokeDashArray: 4, 
                            padding: { left: 15, right: 10, top: 0, bottom: 0 },
                            xaxis: { lines: { show: false } }, 
                            yaxis: { lines: { show: true } }
                        },
                        tooltip: { 
                            theme: isDark ? 'dark' : 'light', 
                            marker: { show: false }, 
                            x: { 
                                formatter: function(val, opts) {
                                    if (opts && opts.w && opts.w.config.series[opts.seriesIndex].data[opts.dataPointIndex]) {
                                        let meta = opts.w.config.series[opts.seriesIndex].data[opts.dataPointIndex].metaTime;
                                        if (meta) return meta;
                                    }
                                    let d = new Date(val);
                                    return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0') + ':' + d.getSeconds().toString().padStart(2, '0');
                                } 
                            },
                            y: { formatter: function(val) { return Math.round(val) + " cm" } } 
                        }
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
                                triggerNotif('PERINGATAN CUACA!', `Terdeteksi hujan turun. Jarak Air: ${jarak}cm. Pantau terus ketinggian air.`, {
                                    border: 'border-[#9292C5]', iconBg: 'bg-[#E5E5EF] dark:bg-[rgba(146,146,197,0.15)]', iconColor: 'text-[#7b7bb2] dark:text-[#9292C5]', titleColor: 'text-[#9292C5]'
                                });
                            }
                            
                            this.lastStatus = this.device.status;
                            this.lastRainStatus = this.device.statusHujan;

                            // Gunakan Unix timestamp dari backend agar sinkron 100% dengan data histori (mencegah grafik lompat/reset karena zona waktu)
                            let newX = data.timestamp ? data.timestamp : new Date().getTime();
                            let dbDate = data.waktu ? new Date(data.waktu) : new Date(); // Hanya untuk parsing jam meta
                            let metaTimeStr = dbDate.getHours().toString().padStart(2, '0') + ':' + dbDate.getMinutes().toString().padStart(2, '0') + ':' + dbDate.getSeconds().toString().padStart(2, '0');
                            
                            let lastX = this.chartData.length > 0 ? this.chartData[this.chartData.length - 1].x : 0;
                            
                            // HANYA tambahkan titik baru jika waktunya benar-benar bergerak maju (mencegah duplikasi saat offline)
                            if (newX > lastX) {
                                this.chartData.push({ x: newX, y: jarak, metaTime: metaTimeStr });
                                
                                // Batasi memori array
                                let maxArraySize = this.currentRange === 'Live' ? 20 : 1500;
                                if (this.chartData.length > maxArraySize) this.chartData.shift();
                                
                                // Hitung adaptasi meteran grafik (Dynamic Max)
                                let maxDataVal = Math.max(...this.chartData.map(d => d.y));
                                let dynamicMax = 50;
                                if (maxDataVal <= 50) dynamicMax = 100;
                                else if (maxDataVal <= 100) dynamicMax = 200;
                                else dynamicMax = 400;
                                
                                let intervalRaw = data.interval ? parseInt(data.interval) : 10;
                                window.chartInterval = intervalRaw;

                                let optionsToUpdate = {};
                                let needsOptionUpdate = false;
                                
                                if (this.lastDynamicMax !== dynamicMax) {
                                    optionsToUpdate.yaxis = { max: dynamicMax, min: 0, tickAmount: 2 };
                                    this.lastDynamicMax = dynamicMax;
                                    needsOptionUpdate = true;
                                }
                                
                                if (this.chartInstance) {
                                    if (needsOptionUpdate) {
                                        this.chartInstance.updateOptions(optionsToUpdate, false, true);
                                    }
                                    this.chartInstance.updateSeries([{ name: 'Jarak', data: this.chartData }]);
                                }
                            }
                            
                            // Waktu Update Real
                            let timeStr = new Date().getHours().toString().padStart(2, '0') + ':' + new Date().getMinutes().toString().padStart(2, '0');
                            this.device.terakhirUpdate = timeStr;
                            
                            let intervalMs = (window.chartInterval || 2) * 1000;
                            if (window.sensorTimer) clearTimeout(window.sensorTimer);
                            window.sensorTimer = setTimeout(fetchSensor, intervalMs);
                        })
                        .catch(err => {
                            this.isOnline = false;
                            console.error('Sensor fetch error:', err);
                            if (window.sensorTimer) clearTimeout(window.sensorTimer);
                            window.sensorTimer = setTimeout(fetchSensor, 5000);
                        });
                };
                
                fetchSensor();
            },
            destroy() {
                // Bersihkan timer dan chart saat pindah halaman agar tidak menumpuk (karena fitur SPA Swup)
                if (window.sensorTimer) {
                    clearTimeout(window.sensorTimer);
                    window.sensorTimer = null;
                }
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                    this.chartInstance = null;
                }
            }
        }));
    });
</script>
@endpush
