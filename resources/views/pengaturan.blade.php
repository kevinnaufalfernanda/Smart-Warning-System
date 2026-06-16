@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
@php
    $lastStationId = session('last_station_id');
    $selectedStation = null;
    
    if ($lastStationId) {
        $selectedStation = $stations->where('id', $lastStationId)->first();
    }
    if (!$selectedStation) {
        $selectedStation = $stations->first();
    }
    
    // Default values
    $amanVal = 12;
    $waspadaVal = 12;
    $bahayaVal = 8;
    
    if ($selectedStation && $selectedStation->thresholds->count() > 0) {
        $bahaya = $selectedStation->thresholds->where('level_label', 'BAHAYA')->first();
        $waspada = $selectedStation->thresholds->where('level_label', 'WASPADA')->first();
        $aman = $selectedStation->thresholds->where('level_label', 'AMAN')->first();
        if ($bahaya) $bahayaVal = $bahaya->water_max_cm;
        if ($waspada) $waspadaVal = $waspada->water_max_cm;
        if ($aman && $aman->water_max_cm != 400) {
            $amanVal = $aman->water_max_cm;
        } else {
            $amanVal = $waspadaVal; 
        }
    }

    $stationsData = $stations->map(function($st) {
        $b = 8; $w = 12; $a = 12;
        if ($st->thresholds->count() > 0) {
            $bh = $st->thresholds->where('level_label', 'BAHAYA')->first();
            $ws = $st->thresholds->where('level_label', 'WASPADA')->first();
            $am = $st->thresholds->where('level_label', 'AMAN')->first();
            if ($bh) $b = $bh->water_max_cm;
            if ($ws) {
                $w = $ws->water_max_cm;
                $a = $w;
            }
            if ($am && $am->water_max_cm != 400) {
                $a = $am->water_max_cm;
            }
        }
        return [
            'id' => $st->id,
            'name' => $st->name,
            'bahaya' => $b,
            'waspada' => $w,
            'aman' => $a
        ];
    })->keyBy('id')->toArray();
@endphp

<div x-data="{ 
    showGuide: false, 
    stationsData: {{ json_encode($stationsData) }},
    amanVal: {{ $amanVal }}, 
    waspadaVal: {{ $waspadaVal }}, 
    bahayaVal: {{ $bahayaVal }},
    get totalVal() { 
        let sum = (Number(this.amanVal) || 0) + (Number(this.waspadaVal) || 0) + (Number(this.bahayaVal) || 0);
        return sum === 0 ? 1 : sum;
    },
    updateThresholds(stationId) {
        if(this.stationsData[stationId]) {
            this.amanVal = this.stationsData[stationId].aman;
            this.waspadaVal = this.stationsData[stationId].waspada;
            this.bahayaVal = this.stationsData[stationId].bahaya;
        }
    }
}" class="h-full">
<form action="{{ route('pengaturan.threshold') }}" method="POST" class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[32px] shadow-sm mb-[24px] transition-colors duration-300 animate-fade-in-up stagger-1">
    @csrf
    
    @if(session('success'))
    <div class="mb-4 bg-[#e2f1e2] dark:bg-[#344034] text-[#6BBF6B] px-4 py-3 rounded-[12px] font-bold text-[14px]">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.15)] text-[#e02424] px-4 py-3 rounded-[12px] font-bold text-[14px]">
        {{ session('error') }}
    </div>
    @endif

    <!-- Header with EWS selector -->
    <div class="flex justify-between items-center mb-[24px]">
        <div class="flex items-center gap-4">
            <h3 class="text-[22px] font-bold tracking-tight text-black dark:text-white">Konfigurasi Threshold</h3>
            <button type="button" @click="showGuide = true" class="bg-[#9292C5]/10 text-[#9292C5] dark:text-[#a5a5d1] hover:bg-[#9292C5]/20 px-[16px] py-[6px] rounded-[10px] text-[12px] font-bold flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Panduan & Rumus
            </button>
        </div>
        <!-- EWS Dropdown Selector -->
        <div class="relative z-20 flex items-center" x-data="{ dropdownOpen: false, selectedStationId: '{{ $selectedStation ? $selectedStation->id : '' }}', selectedStationName: '{{ $selectedStation ? $selectedStation->name : 'Pilih EWS' }}' }">
            <input type="hidden" name="station_id" x-model="selectedStationId">
            <button type="button" @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-[#9292C5] text-white pl-[20px] pr-[16px] py-[8px] rounded-[12px] text-[13px] font-[700] hover:bg-[#8585b8] hover:shadow-md transition-all duration-300 cursor-pointer shadow-sm outline-none border border-transparent flex items-center gap-3 min-w-[120px] justify-between">
                <span x-text="selectedStationName"></span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="dropdownOpen" x-transition class="absolute right-0 top-full mt-2 w-full min-w-[140px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                @foreach($stations as $station)
                <button type="button" @click="selectedStationId = '{{ $station->id }}'; selectedStationName = '{{ $station->name }}'; updateThresholds('{{ $station->id }}'); dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors">
                    {{ $station->name }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="w-full h-[12px] rounded-full flex overflow-hidden mb-[16px]">
        <div class="bg-[#6BBF6B] transition-all duration-500" :style="`width: ${(Number(amanVal) / totalVal) * 100}%`"></div>
        <div class="bg-[#D8C726] transition-all duration-500" :style="`width: ${(Number(waspadaVal) / totalVal) * 100}%`"></div>
        <div class="bg-[#e02424] transition-all duration-500" :style="`width: ${(Number(bahayaVal) / totalVal) * 100}%; box-shadow: inset 0 0 10px rgba(0,0,0,0.2);`"></div>
    </div>
    
    <div class="flex justify-between text-[13px] font-[700] mb-[32px] px-2">
        <div class="flex items-center gap-2 w-1/2 text-[#6BBF6B]"><div class="w-2.5 h-2.5 rounded-full bg-[#6BBF6B]"></div>Aman</div>
        <div class="flex items-center gap-2 w-1/4 text-[#D8C726]"><div class="w-2.5 h-2.5 rounded-full bg-[#D8C726]"></div>Siaga</div>
        <div class="flex items-center gap-2 w-1/4 text-[#e02424]"><div class="w-2.5 h-2.5 rounded-full bg-[#e02424]"></div>Bahaya</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
        <!-- Aman Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm hover:shadow-md hover:border-[#9292C5] transition-all duration-300">
            <h4 class="font-[800] text-[#6BBF6B] mb-1">Aman</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak > X cm</p>
            <div class="flex overflow-hidden rounded-[12px] bg-[#F9F9FB] dark:bg-[#2a2b36] border border-transparent focus-within:border-[#9292C5] focus-within:ring-2 focus-within:ring-[#9292C5]/20 transition-all duration-300">
                <input type="number" name="batas_aman" x-model.number="amanVal" class="w-full bg-transparent p-[12px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#9292C5] text-white px-[16px] py-[12px] font-[800] text-[14px] flex items-center">cm</div>
            </div>
        </div>
        <!-- Siaga Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm hover:shadow-md hover:border-[#9292C5] transition-all duration-300">
            <h4 class="font-[800] text-[#D8C726] mb-1">Siaga</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak <= Aman, > Bahaya</p>
            <div class="flex overflow-hidden rounded-[12px] bg-[#F9F9FB] dark:bg-[#2a2b36] border border-transparent focus-within:border-[#9292C5] focus-within:ring-2 focus-within:ring-[#9292C5]/20 transition-all duration-300">
                <input type="number" name="batas_waspada" x-model.number="waspadaVal" class="w-full bg-transparent p-[12px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#9292C5] text-white px-[16px] py-[12px] font-[800] text-[14px] flex items-center">cm</div>
            </div>
        </div>
        <!-- Bahaya Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm hover:shadow-md hover:border-[#9292C5] transition-all duration-300">
            <h4 class="font-[800] text-[#e02424] mb-1">Bahaya</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak <= X cm</p>
            <div class="flex overflow-hidden rounded-[12px] bg-[#F9F9FB] dark:bg-[#2a2b36] border border-transparent focus-within:border-[#9292C5] focus-within:ring-2 focus-within:ring-[#9292C5]/20 transition-all duration-300">
                <input type="number" name="batas_bahaya" x-model.number="bahayaVal" class="w-full bg-transparent p-[12px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#9292C5] text-white px-[16px] py-[12px] font-[800] text-[14px] flex items-center">cm</div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end mt-[24px]">
        <button type="submit" class="bg-[#9292C5] text-white px-[28px] py-[10px] rounded-[12px] font-bold text-[14px] hover:bg-[#8585b8] transition-all shadow-sm hover:shadow-[0_4px_16px_rgba(146,146,197,0.35)]">
            Simpan Threshold
        </button>
    </div>
</form>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-[24px]">
    <!-- Card Konfigurasi Sensor -->
    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[32px] shadow-sm flex flex-col transition-colors duration-300 h-full animate-fade-in-up stagger-2">
        <h3 class="text-[22px] font-bold tracking-tight text-black dark:text-white mb-[24px]">Konfigurasi Sensor</h3>
        
        <form action="{{ route('pengaturan.sensor') }}" method="POST" class="flex flex-col flex-1">
            @csrf
            <div class="flex flex-col gap-[24px] flex-1">
                <div>
                    <div class="flex items-center justify-between gap-4 mb-2">
                        <span class="font-[800] text-[14px] text-black dark:text-white whitespace-nowrap">Tinggi Wadah (cm)</span>
                        <input type="number" name="tinggi_wadah" value="{{ $sensorConfig['tinggi_wadah'] ?? 20 }}" class="bg-[#F9F9FB] dark:bg-[#1a1b24] outline-none rounded-[12px] px-[16px] py-[10px] font-[800] text-[14px] w-[100px] text-black dark:text-white border border-transparent shadow-sm hover:shadow-md hover:bg-[#F3F3F3] focus:border-[#9292C5] focus:ring-2 focus:ring-[#9292C5]/20 transition-all duration-300 text-center">
                    </div>
                    <p class="text-[13px] font-[500] text-[#333] dark:text-[#a5a5d1]">Jarak dari sensor (atas) ke dasar wadah air.</p>
                </div>
                <div>
                    <div class="flex items-center justify-between gap-4 mb-2">
                        <span class="font-[800] text-[14px] text-black dark:text-white whitespace-nowrap">Interval Baca (detik)</span>
                        <input type="number" name="interval_baca" value="{{ $sensorConfig['interval_baca'] ?? 2 }}" class="bg-[#F9F9FB] dark:bg-[#1a1b24] outline-none rounded-[12px] px-[16px] py-[10px] font-[800] text-[14px] w-[100px] text-black dark:text-white border border-transparent shadow-sm hover:shadow-md hover:bg-[#F3F3F3] focus:border-[#9292C5] focus:ring-2 focus:ring-[#9292C5]/20 transition-all duration-300 text-center">
                    </div>
                    <p class="text-[13px] font-[500] text-[#333] dark:text-[#a5a5d1]">Jeda waktu antar pembacaan data sensor.</p>
                </div>
            </div>

            <div class="flex justify-end mt-[32px]">
                <button type="submit" class="bg-[#9292C5] text-white px-[28px] py-[10px] rounded-[12px] font-bold text-[14px] hover:bg-[#8585b8] transition-all shadow-sm hover:shadow-[0_4px_16px_rgba(146,146,197,0.35)] shrink-0">
                    Simpan Sensor
                </button>
            </div>
        </form>
    </div>

    <!-- Card Kontrol Aktuator -->
    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[32px] shadow-sm flex flex-col transition-colors duration-300 h-full animate-fade-in-up stagger-3"
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
        <h3 class="text-[22px] font-bold tracking-tight text-black dark:text-white mb-[24px]">Kontrol Aktuator</h3>
        
        <div class="flex flex-col gap-[20px] flex-1">
            <div class="flex items-start gap-4 bg-white dark:bg-[#1a1b24] p-4 rounded-[16px] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] shadow-sm transition-colors duration-300">
                <!-- Toggle Switch Buzzer -->
                <div @click="buzzer = !buzzer; saveActuators()" class="w-[42px] h-[24px] rounded-full relative cursor-pointer shadow-sm mt-1 transition-colors duration-300 shrink-0" :class="buzzer ? 'bg-[#9292C5]' : 'bg-[#C8C8E1]'">
                    <div class="w-[18px] h-[18px] bg-white rounded-full absolute top-[3px] left-[3px] shadow-sm transition-transform duration-300 ease-in-out" :class="buzzer ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                </div>
                <div>
                    <p class="font-[800] text-[14px] text-black dark:text-white mb-1 transition-colors duration-300">Alarm Buzzer</p>
                    <p class="text-[12px] font-[500] text-[#555] dark:text-[#a5a5d1] leading-relaxed transition-colors duration-300">Berbunyi otomatis saat bahaya.</p>
                </div>
            </div>

            <div class="flex items-start gap-4 bg-white dark:bg-[#1a1b24] p-4 rounded-[16px] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] shadow-sm transition-colors duration-300">
                <!-- Toggle Switch Pompa -->
                <div @click="pompa = !pompa; saveActuators()" class="w-[42px] h-[24px] rounded-full relative cursor-pointer shadow-sm mt-1 transition-colors duration-300 shrink-0" :class="pompa ? 'bg-[#9292C5]' : 'bg-[#C8C8E1]'">
                    <div class="w-[18px] h-[18px] bg-white rounded-full absolute top-[3px] left-[3px] shadow-sm transition-transform duration-300 ease-in-out" :class="pompa ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                </div>
                <div>
                    <p class="font-[800] text-[14px] text-black dark:text-white mb-1 transition-colors duration-300">Pompa Air</p>
                    <p class="text-[12px] font-[500] text-[#555] dark:text-[#a5a5d1] leading-relaxed transition-colors duration-300">Menyedot air jika banjir.</p>
                </div>
            </div>

            <div class="flex items-start gap-4 bg-white dark:bg-[#1a1b24] p-4 rounded-[16px] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] shadow-sm transition-colors duration-300">
                <!-- Toggle Switch LED -->
                <div @click="led = !led; saveActuators()" class="w-[42px] h-[24px] rounded-full relative cursor-pointer shadow-sm mt-1 transition-colors duration-300 shrink-0" :class="led ? 'bg-[#9292C5]' : 'bg-[#C8C8E1]'">
                    <div class="w-[18px] h-[18px] bg-white rounded-full absolute top-[3px] left-[3px] shadow-sm transition-transform duration-300 ease-in-out" :class="led ? 'translate-x-[18px]' : 'translate-x-0'"></div>
                </div>
                <div>
                    <p class="font-[800] text-[14px] text-black dark:text-white mb-1 transition-colors duration-300">Lampu LED</p>
                    <p class="text-[12px] font-[500] text-[#555] dark:text-[#a5a5d1] leading-relaxed transition-colors duration-300">Isyarat visual status air.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Panduan & Rumus -->
<template x-teleport="body">
    <div x-show="showGuide" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
         style="display: none;">
        
        <div class="absolute inset-0" @click="showGuide = false"></div>
        
        <div x-show="showGuide"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white dark:bg-[#20212a] w-full max-w-2xl rounded-[24px] p-[32px] shadow-2xl border border-transparent dark:border-[rgba(255,255,255,0.05)] z-10 flex flex-col max-h-[90vh] overflow-hidden">
            
            <div class="flex justify-between items-center mb-6 shrink-0">
                <h3 class="text-[20px] font-bold text-black dark:text-white tracking-tight">Panduan Koneksi & Rumus</h3>
                <button @click="showGuide = false" class="text-[#555] dark:text-[#a5a5d1] hover:text-[#e02424] transition-colors bg-transparent border-none p-1 cursor-pointer rounded-full hover:bg-gray-100 dark:hover:bg-[rgba(255,255,255,0.05)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="flex flex-col gap-6 overflow-y-auto pr-2 pb-2" style="scrollbar-width: thin;">
                <!-- Rumus -->
                <div class="bg-[#F9F9FB] dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] p-[20px] shadow-sm">
                    <p class="text-[16px] font-bold tracking-tight text-black dark:text-white mb-[8px]">Rumus Perhitungan</p>
                    <p class="text-[13px] text-[#555] dark:text-[#a5a5d1] mb-[12px] italic">Mengukur jarak pantulan gelombang suara ke permukaan air</p>
                    <div class="bg-[#e2f1e2] dark:bg-[rgba(34,197,94,0.1)] text-[#16a34a] dark:text-[#4ade80] px-4 py-3 rounded-[12px] font-[800] text-[14px] text-center border border-[#bbf7d0] dark:border-[rgba(34,197,94,0.2)]">
                        Level Air = Tinggi Sensor – Jarak Terukur
                    </div>
                </div>

                <!-- Panduan -->
                <div class="bg-[#F9F9FB] dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] p-[20px] shadow-sm">
                    <p class="text-[16px] font-bold tracking-tight text-black dark:text-white mb-[16px]">Panduan Koneksi NodeMCU</p>
                    <div class="text-[13px] text-[#333] dark:text-[#d1d1d6] space-y-5">
                        <div>
                            <p class="font-[800] mb-2 text-black dark:text-white">Topologi Koneksi</p>
                            <ol class="list-decimal pl-4 space-y-1">
                                <li>Sensor HC-SR04 → NodeMCU ESP8266</li>
                                <li>NodeMCU → WiFi Hotspot HP/Router</li>
                                <li>Laptop (server) ← WiFi yang sama</li>
                                <li>NodeMCU POST ke http://[IP_LAPTOP]:8000/api/sensor</li>
                            </ol>
                        </div>
                        <div>
                            <p class="font-[800] mb-2 text-black dark:text-white">Wiring HC-SR04</p>
                            <div class="grid grid-cols-[100px__20px__1fr] space-y-1">
                                <div class="font-[700]">VCC</div><div>→</div><div>5V (NodeMCU VIN)</div>
                                <div class="font-[700]">GND</div><div>→</div><div>GND</div>
                                <div class="font-[700]">TRIG</div><div>→</div><div>D1 (GPIO5)</div>
                                <div class="font-[700]">ECHO</div><div>→</div><div>D2 (GPIO4) via Voltage Divider</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
</div>
@endsection
