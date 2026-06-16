@extends('layouts.app')
@section('title', 'Peringatan')

@section('content')
<div x-data="{ 
    unreadCount: {{ $totalBahaya + $totalSiaga }},
    filterTipe: 'Semua Tipe',
    filterStatusBaca: 'Semua Status',
    filterPerangkat: 'Semua Perangkat',
    checkFilter(tipe, deviceName, isItemRead) {
        let matchTipe = (this.filterTipe === 'Semua Tipe' || this.filterTipe === tipe);
        let matchPerangkat = (this.filterPerangkat === 'Semua Perangkat' || this.filterPerangkat === deviceName);
        let matchStatus = true;
        if (this.filterStatusBaca === 'Sudah dibaca') matchStatus = isItemRead;
        if (this.filterStatusBaca === 'Belum dibaca') matchStatus = !isItemRead;
        return matchTipe && matchPerangkat && matchStatus;
    }
}" class="flex flex-col h-full">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] mb-[24px]">
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] shadow-sm flex flex-col justify-between h-[120px] modern-card animate-fade-in-up stagger-1">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Total Peringatan</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#9292C5] text-right leading-none" x-text="unreadCount">{{ $totalBahaya + $totalSiaga }}</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] shadow-sm flex flex-col justify-between h-[120px] modern-card animate-fade-in-up stagger-2">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Total Bahaya</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#e02424] text-right leading-none">{{ $totalBahaya }}</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] shadow-sm flex flex-col justify-between h-[120px] modern-card animate-fade-in-up stagger-3">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Total Siaga</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#D8C726] text-right leading-none">{{ $totalSiaga }}</p>
        </div>
    </div>

    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] md:p-[24px] shadow-sm flex-1 flex flex-col overflow-hidden min-h-[350px] animate-fade-in-up stagger-4 modern-card">
        <div class="flex flex-wrap gap-4 mb-[24px]">
            <!-- Dropdown Perangkat -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" 
                        class="font-[700] px-[20px] py-[8px] rounded-[12px] text-[13px] border shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between gap-2 min-w-[160px]"
                        :class="filterPerangkat === 'Semua Perangkat' ? 'bg-white dark:bg-[#1a1b24] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] text-[#555] dark:text-[#a5a5d1] hover:bg-gray-50' : 'bg-[#9292C5] border-transparent text-white hover:bg-[#8585b8]'">
                    <span x-text="filterPerangkat"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute left-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    @php
                        $deviceNames = $devices->pluck('name')->toArray();
                        array_unshift($deviceNames, 'Semua Perangkat');
                    @endphp
                    <template x-for="item in {{ json_encode($deviceNames) }}">
                        <button @click="filterPerangkat = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>

            <!-- Dropdown Tipe Peringatan (outlined style) -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" 
                        class="font-[700] px-[20px] py-[8px] rounded-[12px] text-[13px] border shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between gap-2 min-w-[140px]"
                        :class="filterTipe === 'Semua Tipe' ? 'bg-white dark:bg-[#1a1b24] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] text-[#555] dark:text-[#a5a5d1] hover:bg-gray-50' : 'bg-[#9292C5] border-transparent text-white hover:bg-[#8585b8]'">
                    <span x-text="filterTipe"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute left-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Semua Tipe', 'Bahaya', 'Waspada', 'Aman']">
                        <button @click="filterTipe = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>

            <!-- Dropdown Status Baca (outlined style) -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" 
                        class="font-[700] px-[20px] py-[8px] rounded-[12px] text-[13px] border shadow-sm hover:shadow-md transition-all duration-300 flex items-center justify-between gap-2 min-w-[160px]"
                        :class="filterStatusBaca === 'Semua Status' ? 'bg-white dark:bg-[#1a1b24] border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] text-[#555] dark:text-[#a5a5d1] hover:bg-gray-50' : 'bg-[#9292C5] border-transparent text-white hover:bg-[#8585b8]'">
                    <span x-text="filterStatusBaca"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute left-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Semua Status', 'Belum dibaca', 'Sudah dibaca']">
                        <button @click="filterStatusBaca = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>
        </div>

        <div class="w-full relative overflow-y-auto pr-2 rounded-[16px] bg-[#F9F9FB] dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.02)] transition-colors duration-300">
            <table class="w-full text-left text-[13px]">
                <thead class="sticky top-0 bg-[#F9F9FB] dark:bg-[#1a1b24] border-b-[2px] border-[#E5E5EF] dark:border-[#2a2b36] z-10 text-[13px] font-[800] text-black dark:text-white">
                    <tr>
                        <th class="py-[16px] px-[20px] w-[12%]">Tipe</th>
                        <th class="py-[16px] px-[20px] w-[40%]">Peringatan</th>
                        <th class="py-[16px] px-[20px] text-center w-[15%]">Level Air</th>
                        <th class="py-[16px] px-[20px] text-center w-[18%]">Waktu</th>
                        <th class="py-[16px] px-[20px] text-center w-[10%]">Status</th>
                    </tr>
                </thead>
                
                @forelse ($data as $row)
                @php
                    $jarakVal = $row->distance_cm;
                    $statusType = ucfirst(strtolower($row->flood_status));
                    if (!in_array($statusType, ['Bahaya', 'Waspada', 'Aman'])) $statusType = 'Aman';
                    
                    // Timezone correction
                    $dateObj = \Carbon\Carbon::parse($row->created_at, 'UTC')->setTimezone('Asia/Jakarta');
                    $dateStr = $dateObj->format('H.i');
                    $statusLabel = $statusType === 'Waspada' ? 'Siaga' : $statusType;
                    
                    $badgeClass = '';
                    if ($statusType === 'Bahaya') $badgeClass = 'bg-[#fde8e8] text-[#e02424] dark:bg-[rgba(224,36,36,0.15)]';
                    elseif ($statusType === 'Waspada') $badgeClass = 'bg-[#fef3c7] text-[#d97706] dark:bg-[rgba(245,158,11,0.15)] dark:text-[#f59e0b]';
                    else $badgeClass = 'bg-[#e2f1e2] text-[#22c55e] dark:bg-[rgba(34,197,94,0.15)] dark:text-[#4ade80]';
                    
                    $deviceName = $row->device ? $row->device->name : 'EWS 1';
                    $msgText = "Status debit air di area sensor terpantau normal dan terkendali.";
                    if ($statusType === 'Bahaya') $msgText = "Ancaman Banjir di area Soekarno-Hatta! Air mencapai level kritis, harap waspada.";
                    elseif ($statusType === 'Waspada') $msgText = "Peringatan dini! Debit air di area Soekarno-Hatta mulai meningkat perlahan.";
                    else $msgText = "Status debit air di area sensor terpantau normal dan terkendali.";
                @endphp
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('{{ $statusType }}', '{{ $deviceName }}', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = true; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-transparent hover:bg-[#9292C5]/40 dark:hover:bg-[#9292C5]/40 text-black dark:text-[#d1d1d8] font-[500] transition-colors duration-300">
                        <td class="py-[14px] px-[20px]">
                            <span class="text-[12px] font-[700] px-[12px] py-[4px] rounded-full {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="py-[14px] px-[20px]">
                            <span :class="isRead ? '' : 'font-[700]'" class="truncate block w-full text-[13px]">{{ $msgText }}</span>
                        </td>
                        <td class="py-[14px] px-[20px] text-center font-bold text-[13px]">{{ $jarakVal }}cm</td>
                        <td class="py-[14px] px-[20px] text-center text-[13px]">{{ $dateStr }}</td>
                        <td class="py-[14px] px-[20px] text-center">
                            <div class="inline-flex items-center justify-center">
                                <div :class="isRead ? 'bg-[#C8C8E1] dark:bg-[#4b4b57]' : 'bg-[#6BBF6B] shadow-[0_0_6px_#6bbf6b]'" class="w-3 h-3 rounded-full transition-colors duration-300"></div>
                            </div>
                        </td>
                    </tr>
                    
                    <template x-teleport="body">
                        <div x-show="open" 
                             x-transition.opacity.duration.300ms
                             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
                             style="display: none;">
                            
                            <!-- Modal backdrop -->
                            <div class="absolute inset-0" @click="open = false"></div>
                            
                            <!-- Modal panel -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="relative bg-white dark:bg-[#20212a] w-full max-w-lg rounded-[24px] p-[32px] shadow-2xl border border-transparent dark:border-[rgba(255,255,255,0.05)] z-10 flex flex-col">
                                
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-[20px] font-bold text-black dark:text-white tracking-tight">Detail Peringatan</h3>
                                    <button @click="open = false" class="text-[#555] dark:text-[#a5a5d1] hover:text-[#e02424] transition-colors bg-transparent border-none p-1 cursor-pointer rounded-full hover:bg-gray-100 dark:hover:bg-[rgba(255,255,255,0.05)]">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                                
                                <!-- Premium Detailed Content Layout -->
                                <div class="flex flex-col gap-5 mt-4">
                                    <!-- Top Status Banner -->
                                    <div class="flex items-center gap-4 p-4 rounded-2xl @if($statusType === 'Bahaya') bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.1)] @elseif($statusType === 'Waspada') bg-[#fef3c7] dark:bg-[rgba(245,158,11,0.1)] @else bg-[#e2f1e2] dark:bg-[rgba(34,197,94,0.1)] @endif border @if($statusType === 'Bahaya') border-[#f8b4b4] dark:border-[rgba(224,36,36,0.2)] @elseif($statusType === 'Waspada') border-[#fde68a] dark:border-[rgba(245,158,11,0.2)] @else border-[#bbf7d0] dark:border-[rgba(34,197,94,0.2)] @endif">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 @if($statusType === 'Bahaya') bg-[#e02424] text-white @elseif($statusType === 'Waspada') bg-[#f59e0b] text-white @else bg-[#22c55e] text-white @endif shadow-lg">
                                            @if($statusType === 'Bahaya')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            @elseif($statusType === 'Waspada')
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-[800] text-[16px] @if($statusType === 'Bahaya') text-[#c81e1e] dark:text-[#f8b4b4] @elseif($statusType === 'Waspada') text-[#b45309] dark:text-[#fde68a] @else text-[#15803d] dark:text-[#bbf7d0] @endif leading-tight mb-1">Status {{ $statusLabel }}</h4>
                                            <p class="text-[13px] font-[500] @if($statusType === 'Bahaya') text-[#e02424] dark:text-[#fca5a5] @elseif($statusType === 'Waspada') text-[#d97706] dark:text-[#fcd34d] @else text-[#16a34a] dark:text-[#86efac] @endif leading-snug">
                                                Air terdeteksi di level <span class="font-bold">{{ $jarakVal }} cm</span>.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Grid Details -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-[#F9F9FB] dark:bg-[#20212a] p-3 rounded-xl border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)]">
                                            <p class="text-[11px] font-bold text-[#9292C5] dark:text-[#a5a5d1] uppercase tracking-wider mb-1">Perangkat</p>
                                            <p class="text-[15px] font-[800] text-black dark:text-white">{{ $deviceName }}</p>
                                        </div>
                                        <div class="bg-[#F9F9FB] dark:bg-[#20212a] p-3 rounded-xl border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)]">
                                            <p class="text-[11px] font-bold text-[#9292C5] dark:text-[#a5a5d1] uppercase tracking-wider mb-1">Waktu</p>
                                            <p class="text-[14px] font-[700] text-black dark:text-white">{{ $dateStr }}</p>
                                        </div>
                                        <div class="bg-[#F9F9FB] dark:bg-[#20212a] p-3 rounded-xl border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)]">
                                            <p class="text-[11px] font-bold text-[#9292C5] dark:text-[#a5a5d1] uppercase tracking-wider mb-1">Perangkat</p>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <svg class="w-3.5 h-3.5 text-[#9292C5]" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16H3v-2h18v2zm0-5H3V9h18v2zm0-5H3V4h18v2z"/></svg>
                                                <p class="text-[13px] font-[700] text-black dark:text-white">EWS {{ $row->device->id ?? 1 }}</p>
                                            </div>
                                        </div>
                                        <div class="bg-[#F9F9FB] dark:bg-[#20212a] p-3 rounded-xl border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)]">
                                            <p class="text-[11px] font-bold text-[#9292C5] dark:text-[#a5a5d1] uppercase tracking-wider mb-1">Lokasi</p>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <svg class="w-3.5 h-3.5 text-[#9292C5]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                                <p class="text-[13px] font-[700] text-black dark:text-white truncate" title="{{ $row->device->lokasi ?? 'Soekarno-Hatta' }}">{{ $row->device->lokasi ?? 'Soekarno-Hatta' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Detailed Description -->
                                    <div>
                                        <p class="text-[12px] font-[800] text-[#555] dark:text-[#a5a5d1] mb-2 uppercase tracking-wide">Keterangan Detail:</p>
                                        <div class="bg-[#F3F3F3] dark:bg-[#1a1b24] p-4 rounded-[12px] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] relative group overflow-hidden shadow-sm">
                                            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-[rgba(255,255,255,0.4)] dark:via-[rgba(255,255,255,0.05)] to-transparent -translate-x-[100%] group-hover:translate-x-[100%] transition-transform duration-1000 z-0"></div>
                                            <p class="text-[13px] text-[#555] dark:text-[#a5a5d1] font-[500] leading-relaxed relative z-10">Berdasarkan pembacaan sensor pada saat itu, tingkat kedalaman air tercatat pada angka <strong class="text-black dark:text-white">{{ $jarakVal }} cm</strong> dengan nilai intensitas hujan sebesar <strong class="text-black dark:text-white">{{ $row->rain_intensity_raw ?? 0 }}</strong>. Oleh karena itu, sistem menetapkan status lokasi ini sebagai <strong class="text-black dark:text-white">{{ $statusType }}</strong>.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-8 flex justify-end">
                                    <button @click="open = false" class="bg-[#9292C5] text-white px-[20px] py-[8px] rounded-[12px] font-bold hover:bg-[#8585b8] transition-all shadow-sm">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </tbody>
                @empty
                <tbody>
                    <tr><td colspan="5" class="text-center py-4 font-bold">Belum ada peringatan kritis tercatat di database.</td></tr>
                </tbody>
                @endforelse

            </table>
        </div>
    </div>
</div>
@endsection
