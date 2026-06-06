@extends('layouts.app')
@section('title', 'Riwayat Data')

@section('content')
<div x-data="{
    filterStatus: 'Semua Status',
    filterPerangkat: 'Semua Perangkat',
    filterSort: 'Terbaru',
    filterTanggal: '',
    checkFilter(status, ewsId, dateYmd) {
        let matchStatus = (this.filterStatus === 'Semua Status' || this.filterStatus === status);
        let matchPerangkat = (this.filterPerangkat === 'Semua Perangkat' || this.filterPerangkat === 'EWS ' + ewsId);
        let matchDate = (this.filterTanggal === '' || this.filterTanggal === dateYmd);
        return matchStatus && matchPerangkat && matchDate;
    },
    sortData() {
        const table = this.$refs.dataTable;
        if (!table) return;
        const tbodys = Array.from(table.querySelectorAll('tbody.data-row'));
        tbodys.sort((a, b) => {
            const timeA = parseInt(a.dataset.timestamp);
            const timeB = parseInt(b.dataset.timestamp);
            return this.filterSort === 'Terbaru' ? timeB - timeA : timeA - timeB;
        });
        tbodys.forEach(tbody => table.appendChild(tbody));
    }
}" x-init="$nextTick(() => sortData())" class="flex flex-col h-full">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] mb-[24px]">
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Total Data</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#9292C5] text-right leading-none">{{ count($data) }}</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Rata Rata Level</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#6BBF6B] text-right leading-none">{{ $avgLevel }}<span class="text-[20px]">cm</span></p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Level Tertinggi</p>
            <p class="text-[56px] font-[900] text-black dark:text-[#e02424] text-right leading-none">{{ $highestLevel }}<span class="text-[20px]">cm</span></p>
        </div>
    </div>

    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex-1 flex flex-col overflow-hidden min-h-[350px] transition-colors duration-300">
        <div class="flex flex-wrap gap-4 mb-[24px] justify-between">
        <div class="flex items-center gap-2 relative">
            <!-- Clear date button (appears if date is selected) -->
            <button x-show="filterTanggal !== ''" @click="filterTanggal = ''" class="absolute right-[46px] text-[#555] hover:text-[#e02424] opacity-80 z-10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="flex items-center bg-white dark:bg-[rgba(255,255,255,0.05)] border border-[#d0d0e0] dark:border-[rgba(255,255,255,0.1)] rounded-[10px] overflow-hidden">
                <span class="text-[13px] font-[700] text-[#555] dark:text-[#a5a5d1] pl-[14px] pr-[4px] whitespace-nowrap">Tanggal</span>
                <input type="date" x-model="filterTanggal" class="bg-transparent text-[#555] dark:text-[#a5a5d1] font-[600] pl-[6px] pr-[14px] py-[6px] text-[13px] border-none focus:outline-none cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-50">
            </div>
        </div>
        
        <div class="flex flex-wrap gap-4">
            <!-- Dropdown Perangkat (outlined) -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-white dark:bg-[rgba(255,255,255,0.05)] text-[#555] dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-[#d0d0e0] dark:border-[rgba(255,255,255,0.1)] hover:bg-[#f0f0f5] dark:hover:bg-[rgba(255,255,255,0.08)] transition-colors flex items-center justify-between gap-2 min-w-[160px]">
                    <span x-text="filterPerangkat"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute left-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Semua Perangkat', 'EWS 1', 'EWS 2', 'EWS 3']">
                        <button @click="filterPerangkat = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>

            <!-- Dropdown Kategori Status (outlined) -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-white dark:bg-[rgba(255,255,255,0.05)] text-[#555] dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-[#d0d0e0] dark:border-[rgba(255,255,255,0.1)] hover:bg-[#f0f0f5] dark:hover:bg-[rgba(255,255,255,0.08)] transition-colors flex items-center justify-between gap-2 min-w-[140px]">
                    <span x-text="filterStatus"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute right-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Semua Status', 'Bahaya', 'Waspada', 'Aman']">
                        <button @click="filterStatus = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>
            
            <!-- Dropdown Tipe Sort (outlined) -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-white dark:bg-[rgba(255,255,255,0.05)] text-[#555] dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-[#d0d0e0] dark:border-[rgba(255,255,255,0.1)] hover:bg-[#f0f0f5] dark:hover:bg-[rgba(255,255,255,0.08)] transition-colors flex items-center justify-between gap-2 min-w-[120px]">
                    <span x-text="filterSort"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute right-0 mt-2 w-[120px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Terbaru', 'Terlama']">
                        <button @click="filterSort = item; dropdownOpen = false; sortData();" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full relative overflow-y-auto pr-2 rounded-[16px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] transition-colors duration-300">
        <table x-ref="dataTable" class="w-full text-left text-[13px]">
            <thead class="sticky top-0 bg-white dark:bg-[#1a1b24] border-b-[2px] border-[#E5E5EF] dark:border-[#2a2b36] z-10 text-[13px] font-[800] text-black dark:text-white transition-colors duration-300">
                <tr>
                    <th class="py-[16px] px-[20px] w-[12%]">Status</th>
                    <th class="py-[16px] px-[20px] w-[25%]">Perangkat & Info</th>
                    <th class="py-[16px] px-[20px] text-center w-[15%]">Jarak (CM)</th>
                    <th class="py-[16px] px-[20px] text-center w-[15%]">Level Air</th>
                    <th class="py-[16px] px-[20px] text-center w-[20%]">Waktu</th>
                </tr>
            </thead>
            
            @forelse ($data as $row)
                @php
                    $jarakVal = $row->distance_cm;
                    $statusType = ucfirst(strtolower($row->flood_status));
                    if (!in_array($statusType, ['Bahaya', 'Waspada', 'Aman'])) $statusType = 'Aman';
                    $statusLabel = $statusType === 'Waspada' ? 'Siaga' : $statusType;
                    
                    // Timezone correction
                    $dateObj = \Carbon\Carbon::parse($row->created_at)->setTimezone('Asia/Jakarta');
                    $bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    $dateStr = $dateObj->format('d') . ' ' . $bulanIndo[$dateObj->format('n') - 1] . ' ' . $dateObj->format('Y');
                    $dateYmd = $dateObj->format('Y-m-d');
                    $timestamp = $dateObj->timestamp;
                    $ewsId = 1; // Default
                @endphp
                <tbody class="data-row" data-timestamp="{{ $timestamp }}" data-date-ymd="{{ $dateYmd }}" x-data="{ open: false }" x-show="checkFilter('{{ $statusType }}', {{ $ewsId }}, '{{ $dateYmd }}')" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open" class="cursor-pointer bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)] text-black dark:text-[#d1d1d8] font-[500] transition-colors duration-300">
                        <td class="py-[14px] px-[20px]">
                            <span class="text-[13px] font-[600]">{{ $statusLabel }}</span>
                        </td>
                        <td class="py-[14px] px-[20px]">
                            <span class="font-[600] text-[13px]">EWS {{ $ewsId }}</span>
                        </td>
                        <td class="py-[14px] px-[20px] text-center font-bold text-[13px]">{{ $jarakVal }}cm</td>
                        <td class="py-[14px] px-[20px] text-center font-bold text-[13px]">{{ $jarakVal }}cm</td>
                        <td class="py-[14px] px-[20px] text-center text-[13px]">{{ $dateStr }}</td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 @if($statusType === 'Bahaya') border-[#c81e1e] @elseif($statusType === 'Waspada') border-[#D8C726] @else border-[#6BBF6B] @endif ml-2 my-2">
                                <strong>Detail Riwayat (DB ID: {{ $row->id }}):</strong> Jarak terukur menunjukkan air berada di level <strong>{{ $jarakVal }} cm</strong>. Data dikirim oleh node MQTT dengan pesan asli: <br><code class="mt-1 block bg-[#e8e8ed] dark:bg-[rgba(255,255,255,0.05)] px-2 py-1 rounded">Jarak: {{ $row->distance_cm }} cm, Hujan: {{ $row->rain_intensity_raw }}, Kondisi: {{ $row->flood_condition }}</code>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr><td colspan="5" class="text-center py-4 font-bold">Tidak ada riwayat sensor di database.</td></tr>
                </tbody>
            @endforelse
            
        </table>
    </div>
    </div>
</div>
@endsection
