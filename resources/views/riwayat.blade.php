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
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white">Total Data</p>
            <p class="text-[48px] font-[800] text-black dark:text-[#9292C5] text-right">241</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white">Rata Rata Level</p>
            <p class="text-[48px] font-[800] text-black dark:text-[#6BBF6B] text-right">12<span class="text-[20px]">cm</span></p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white">Level Tertinggi</p>
            <p class="text-[48px] font-[800] text-black dark:text-[#e02424] text-right">19<span class="text-[20px]">cm</span></p>
        </div>
    </div>

    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex-1 flex flex-col overflow-hidden min-h-[350px] transition-colors duration-300">
        <div class="flex flex-wrap gap-4 mb-[24px] justify-between">
        <div class="flex items-center gap-2 relative">
            <!-- Clear date button (appears if date is selected) -->
            <button x-show="filterTanggal !== ''" @click="filterTanggal = ''" class="absolute right-[46px] text-white hover:text-[#e02424] opacity-80 z-10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <input type="date" x-model="filterTanggal" class="bg-[#9292C5] dark:bg-[rgba(255,255,255,0.05)] text-white dark:text-[#a5a5d1] font-[700] pl-[14px] pr-[14px] py-[6px] rounded-[10px] text-[13px] border border-transparent dark:border-[rgba(255,255,255,0.05)] focus:outline-none focus:ring-2 focus:ring-white transition-all cursor-pointer relative z-0 [&::-webkit-calendar-picker-indicator]:invert-[1]">
        </div>
        
        <div class="flex flex-wrap gap-4">
            <!-- Dropdown Perangkat -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-[#9292C5] dark:bg-[rgba(255,255,255,0.05)] text-white dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-transparent dark:border-[rgba(255,255,255,0.05)] hover:bg-[#7b7bb2] hover:text-white transition-colors flex items-center justify-between gap-2 min-w-[160px]">
                    <span x-text="filterPerangkat"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute left-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Semua Perangkat', 'EWS 1', 'EWS 2', 'EWS 3']">
                        <button @click="filterPerangkat = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>

            <!-- Dropdown Kategori Status -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-[#9292C5] dark:bg-[rgba(255,255,255,0.05)] text-white dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-transparent dark:border-[rgba(255,255,255,0.05)] hover:bg-[#7b7bb2] hover:text-white transition-colors flex items-center justify-between gap-2 min-w-[160px]">
                    <span x-text="filterStatus"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute right-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Semua Status', 'Bahaya', 'Siaga', 'Aman']">
                        <button @click="filterStatus = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>
            
            <!-- Dropdown Tipe Sort -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-[#9292C5] dark:bg-[rgba(255,255,255,0.05)] text-white dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-transparent dark:border-[rgba(255,255,255,0.05)] hover:bg-[#7b7bb2] hover:text-white transition-colors flex items-center justify-between gap-2 min-w-[120px]">
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
                    <th class="py-[16px] px-[20px] w-[48%]">Perangkat & Info</th>
                    <th class="py-[16px] px-[20px] text-center w-[15%]">Jarak (CM)</th>
                    <th class="py-[16px] px-[20px] text-center w-[15%]">Level Air</th>
                    <th class="py-[16px] px-[20px] text-center w-[10%]">Waktu</th>
                </tr>
            </thead>
            
            <!-- Item 1 (Bahaya) -->
            <tbody class="data-row" data-timestamp="1773324600" data-date-ymd="2026-03-12" x-data="{ open: false }" x-show="checkFilter('Bahaya', 2, '2026-03-12')" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                <tr @click="open = !open" class="cursor-pointer bg-white dark:bg-transparent hover:bg-[#F9F9FB] dark:hover:bg-[rgba(255,255,255,0.02)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                    <td class="py-[16px] px-[20px]">
                        <span class="px-2.5 py-1 bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.15)] text-[#c81e1e] dark:text-[#f8b4b4] rounded-full text-[11px] font-bold uppercase tracking-wider">Bahaya</span>
                    </td>
                    <td class="py-[16px] px-[20px] group">
                        <span class="truncate block w-full">Riwayat EWS 2 mencapai batas bahaya.</span>
                        <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                    </td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-black dark:text-white">10cm</td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-[#c81e1e]">19cm</td>
                    <td class="py-[16px] px-[20px] text-center">12 Mar 2026, 13:10 WIB</td>
                </tr>
                <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                    <td colspan="5" class="p-0">
                        <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#c81e1e] ml-2 my-2">
                            <strong>Detail Riwayat:</strong> Tercatat kenaikan debit air mencapai tinggi maksimum secara eksponensial dalam 15 menit terakhir. Sistem otomatis mengirimkan status alarm.
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- Item 2 (Siaga) -->
            <tbody class="data-row" data-timestamp="1773324000" data-date-ymd="2026-03-12" x-data="{ open: false }" x-show="checkFilter('Siaga', 1, '2026-03-12')" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                <tr @click="open = !open" class="cursor-pointer bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                    <td class="py-[16px] px-[20px]">
                        <span class="px-2.5 py-1 bg-[#fdf6b2] dark:bg-[rgba(216,199,38,0.15)] text-[#c27803] dark:text-[#D8C726] rounded-full text-[11px] font-bold uppercase tracking-wider">Siaga</span>
                    </td>
                    <td class="py-[16px] px-[20px] group">
                        <span class="truncate block w-full">Riwayat EWS 1 di area Soekarno-Hatta.</span>
                        <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                    </td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-black dark:text-white">10cm</td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-[#c27803] dark:text-[#D8C726]">14cm</td>
                    <td class="py-[16px] px-[20px] text-center">12 Mar 2026, 13:00 WIB</td>
                </tr>
                <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                    <td colspan="5" class="p-0">
                        <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#D8C726] ml-2 my-2">
                            <strong>Detail Riwayat:</strong> Status sungai stabil pada batas siaga pertama. Intensitas hujan menyebabkan sedikit fluktuasi debit air.
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- Item 3 (Aman) -->
            <tbody class="data-row" data-timestamp="1773323400" data-date-ymd="2026-03-12" x-data="{ open: false }" x-show="checkFilter('Aman', 3, '2026-03-12')" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                <tr @click="open = !open" class="cursor-pointer bg-white dark:bg-transparent hover:bg-[#F9F9FB] dark:hover:bg-[rgba(255,255,255,0.02)] text-[#555] dark:text-[#a5a5d1] font-[500] transition-colors duration-300">
                    <td class="py-[16px] px-[20px]">
                        <span class="px-2.5 py-1 bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.15)] text-[#15803d] dark:text-[#6BBF6B] rounded-full text-[11px] font-bold uppercase tracking-wider">Aman</span>
                    </td>
                    <td class="py-[16px] px-[20px] group">
                        <span class="truncate block w-full">Riwayat EWS 3 terpantau stabil.</span>
                        <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                    </td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-black dark:text-white">10cm</td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-[#15803d] dark:text-[#6BBF6B]">5cm</td>
                    <td class="py-[16px] px-[20px] text-center">12 Mar 2026, 12:50 WIB</td>
                </tr>
                <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                    <td colspan="5" class="p-0">
                        <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#6BBF6B] ml-2 my-2">
                            <strong>Detail Riwayat:</strong> Cek rutin alat tercatat. Aliran air di sensor EWS 3 berada di level aman tanpa ada potensi meluap.
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- Item 4 (Aman) -->
            <tbody class="data-row" data-timestamp="1773322800" data-date-ymd="2026-03-12" x-data="{ open: false }" x-show="checkFilter('Aman', 1, '2026-03-12')" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                <tr @click="open = !open" class="cursor-pointer bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)] text-[#555] dark:text-[#a5a5d1] font-[500] transition-colors duration-300">
                    <td class="py-[16px] px-[20px]">
                        <span class="px-2.5 py-1 bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.15)] text-[#15803d] dark:text-[#6BBF6B] rounded-full text-[11px] font-bold uppercase tracking-wider">Aman</span>
                    </td>
                    <td class="py-[16px] px-[20px] group">
                        <span class="truncate block w-full">Riwayat EWS 1 normal, log siang.</span>
                        <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                    </td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-black dark:text-white">10cm</td>
                    <td class="py-[16px] px-[20px] text-center font-bold text-[#15803d] dark:text-[#6BBF6B]">7.5cm</td>
                    <td class="py-[16px] px-[20px] text-center">12 Mar 2026, 12:40 WIB</td>
                </tr>
                <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                    <td colspan="5" class="p-0">
                        <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#6BBF6B] ml-2 my-2">
                            <strong>Detail Riwayat:</strong> Posisi aman tercatat pada log reguler setelah hujan ringan sore hari.
                        </div>
                    </td>
                </tr>
            </tbody>

            <!-- Sisa 237 Data Generated secara dinamis menggunakan Blade Loop -->
            @for ($i = 5; $i <= 241; $i++)
                @php
                    $isSiaga = rand(1, 100) > 90; // 10% peluang menjadi status Siaga
                    $statusType = $isSiaga ? 'Siaga' : 'Aman';
                    $ewsId = rand(1, 3);
                    $levelAirInt = $isSiaga ? rand(13, 14) : rand(4, 11);
                    $levelAirDecimal = rand(0, 9);
                    $levelAir = $levelAirInt . '.' . $levelAirDecimal;
                    
                    // Interval pasti: setiap rekam history turun 10 menit (600 detik)
                    $baseTimestamp = 1773324600; // 12 Mar 2026, 13:10 WIB
                    $timestamp = $baseTimestamp - (($i - 1) * 600); 
                    $dateStr = date('d M Y, H:i', $timestamp) . " WIB";
                    $dateYmd = date('Y-m-d', $timestamp);
                @endphp
                
                <tbody class="data-row" data-timestamp="{{ $timestamp }}" data-date-ymd="{{ $dateYmd }}" x-data="{ open: false }" x-show="checkFilter('{{ $statusType }}', {{ $ewsId }}, '{{ $dateYmd }}')" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open" class="cursor-pointer {{ $i % 2 == 0 ? 'bg-white dark:bg-transparent hover:bg-[#F9F9FB] dark:hover:bg-[rgba(255,255,255,0.02)]' : 'bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)]' }} text-black dark:text-[#d1d1d8] font-[500] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            @if($statusType === 'Siaga')
                                <span class="px-2.5 py-1 bg-[#fdf6b2] dark:bg-[rgba(216,199,38,0.15)] text-[#c27803] dark:text-[#D8C726] rounded-full text-[11px] font-bold uppercase tracking-wider">Siaga</span>
                            @else
                                <span class="px-2.5 py-1 bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.15)] text-[#15803d] dark:text-[#6BBF6B] rounded-full text-[11px] font-bold uppercase tracking-wider">Aman</span>
                            @endif
                        </td>
                        <td class="py-[16px] px-[20px] group">
                            <span class="truncate block w-full">Riwayat EWS {{ $ewsId }} terpantau {{ $statusType === 'Siaga' ? 'fluktuatif' : 'stabil normatif' }}.</span>
                            <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center font-bold text-black dark:text-white">10cm</td>
                        <td class="py-[16px] px-[20px] text-center font-bold {{ $statusType === 'Siaga' ? 'text-[#c27803] dark:text-[#D8C726]' : 'text-[#15803d] dark:text-[#6BBF6B]' }}">{{ $levelAir }}cm</td>
                        <td class="py-[16px] px-[20px] text-center">{{ $dateStr }}</td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 {{ $statusType === 'Siaga' ? 'border-[#D8C726]' : 'border-[#6BBF6B]' }} ml-2 my-2">
                                <strong>Detail Riwayat:</strong> Log historis ke-{{ $i }} dari perangkat EWS {{ $ewsId }}. Status sistem tercatat {{ $statusType }} berdasarkan pengamatan jarak sensor permukaan air rutin.
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endfor
            
        </table>
    </div>
    </div>
</div>
@endsection
