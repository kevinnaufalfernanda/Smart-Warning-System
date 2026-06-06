@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
<div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[32px] shadow-sm mb-[24px] transition-colors duration-300">
    <!-- Header with EWS selector -->
    <div class="flex justify-between items-center mb-[24px]">
        <h3 class="text-[22px] font-bold tracking-tight text-black dark:text-white">Konfigurasi Threshold</h3>
        <!-- EWS Dropdown Selector -->
        <div x-data="{ open: false }" class="relative z-20">
            <button @click="open = !open" @click.outside="open = false" class="bg-[#9292C5] text-white px-[16px] py-[6px] rounded-[10px] text-[13px] font-bold flex items-center gap-2 hover:bg-[#8585b8] transition-colors cursor-pointer shadow-sm">
                <span>EWS 1</span>
                <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" x-transition style="display: none;" class="absolute right-0 mt-2 w-[140px] bg-white dark:bg-[#2e2f3a] rounded-[12px] shadow-[0_4px_20px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.4)] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] py-2 z-50">
                <button @click="open = false" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-black dark:text-white hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors">EWS 1</button>
                <button @click="open = false" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-black dark:text-white hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors">EWS 2</button>
                <button @click="open = false" class="w-full text-left px-4 py-2 text-[13px] font-semibold text-black dark:text-white hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors">EWS 3</button>
            </div>
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
                <input type="number" value="10" class="w-full bg-transparent p-[10px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#C8C8E1] dark:bg-[rgba(255,255,255,0.05)] px-4 py-[10px] text-black dark:text-white font-[800] text-[14px]">cm</div>
            </div>
        </div>
        <!-- Siaga Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm transition-colors duration-300">
            <h4 class="font-[800] text-[#D8C726] mb-1">Siaga</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak < X cm</p>
            <div class="flex overflow-hidden rounded-[8px] bg-[#E5E5EF]/60 dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)]">
                <input type="number" value="7" class="w-full bg-transparent p-[10px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#C8C8E1] dark:bg-[rgba(255,255,255,0.05)] px-4 py-[10px] text-black dark:text-white font-[800] text-[14px]">cm</div>
            </div>
        </div>
        <!-- Bahaya Input -->
        <div class="border-[2px] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] bg-white dark:bg-[#1a1b24] rounded-[16px] p-[20px] shadow-sm transition-colors duration-300">
            <h4 class="font-[800] text-[#e02424] mb-1">Bahaya</h4>
            <p class="text-[13px] font-[500] text-black dark:text-[#a5a5d1] mb-4">Jarak < X cm</p>
            <div class="flex overflow-hidden rounded-[8px] bg-[#E5E5EF]/60 dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)]">
                <input type="number" value="5" class="w-full bg-transparent p-[10px] text-[15px] font-[800] text-black dark:text-white outline-none border-none">
                <div class="bg-[#C8C8E1] dark:bg-[rgba(255,255,255,0.05)] px-4 py-[10px] text-black dark:text-white font-[800] text-[14px]">cm</div>
            </div>
        </div>
    </div>
</div>

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
