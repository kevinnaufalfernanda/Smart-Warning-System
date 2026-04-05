@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-12 gap-[24px] pb-4 items-stretch">
    
    <!-- Status Air Card -->
    <div class="col-span-1 md:col-span-12 xl:col-span-4 flex flex-col items-stretch">
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] rounded-[24px] p-[28px] md:p-[32px] flex flex-col relative h-full border border-transparent dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300">
            
            <div class="flex justify-between items-start relative z-50 w-full mb-[8px]" x-data="{ 
                open: false, 
                currentEwsId: 1,
                get device() {
                    return $store.iot.devices.find(d => d.id === this.currentEwsId) || $store.iot.devices[0];
                },
                get isOnline() { return true; },
                get gaugeColor() {
                    if (this.device.status === 'Bahaya') return '#e02424';
                    if (this.device.status === 'Siaga') return '#f59e0b';
                    return '#6BBF6B';
                },
                get gaugeShadow() {
                    if (this.device.status === 'Bahaya') return 'rgba(224,36,36,0.3)';
                    if (this.device.status === 'Siaga') return 'rgba(245,158,11,0.3)';
                    return 'rgba(107,191,107,0.3)';
                }
            }">
                <div>
                    <h3 class="text-[22px] font-bold tracking-tight mb-[12px] text-black dark:text-white">Status Air</h3>
                    
                    <div class="relative inline-block mb-[12px] z-[100]">
                        <button @click="open = !open" @click.outside="open = false" class="bg-[#9292C5] text-white px-[16px] py-[6px] rounded-[10px] text-[13px] font-bold flex items-center gap-2 hover:bg-[#8585b8] transition-colors cursor-pointer shadow-sm">
                            <span x-text="device.name">EWS 1</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition style="display: none;" class="absolute left-0 mt-2 w-[140px] bg-white dark:bg-[#2e2f3a] rounded-[12px] shadow-[0_4px_20px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.4)] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] py-2 z-[100]">
                            <template x-for="ews in $store.iot.devices" :key="ews.id">
                                <button @click="currentEwsId = ews.id; open = false" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-black dark:text-white hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors relative z-10" x-text="ews.name"></button>
                            </template>
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

            <!-- Gauge Area -->
            <div class="flex-1 flex flex-col items-center justify-center z-10 w-full mt-[40px] mb-[16px]" x-data="{ get device() { return $store.iot.devices.find(d => d.id === Alpine.$data(document.querySelector('.flex.justify-between.items-start.relative')).currentEwsId) || $store.iot.devices[0]; }, get gaugeColor() { return device.status === 'Bahaya' ? '#e02424' : (device.status === 'Siaga' ? '#f59e0b' : '#6BBF6B'); } }">
                <div class="relative flex items-center justify-center w-[160px] h-[160px] bg-transparent rounded-full border-[8px] p-[4px] shadow-[0_4px_32px_rgba(107,191,107,0.25)] flex-shrink-0 transition-colors duration-500" :style="`border-color: ${gaugeColor}; box-shadow: 0 4px 32px ${gaugeColor}40`">
                    <div class="text-center w-full h-full flex items-center justify-center bg-white dark:bg-[#2e2f3a] rounded-full transition-colors duration-300">
                        <div class="flex items-baseline">
                            <span class="text-[42px] font-bold leading-none tracking-tight transition-colors duration-500" :style="`color: ${gaugeColor}`" x-text="device.levelAir">10</span>
                            <span class="text-[20px] font-bold leading-none tracking-tight ml-0.5 transition-colors duration-500" :style="`color: ${gaugeColor}`">cm</span>
                        </div>
                    </div>
                </div>
                <p class="text-[32px] font-bold mt-[24px] tracking-wide transition-colors duration-500" :style="`color: ${gaugeColor}; text-shadow: 0 4px 12px ${gaugeColor}66`" x-text="device.status">Aman</p>
            </div>
            
            <!-- Real Chart Area with Y-Axis -->
            <div class="w-full h-[120px] mt-[32px] mb-[48px] relative z-0 flex items-stretch pr-[12px]" x-data="{ hovered: null, hoverX: 0, hoverY: 0, hoverText: '' }">
                
                <!-- Y-Axis Labels & Grid -->
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none w-full">
                    <!-- 30cm -->
                    <div class="flex items-center w-full translate-y-[-50%]">
                        <span class="w-[32px] text-right text-[10px] font-[700] text-[#9292C5] dark:text-[#a5a5d1] opacity-80 uppercase leading-none">30cm</span>
                        <div class="flex-1 border-t border-dashed border-black/30 dark:border-[rgba(255,255,255,0.2)] ml-3"></div>
                    </div>
                    <!-- 20cm -->
                    <div class="flex items-center w-full translate-y-[-50%]">
                        <span class="w-[32px] text-right text-[10px] font-[700] text-[#9292C5] dark:text-[#a5a5d1] opacity-80 uppercase leading-none">20cm</span>
                        <div class="flex-1 border-t border-dashed border-black/30 dark:border-[rgba(255,255,255,0.2)] ml-3"></div>
                    </div>
                    <!-- 10cm -->
                    <div class="flex items-center w-full translate-y-[-50%]">
                        <span class="w-[32px] text-right text-[10px] font-[700] text-[#9292C5] dark:text-[#a5a5d1] opacity-80 uppercase leading-none">10cm</span>
                        <div class="flex-1 border-t border-dashed border-black/30 dark:border-[rgba(255,255,255,0.2)] ml-3"></div>
                    </div>
                    <!-- 0cm -->
                    <div class="flex items-center w-full translate-y-[-50%]">
                        <span class="w-[32px] text-right text-[10px] font-[700] text-[#9292C5] dark:text-[#a5a5d1] opacity-80 uppercase leading-none">0cm</span>
                        <div class="flex-1 border-t border-solid border-black/40 dark:border-[rgba(255,255,255,0.3)] ml-3"></div>
                    </div>
                </div>

                <!-- Inner Chart Box -->
                <div class="relative w-full h-full ml-[42px]">
                    <!-- Tooltip untuk Jam -->
                    <div x-show="hovered !== null" style="display: none;"
                         class="absolute z-50 pointer-events-none"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-3"
                         :style="`left: ${hoverX}%; top: calc(${(hoverY / 30) * 100}% - 12px);`">
                         
                         <div class="bg-[#9292C5] text-white shadow-[0_4px_16px_rgba(146,146,197,0.4)] text-[11px] font-bold px-2 py-1 rounded-[6px] whitespace-nowrap transform transition-none"
                              :class="hoverX === 0 ? '-translate-y-full translate-x-0' : hoverX === 100 ? '-translate-y-full -translate-x-[100%]' : '-translate-y-full -translate-x-1/2'">
                             <span x-text="hoverText"></span>
                         </div>
                    </div>

                    @php
                        // 12 Titik yang merepresentasikan rentang 12 Jam (misalnya: Pagi - Sore)
                        $points = [
                            26, 25.5, 25, 24, 23.5, 22, 20, 18, 16, 15, 17, 21
                        ];
                        $numPoints = count($points);
                        $pathSegments = [];
                        foreach($points as $i => $y) {
                            $x = round(($i / ($numPoints - 1)) * 100, 2);
                            $pathSegments[] = ($i === 0 ? 'M' : 'L') . $x . ',' . $y;
                        }
                        $pathString = implode(' ', $pathSegments);
                        $fillPathString = $pathString . " L100,30 L0,30 Z";
                    @endphp

                    <svg viewBox="0 0 100 30" preserveAspectRatio="none" class="w-full h-full text-[#281682] dark:text-[#9292C5] overflow-visible">
                        <defs>
                            <linearGradient id="waveGradDash" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="currentColor" stop-opacity="0.2"></stop>
                                <stop offset="100%" stop-color="currentColor" stop-opacity="0.0"></stop>
                            </linearGradient>
                        </defs>
                        <path d="{{ $fillPathString }}" fill="url(#waveGradDash)"/>
                        <path d="{{ $pathString }}" fill="none" class="text-[#9292C5]" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        
                        <!-- Interactive Graph Nodes (12 Hourly) -->
                        @foreach($points as $i => $y)
                            @php
                                $x = round(($i / ($numPoints - 1)) * 100, 2);
                                $displayHour = $i + 6; // Mulai dari 06:00 WIB
                                $hour = str_pad($displayHour, 2, '0', STR_PAD_LEFT) . ':00 WIB';
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" :r="hovered === {{ $i }} ? 2.5 : 1.2" :fill="hovered === {{ $i }} ? '#9292C5' : 'currentColor'" class="cursor-pointer transition-all duration-200" @mouseenter="hovered = {{ $i }}; hoverX = {{ $x }}; hoverY = {{ $y }}; hoverText = '{{ $hour }}'" @mouseleave="hovered = null" />
                        @endforeach
                    </svg>
                </div>
            </div>

            <!-- Texts Bottom -->
            <div class="flex justify-between items-center z-10 px-[12px]">
                <div class="text-center">
                    <p class="text-black dark:text-white font-bold text-[14px] mb-[1px] tracking-tight">Jarak Sensor</p>
                    <p class="text-black dark:text-[#a5a5d1] font-medium text-[14px]">12 cm</p>
                </div>
                <div class="text-center">
                    <p class="text-black dark:text-white font-bold text-[14px] mb-[1px] tracking-tight">Terakhir Update</p>
                    <p class="text-black dark:text-[#a5a5d1] font-medium text-[14px]">10.24</p>
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
                <!-- Fictional Notification Data -->
                <div class="bg-[#e7e7f1] dark:bg-[rgba(255,255,255,0.1)] min-h-[64px] rounded-[16px] w-full flex items-center px-[24px] text-[15px] text-black dark:text-white font-semibold gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#6BBF6B] shrink-0"></div>
                    <span class="truncate">EWS 1 berjalan normal, ketinggian air terpantau stabil pada 10cm.</span>
                    <span class="ml-auto text-[#9292C5] dark:text-[#a5a5d1] font-semibold text-[13px] shrink-0">10.24 WIB</span>
                </div>
                <div class="bg-[#e7e7f1] dark:bg-[rgba(255,255,255,0.1)] min-h-[64px] rounded-[16px] w-full flex items-center px-[24px] text-[15px] text-black dark:text-white font-semibold gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#e02424] shrink-0"></div>
                    <span class="truncate">EWS 3 kehilangan koneksi, unit terdeteksi offline.</span>
                    <span class="ml-auto text-[#9292C5] dark:text-[#a5a5d1] font-semibold text-[13px] shrink-0">08:00 WIB</span>
                </div>
                <div class="bg-[#e7e7f1] dark:bg-[rgba(255,255,255,0.1)] min-h-[64px] rounded-[16px] w-full flex items-center px-[24px] text-[15px] text-black dark:text-white font-semibold gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#D8C726] shrink-0"></div>
                    <span class="truncate">Baterai cadangan pada NodeMCU EWS 2 menipis.</span>
                    <span class="ml-auto text-[#9292C5] dark:text-[#a5a5d1] font-semibold text-[13px] shrink-0">kemarin</span>
                </div>
                <div class="bg-[#e7e7f1] dark:bg-[rgba(255,255,255,0.1)] min-h-[64px] rounded-[16px] w-full flex items-center px-[24px] text-[15px] text-black dark:text-white font-semibold gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#6BBF6B] shrink-0"></div>
                    <span class="truncate">EWS 1 kembali terhubung ke jaringan dengan stabil.</span>
                    <span class="ml-auto text-[#9292C5] dark:text-[#a5a5d1] font-semibold text-[13px] shrink-0">2 hari yang lalu</span>
                </div>
                <div class="bg-[#e7e7f1] dark:bg-[rgba(255,255,255,0.1)] min-h-[64px] rounded-[16px] w-full flex items-center px-[24px] text-[15px] text-black dark:text-white font-semibold gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#6BBF6B] shrink-0"></div>
                    <span class="truncate">Update sistem firmware pada perangkat EWS 2 berhasil.</span>
                    <span class="ml-auto text-[#9292C5] dark:text-[#a5a5d1] font-semibold text-[13px] shrink-0">3 hari yang lalu</span>
                </div>
            </div>

            <div class="flex justify-end mt-[28px]">
                <a href="/peringatan" class="bg-[#9292C5] text-white px-[20px] py-[8px] rounded-[12px] font-bold text-[14px] hover:bg-[#8585b8] hover:-translate-y-0.5 transition-all text-center inline-block cursor-pointer tracking-wide shadow-[0_4px_16px_rgba(146,146,197,0.4)]">
                    Selengkapnya
                </a>
            </div>
        </div>

        <!-- 3 Stats Cards Row (auto height) -->
        <div class="grid grid-cols-3 gap-[24px] shrink-0 min-h-[160px] relative z-0">
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300 rounded-[20px] flex flex-col items-center justify-center text-center py-[32px]">
                <p class="text-[22px] font-bold text-black dark:text-white mb-1">Perangkat</p>
                <p class="text-[42px] font-bold tracking-tight leading-tight text-black dark:text-[#9292C5]">3</p>
            </div>
            
            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300 rounded-[20px] flex flex-col items-center justify-center text-center py-[32px]">
                <p class="text-[22px] font-bold text-black dark:text-white mb-1">Data</p>
                <p class="text-[42px] font-bold tracking-tight leading-tight text-black dark:text-[#9292C5]">241</p>
            </div>

            <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300 rounded-[20px] flex flex-col items-center justify-center text-center py-[32px]">
                <p class="text-[22px] font-bold text-black dark:text-white mb-1">Peringatan</p>
                <p class="text-[42px] font-bold tracking-tight leading-tight text-black dark:text-[#9292C5]">16</p>
            </div>
        </div>

    </div>

</div>
@endsection
