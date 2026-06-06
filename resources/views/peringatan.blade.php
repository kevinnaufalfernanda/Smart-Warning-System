@extends('layouts.app')
@section('title', 'Peringatan')

@section('content')
<div x-data="{ 
    unreadCount: {{ count($data) }},
    filterTipe: 'Semua Tipe',
    filterStatusBaca: 'Semua Status',
    checkFilter(tipe, isItemRead) {
        let matchTipe = (this.filterTipe === 'Semua Tipe' || this.filterTipe === tipe);
        let matchStatus = true;
        if (this.filterStatusBaca === 'Sudah dibaca') matchStatus = isItemRead;
        if (this.filterStatusBaca === 'Belum dibaca') matchStatus = !isItemRead;
        return matchTipe && matchStatus;
    }
}" class="flex flex-col h-full">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] mb-[24px]">
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Belum dibaca</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#9292C5] text-right leading-none" x-text="unreadCount">{{ count($data) }}</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Total Bahaya</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#e02424] text-right leading-none">{{ $totalBahaya }}</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Total Siaga</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#D8C726] text-right leading-none">{{ $totalSiaga }}</p>
        </div>
    </div>

    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex-1 flex flex-col overflow-hidden min-h-[350px] transition-colors duration-300">
        <div class="flex gap-4 mb-[24px]">
            <!-- Dropdown Tipe Peringatan (outlined style) -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-white dark:bg-[rgba(255,255,255,0.05)] text-[#555] dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-[#d0d0e0] dark:border-[rgba(255,255,255,0.1)] hover:bg-[#f0f0f5] dark:hover:bg-[rgba(255,255,255,0.08)] transition-colors flex items-center justify-between gap-2 min-w-[140px]">
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
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-white dark:bg-[rgba(255,255,255,0.05)] text-[#555] dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-[#d0d0e0] dark:border-[rgba(255,255,255,0.1)] hover:bg-[#f0f0f5] dark:hover:bg-[rgba(255,255,255,0.08)] transition-colors flex items-center justify-between gap-2 min-w-[160px]">
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

        <div class="w-full relative overflow-y-auto pr-2 rounded-[16px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.02)] transition-colors duration-300">
            <table class="w-full text-left text-[13px]">
                <thead class="sticky top-0 bg-white dark:bg-[#1a1b24] border-b-[2px] border-[#E5E5EF] dark:border-[#2a2b36] z-10 text-[13px] font-[800] text-black dark:text-white transition-colors duration-300">
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
                    $dateObj = \Carbon\Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta');
                    $dateStr = $dateObj->format('H.i');
                    $statusLabel = $statusType === 'Waspada' ? 'Siaga' : $statusType;
                    
                    $msgText = "Ancaman Banjir di area Soekar...";
                    if ($statusType === 'Bahaya') $msgText = "Ancaman Banjir di area Soekar...";
                    elseif ($statusType === 'Waspada') $msgText = "Ancaman Banjir di area Soekar...";
                    else $msgText = "Status normal di area sensor";
                @endphp
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('{{ $statusType }}', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-white dark:bg-transparent hover:bg-[#F9F9FB] dark:hover:bg-[rgba(255,255,255,0.02)] text-black dark:text-[#d1d1d8] font-[500] transition-colors duration-300">
                        <td class="py-[14px] px-[20px]">
                            <span class="text-[13px] font-[600]">{{ $statusLabel }}</span>
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
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 @if($statusType === 'Bahaya') border-[#c81e1e] @elseif($statusType === 'Waspada') border-[#D8C726] @else border-[#6BBF6B] @endif ml-2 my-2">
                                <strong>Detail Sistem (DB ID: {{ $row->id }}):</strong> Jarak terukur menembus batas {{ $statusType }} dengan nilai <strong>{{ $jarakVal }} cm</strong>. Data rekaman raw: <br><code class="mt-1 block bg-[#e8e8ed] dark:bg-[rgba(255,255,255,0.05)] px-2 py-1 rounded">Jarak: {{ $row->distance_cm }} cm, Hujan: {{ $row->rain_intensity_raw }}, Kondisi: {{ $row->flood_condition }}</code>
                            </div>
                        </td>
                    </tr>
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
