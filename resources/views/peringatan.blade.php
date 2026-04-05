@extends('layouts.app')
@section('title', 'Peringatan')

@section('content')
<div x-data="{ 
    unreadCount: 10,
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
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] rounded-tl-[24px] rounded-br-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white">Belum dibaca</p>
            <p class="text-[48px] font-[800] text-black dark:text-[#9292C5] text-right" x-text="unreadCount">10</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white">Total Bahaya</p>
            <p class="text-[48px] font-[800] text-black dark:text-[#e02424] text-right">2</p>
        </div>
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[140px] transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white">Total Siaga</p>
            <p class="text-[48px] font-[800] text-black dark:text-[#D8C726] text-right">4</p>
        </div>
    </div>

    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex-1 flex flex-col overflow-hidden min-h-[350px] transition-colors duration-300">
        <div class="flex gap-4 mb-[24px]">
            <!-- Dropdown Tipe Peringatan -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-[#9292C5] dark:bg-[rgba(255,255,255,0.05)] text-white dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-transparent dark:border-[rgba(255,255,255,0.05)] hover:bg-[#7b7bb2] hover:text-white transition-colors flex items-center justify-between gap-2 min-w-[140px]">
                    <span x-text="filterTipe"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="dropdownOpen" x-transition class="absolute left-0 mt-2 w-[160px] bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] rounded-[16px] shadow-lg py-2 z-50">
                    <template x-for="item in ['Semua Tipe', 'Bahaya', 'Siaga', 'Aman']">
                        <button @click="filterTipe = item; dropdownOpen = false" class="w-full text-left px-[20px] py-[8px] text-[13px] font-[600] text-[#555] dark:text-[#a5a5d1] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors" x-text="item"></button>
                    </template>
                </div>
            </div>

            <!-- Dropdown Status Baca -->
            <div x-data="{ dropdownOpen: false }" class="relative z-20">
                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="bg-[#9292C5] dark:bg-[rgba(255,255,255,0.05)] text-white dark:text-[#a5a5d1] font-[700] px-[20px] py-[6px] rounded-[10px] text-[13px] border border-transparent dark:border-[rgba(255,255,255,0.05)] hover:bg-[#7b7bb2] hover:text-white transition-colors flex items-center justify-between gap-2 min-w-[160px]">
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
                        <th class="py-[16px] px-[20px] w-[48%]">Peringatan</th>
                        <th class="py-[16px] px-[20px] text-center w-[15%]">Level Air</th>
                        <th class="py-[16px] px-[20px] text-center w-[15%]">Waktu</th>
                        <th class="py-[16px] px-[20px] text-center w-[10%]">Status</th>
                    </tr>
                </thead>
                
                <!-- Item 1 (Bahaya - Unread) -->
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('Bahaya', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-white dark:bg-transparent hover:bg-[#F9F9FB] dark:hover:bg-[rgba(255,255,255,0.02)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            <span class="px-2.5 py-1 bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.15)] text-[#c81e1e] dark:text-[#f8b4b4] rounded-full text-[11px] font-bold uppercase tracking-wider">Bahaya</span>
                        </td>
                        <td class="py-[16px] px-[20px] group">
                            <span :class="isRead ? '' : 'font-[800]'" class="truncate block w-full">Debit air EWS 2 meluap melebihi batas bahaya! Segera evakuasi.</span>
                            <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center font-bold text-[#c81e1e]">25cm</td>
                        <td class="py-[16px] px-[20px] text-center">13:10 WIB</td>
                        <td class="py-[16px] px-[20px] text-center">
                            <div :class="isRead ? 'bg-[#E5E5EF] dark:bg-[#2a2b36]' : 'bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.2)]'" class="inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-300" title="Status">
                                <div :class="isRead ? 'bg-[#C8C8E1] dark:bg-[#4b4b57] shadow-none' : 'bg-[#6BBF6B] shadow-[0_0_8px_#6bbf6b]'" class="w-3 h-3 rounded-full transition-colors duration-300"></div>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#c81e1e] ml-2 my-2">
                                <strong>Detail Sistem:</strong> Sensor Ultrasonik pada EWS 2 (Bendungan Katulampa) mendeteksi kenaikan level air dari 15cm ke 25cm dalam kurun waktu 15 menit. Pompa darurat otomatis GAGAL diaktifkan karena gangguan listrik.<br><br>
                                <strong>Rekomendasi Tindakan:</strong><br>
                                1. Aktifkan sirine alarm peringatan dini secara manual.<br>
                                2. Terjunkan tim teknisi BPBD ke lokasi pompa air hulu.
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- Item 2 (Bahaya - Unread) -->
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('Bahaya', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-white dark:bg-transparent hover:bg-[#F9F9FB] dark:hover:bg-[rgba(255,255,255,0.02)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            <span class="px-2.5 py-1 bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.15)] text-[#c81e1e] dark:text-[#f8b4b4] rounded-full text-[11px] font-bold uppercase tracking-wider">Bahaya</span>
                        </td>
                        <td class="py-[16px] px-[20px] group">
                            <span :class="isRead ? '' : 'font-[800]'" class="truncate block w-full">EWS 3 mendeteksi anomali aliran air bertekanan tinggi.</span>
                            <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center font-bold text-[#c81e1e]">22cm</td>
                        <td class="py-[16px] px-[20px] text-center">12:45 WIB</td>
                        <td class="py-[16px] px-[20px] text-center">
                            <div :class="isRead ? 'bg-[#E5E5EF] dark:bg-[#2a2b36]' : 'bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.2)]'" class="inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-300" title="Status">
                                <div :class="isRead ? 'bg-[#C8C8E1] dark:bg-[#4b4b57] shadow-none' : 'bg-[#6BBF6B] shadow-[0_0_8px_#6bbf6b]'" class="w-3 h-3 rounded-full transition-colors duration-300"></div>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#c81e1e] ml-2 my-2">
                                <strong>Detail Sistem:</strong> Lonjakan laju air datang secara tiba-tiba akibat curah hujan tinggi di wilayah dataran tinggi. EWS 3 otomatis mengirimkan notifikasi radio RF ke Command Center. Area bantaran sungai rawan terdampak.<br>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- Item 3 (Siaga - Unread) -->
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('Siaga', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            <span class="px-2.5 py-1 bg-[#fdf6b2] dark:bg-[rgba(216,199,38,0.15)] text-[#c27803] dark:text-[#D8C726] rounded-full text-[11px] font-bold uppercase tracking-wider">Siaga</span>
                        </td>
                        <td class="py-[16px] px-[20px]">
                            <span :class="isRead ? '' : 'font-[800]'" class="truncate block w-full">Ancaman banjir perlahan di area Soekarno-Hatta (EWS 1).</span>
                            <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center font-bold text-[#c27803] dark:text-[#D8C726]">14cm</td>
                        <td class="py-[16px] px-[20px] text-center">12:34 WIB</td>
                        <td class="py-[16px] px-[20px] text-center">
                            <div :class="isRead ? 'bg-[#E5E5EF] dark:bg-[#2a2b36]' : 'bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.2)]'" class="inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-300" title="Status">
                                <div :class="isRead ? 'bg-[#C8C8E1] dark:bg-[#4b4b57] shadow-none' : 'bg-[#6BBF6B] shadow-[0_0_8px_#6bbf6b]'" class="w-3 h-3 rounded-full transition-colors duration-300"></div>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#D8C726] ml-2 my-2">
                                Status sungai Brantas di titik EWS 1 mulai naik dari level aman (10cm) menuju ambang siaga pertama (14cm). Diperkirakan genangan akan mencapai daratan rendah dalam waktu 2 jam jika curah hujan tidak mereda.
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- Item 4 (Siaga - Unread) -->
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('Siaga', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            <span class="px-2.5 py-1 bg-[#fdf6b2] dark:bg-[rgba(216,199,38,0.15)] text-[#c27803] dark:text-[#D8C726] rounded-full text-[11px] font-bold uppercase tracking-wider">Siaga</span>
                        </td>
                        <td class="py-[16px] px-[20px]">
                            <span :class="isRead ? '' : 'font-[800]'" class="truncate block w-full">Debit air EWS 1 terpantau fluktuatif di ambang batas siaga.</span>
                            <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center font-bold text-[#c27803] dark:text-[#D8C726]">13.8cm</td>
                        <td class="py-[16px] px-[20px] text-center">12:20 WIB</td>
                        <td class="py-[16px] px-[20px] text-center">
                            <div :class="isRead ? 'bg-[#E5E5EF] dark:bg-[#2a2b36]' : 'bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.2)]'" class="inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-300" title="Status">
                                <div :class="isRead ? 'bg-[#C8C8E1] dark:bg-[#4b4b57] shadow-none' : 'bg-[#6BBF6B] shadow-[0_0_8px_#6bbf6b]'" class="w-3 h-3 rounded-full transition-colors duration-300"></div>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#D8C726] ml-2 my-2">
                                Peringatan otomatis diteruskan dari sensor EWS 1. Tidak ada anomali cuaca yang tercatat, mohon pantau pergerakan air 30 menit ke depan.
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- Item 5 (Siaga - Unread) -->
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('Siaga', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            <span class="px-2.5 py-1 bg-[#fdf6b2] dark:bg-[rgba(216,199,38,0.15)] text-[#c27803] dark:text-[#D8C726] rounded-full text-[11px] font-bold uppercase tracking-wider">Siaga</span>
                        </td>
                        <td class="py-[16px] px-[20px]">
                            <span :class="isRead ? '' : 'font-[800]'" class="truncate block w-full">Penumpukan volume air di pintu air sekunder (EWS 1).</span>
                            <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center font-bold text-[#c27803] dark:text-[#D8C726]">13.5cm</td>
                        <td class="py-[16px] px-[20px] text-center">11:15 WIB</td>
                        <td class="py-[16px] px-[20px] text-center">
                            <div :class="isRead ? 'bg-[#E5E5EF] dark:bg-[#2a2b36]' : 'bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.2)]'" class="inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-300" title="Status">
                                <div :class="isRead ? 'bg-[#C8C8E1] dark:bg-[#4b4b57] shadow-none' : 'bg-[#6BBF6B] shadow-[0_0_8px_#6bbf6b]'" class="w-3 h-3 rounded-full transition-colors duration-300"></div>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#D8C726] ml-2 my-2">
                                Pintu air sekunder mengalami kepadatan debit pasca hujan ringan dini hari. Disarankan membuka katup pembuangan ekstra.
                            </div>
                        </td>
                    </tr>
                </tbody>
                
                <!-- Items 6-10 (Siaga - Unread) -->
                @for ($i = 6; $i <= 10; $i++)
                <tbody x-data="{ open: false, isRead: false }" x-show="checkFilter('Siaga', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open; if(!isRead){ isRead = true; unreadCount-- }" class="cursor-pointer bg-[#F9F9FB] dark:bg-[rgba(255,255,255,0.015)] hover:bg-[#F3F3F3] dark:hover:bg-[rgba(255,255,255,0.03)] text-black dark:text-[#d1d1d8] font-[600] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            <span class="px-2.5 py-1 bg-[#fdf6b2] dark:bg-[rgba(216,199,38,0.15)] text-[#c27803] dark:text-[#D8C726] rounded-full text-[11px] font-bold uppercase tracking-wider">Siaga</span>
                        </td>
                        <td class="py-[16px] px-[20px]">
                            <span :class="isRead ? '' : 'font-[800]'" class="truncate block w-full">Peringatan ambang siaga terdeteksi di sensor EWS {{ rand(1,3) }}.</span>
                            <span class="text-[11px] font-normal text-[#9292C5] mt-1 block">Klik untuk melihat detail &rarr;</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center font-bold text-[#c27803] dark:text-[#D8C726]">{{ rand(11,13) }}.{{ rand(1,9) }}cm</td>
                        <td class="py-[16px] px-[20px] text-center">0{{ 11 - $i }}:{{ rand(10,59) }} WIB</td>
                        <td class="py-[16px] px-[20px] text-center">
                            <div :class="isRead ? 'bg-[#E5E5EF] dark:bg-[#2a2b36]' : 'bg-[#e2f1e2] dark:bg-[rgba(107,191,107,0.2)]'" class="inline-flex items-center justify-center w-6 h-6 rounded-full transition-colors duration-300" title="Status">
                                <div :class="isRead ? 'bg-[#C8C8E1] dark:bg-[#4b4b57] shadow-none' : 'bg-[#6BBF6B] shadow-[0_0_8px_#6bbf6b]'" class="w-3 h-3 rounded-full transition-colors duration-300"></div>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] leading-relaxed border-l-4 border-[#D8C726] ml-2 my-2">
                                Catatan sistem: Fluktuasi minor terdeteksi. Sistem memperbarui log peringatan sesuai protokol siaga harian EWS. Penanganan belum diperlukan secara mendesak.
                            </div>
                        </td>
                    </tr>
                </tbody>
                @endfor

                <!-- Item 11 (Siaga - Read) -->
                <tbody x-data="{ open: false, isRead: true }" x-show="checkFilter('Siaga', isRead)" x-transition.opacity class="border-b border-[#E5E5EF] dark:border-[#2a2b36]">
                    <tr @click="open = !open" class="cursor-pointer bg-white dark:bg-transparent hover:bg-[#F9F9FB] dark:hover:bg-[rgba(255,255,255,0.02)] text-[#555] dark:text-[#a5a5d1] font-[500] transition-colors duration-300">
                        <td class="py-[16px] px-[20px]">
                            <span class="px-2.5 py-1 bg-[#fdf6b2] dark:bg-[rgba(216,199,38,0.15)] text-[#c27803] dark:text-[#D8C726] rounded-full text-[11px] font-bold uppercase tracking-wider opacity-70">Siaga</span>
                        </td>
                        <td class="py-[16px] px-[20px]">
                            <span class="truncate block w-full">Level air stabil di atas normal (H-1).</span>
                        </td>
                        <td class="py-[16px] px-[20px] text-center">13.5cm</td>
                        <td class="py-[16px] px-[20px] text-center">19:20 WIB</td>
                        <td class="py-[16px] px-[20px] text-center">
                            <div class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#E5E5EF] dark:bg-[#2a2b36]" title="Sudah dibaca">
                                <div class="w-3 h-3 bg-[#C8C8E1] dark:bg-[#4b4b57] rounded-full"></div>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition class="bg-[#F3F3F3]/50 dark:bg-[rgba(0,0,0,0.15)]">
                        <td colspan="5" class="p-0">
                            <div class="px-[20px] py-[16px] text-[13px] text-[#555] dark:text-[#a5a5d1] border-l-4 border-[#C8C8E1] ml-2 my-2">
                                Kondisi terpantau aman walau sedikit melewati ambang wajar kemarin sore.
                            </div>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection
