@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
@php
    $selectedStation = $stations->first();
    // Default values
    $amanVal = 12;
    $waspadaVal = 12;
    $bahayaVal = 8;
    
    if ($selectedStation && $selectedStation->thresholds->count() > 0) {
        $bahaya = $selectedStation->thresholds->where('level_label', 'BAHAYA')->first();
        $waspada = $selectedStation->thresholds->where('level_label', 'WASPADA')->first();
        if ($bahaya) $bahayaVal = $bahaya->water_max_cm;
        if ($waspada) $waspadaVal = $waspada->water_max_cm;
        // Aman's threshold isn't really needed for input since it's just > waspadaVal
        $amanVal = $waspadaVal; 
    }
@endphp

<form action="{{ route('pengaturan.threshold') }}" method="POST" class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[32px] shadow-sm mb-[24px] transition-colors duration-300">
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
        <h3 class="text-[22px] font-bold tracking-tight text-black dark:text-white">Konfigurasi Threshold</h3>
        <!-- EWS Dropdown Selector -->
        <div class="relative z-20">
            <select name="station_id" class="bg-[#9292C5] text-white px-[16px] py-[6px] rounded-[10px] text-[13px] font-bold flex items-center gap-2 hover:bg-[#8585b8] transition-colors cursor-pointer shadow-sm outline-none border-none">
                @foreach($stations as $station)
                    <option value="{{ $station->id }}">{{ $station->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="w-full h-[12px] rounded-full flex overflow-hidden mb-[16px]">
        <div class="bg-[#6BBF6B]" style="width: 50%;"></div>
        <div class="bg-[#D8C726]" style="width: 25%;"></div>
        <div class="bg-[#e02424]" style="width: 25%; box-shadow: inset 0 0 10px rgba(0,0,0,0.2);"></div>
    </div>
    
    <div class="flex justify-between text-[13px] font-[700] mb-[32px] px-2">
        <div class="flex items-center gap-2 w-1/2 text-[#6BBF6B]"><div class="w-2.5 h-2.5 rounded-full bg-[#6BBF6B]"></div>Aman</div>
        <div class="flex items-center gap-2 w-1/4 text-[#D8C726]"><div class="w-2.5 h-2.5 rounded-full bg-[#D8C726]"></div>Siaga</div>
        <div class="flex items-center gap-2 w-1/4 text-[#e02424]"><div class="w-2.5 h-2.5 rounded-full bg-[#e02424]"></div>Bahaya</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
        <!-- Aman Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm transition-colors duration-300">
            <h4 class="font-[800] text-[#6BBF6B] mb-1">Aman</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak > X cm</p>
            <div class="flex overflow-hidden rounded-[8px] bg-[#E5E5EF]/60 dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)]">
                <input type="number" name="batas_aman" value="{{ $amanVal }}" class="w-full bg-transparent p-[10px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#C8C8E1] dark:bg-[rgba(255,255,255,0.05)] px-4 py-[10px] text-black dark:text-white font-[800] text-[14px]">cm</div>
            </div>
        </div>
        <!-- Siaga Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm transition-colors duration-300">
            <h4 class="font-[800] text-[#D8C726] mb-1">Siaga</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak <= Aman, > Bahaya</p>
            <div class="flex overflow-hidden rounded-[8px] bg-[#E5E5EF]/60 dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)]">
                <input type="number" name="batas_waspada" value="{{ $waspadaVal }}" class="w-full bg-transparent p-[10px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#C8C8E1] dark:bg-[rgba(255,255,255,0.05)] px-4 py-[10px] text-black dark:text-white font-[800] text-[14px]">cm</div>
            </div>
        </div>
        <!-- Bahaya Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm transition-colors duration-300">
            <h4 class="font-[800] text-[#e02424] mb-1">Bahaya</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak <= X cm</p>
            <div class="flex overflow-hidden rounded-[8px] bg-[#E5E5EF]/60 dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)]">
                <input type="number" name="batas_bahaya" value="{{ $bahayaVal }}" class="w-full bg-transparent p-[10px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#C8C8E1] dark:bg-[rgba(255,255,255,0.05)] px-4 py-[10px] text-black dark:text-white font-[800] text-[14px]">cm</div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end mt-[24px]">
        <button type="submit" class="bg-[#9292C5] text-white px-[28px] py-[10px] rounded-[12px] font-bold text-[14px] hover:bg-[#8585b8] transition-all shadow-sm hover:shadow-[0_4px_16px_rgba(146,146,197,0.35)]">
            Simpan Threshold
        </button>
    </div>
</form>

<div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[32px] shadow-sm flex-1 transition-colors duration-300">
    <h3 class="text-[22px] font-bold tracking-tight text-black dark:text-white mb-[24px]">Konfigurasi Sensor</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-[48px] mb-[32px]">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <span class="font-[800] text-[14px] text-black dark:text-white whitespace-nowrap">Tinggi Sensor (cm)</span>
                <input type="number" value="20" class="bg-[#E5E5EF]/50 dark:bg-[#1a1b24] border-none outline-none rounded-full px-4 py-[6px] font-[800] text-[14px] w-[140px] text-black dark:text-white border border-transparent dark:border-[rgba(255,255,255,0.05)]">
            </div>
            <p class="text-[13px] font-[500] text-[#333] dark:text-[#a5a5d1]">Jarak dari sensor ke dasar wadah air.</p>
        </div>
        <div>
            <div class="flex items-center gap-4 mb-2">
                <span class="font-[800] text-[14px] text-black dark:text-white whitespace-nowrap">Interval Baca (menit)</span>
                <input type="number" value="10" class="bg-[#E5E5EF]/50 dark:bg-[#1a1b24] border-none outline-none rounded-full px-4 py-[6px] font-[800] text-[14px] w-[140px] text-black dark:text-white border border-transparent dark:border-[rgba(255,255,255,0.05)]">
            </div>
            <p class="text-[13px] font-[500] text-[#333] dark:text-[#a5a5d1]">Jeda waktu antar pembacaan data.</p>
        </div>
    </div>

    <div class="flex items-start justify-between">
        <div class="flex items-start gap-4">
            <!-- Toggle Switch -->
            <div x-data="{ enabled: true }" @click="enabled = !enabled" class="w-[42px] h-[24px] rounded-full relative cursor-pointer shadow-sm mt-1 transition-colors duration-300" :class="enabled ? 'bg-[#9292C5]' : 'bg-[#C8C8E1]'">
                <div class="w-[18px] h-[18px] bg-white rounded-full absolute top-[3px] shadow-sm transition-all duration-300" :class="enabled ? 'right-[3px]' : 'left-[3px]'"></div>
            </div>
            <div>
                <p class="font-[800] text-[14px] text-black dark:text-white mb-1">Aktifkan Alarm Buzzer</p>
                <p class="text-[13px] font-[500] text-[#333] dark:text-[#a5a5d1]">Buzzer akan berbunyi otomatis saat status mencapai BAHAYA.</p>
            </div>
        </div>
        
        <!-- Simpan Button -->
        <button class="bg-[#9292C5] text-white px-[28px] py-[10px] rounded-[12px] font-bold text-[14px] hover:bg-[#8585b8] transition-all shadow-sm hover:shadow-[0_4px_16px_rgba(146,146,197,0.35)] shrink-0">
            Simpan
        </button>
    </div>
</div>
@endsection
