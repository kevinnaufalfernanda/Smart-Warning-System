@extends('layouts.app')
@section('title', 'Perangkat')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
    <!-- Online Card -->
    @php
        $onlineCount = 0;
        foreach($devices as $d) {
            $lastLog = \App\Models\SensorLog::where('device_id', $d->id)->latest('created_at')->first();
            if ($lastLog && \Carbon\Carbon::parse($lastLog->created_at)->diffInMinutes(now()) <= 15) {
                $onlineCount++;
            }
        }
    @endphp
    <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] shadow-sm flex flex-col justify-between h-[140px] modern-card animate-fade-in-up stagger-1">
        <p class="text-[18px] font-bold tracking-tight text-black dark:text-white">Perangkat Online</p>
        <p class="text-[56px] font-[900] text-black dark:text-[#9292C5] text-right leading-none mt-2">{{ $onlineCount }}/{{ $devices->count() }}</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-[#e2f1e2] dark:bg-[#344034] text-[#6BBF6B] px-4 py-3 rounded-[12px] font-bold text-[14px]">
    {{ session('success') }}
</div>
@endif

<div class="flex justify-between items-center mt-[24px]">
    <h3 class="text-[22px] font-bold tracking-tight text-black dark:text-white">Daftar Perangkat</h3>
    <button onclick="document.getElementById('addDeviceModal').style.display='flex'" class="bg-[#9292C5] text-white px-[20px] py-[8px] rounded-[10px] text-[13px] font-bold flex items-center gap-2 hover:bg-[#8585b8] transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Perangkat
    </button>
</div>

<!-- Modal Tambah Perangkat -->
<div id="addDeviceModal" style="display:none;" class="fixed inset-0 bg-black/50 z-[100] items-center justify-center">
    <div class="bg-white dark:bg-[#20212a] rounded-[24px] p-6 w-[400px] shadow-lg border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)]">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-[18px] font-bold text-black dark:text-white">Tambah Perangkat IoT</h3>
            <button onclick="document.getElementById('addDeviceModal').style.display='none'" class="text-[#9292C5] hover:text-black dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('perangkat.store') }}" method="POST" class="flex flex-col gap-4">
            @csrf
            <div>
                <label class="block text-[13px] font-bold text-black dark:text-[#a5a5d1] mb-1">Nama EWS</label>
                <input type="text" name="name" required placeholder="Contoh: EWS 1" class="w-full bg-[#F3F3F3] dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[8px] px-3 py-2 text-[14px] font-medium dark:text-white focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
            </div>
            <div>
                <label class="block text-[13px] font-bold text-black dark:text-[#a5a5d1] mb-1">Lokasi</label>
                <input type="text" name="location" required placeholder="Contoh: Sungai Brantas" class="w-full bg-[#F3F3F3] dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[8px] px-3 py-2 text-[14px] font-medium dark:text-white focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
            </div>
            <div>
                <label class="block text-[13px] font-bold text-black dark:text-[#a5a5d1] mb-1">MAC Address ESP32/NodeMCU</label>
                <input type="text" name="mac_address" required placeholder="00:1B:44:11:3A:B7" class="w-full bg-[#F3F3F3] dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[8px] px-3 py-2 text-[14px] font-medium dark:text-white focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
            </div>
            <button type="submit" class="w-full bg-[#9292C5] text-white font-bold py-2 rounded-[8px] mt-2 hover:bg-[#8585b8] transition-colors">Simpan</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] mt-[16px]">
    @foreach($devices as $index => $device)
    <div x-data="{ isEditing: false, nama: '{{ $device->name }}', lokasi: '{{ $device->station ? $device->station->location : '-' }}' }" class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] md:p-[24px] shadow-sm flex flex-col modern-card animate-fade-in-up" style="animation-delay: {{ 0.3 + ($index * 0.1) }}s">
        <div class="flex justify-between items-start mb-[4px]">
            <template x-if="!isEditing">
                <h3 class="font-bold tracking-tight text-[20px] text-black dark:text-white" x-text="nama"></h3>
            </template>
            <template x-if="isEditing">
                <input type="text" x-model="nama" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[8px] px-2 py-1 text-[18px] font-bold dark:text-white w-[120px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
            </template>
            
            @php
                // Cek status online berdasarkan log sensor terakhir (misal 5 menit terakhir)
                $lastLog = \App\Models\SensorLog::where('device_id', $device->id)->latest('created_at')->first();
                $isOnline = false;
                if ($lastLog && \Carbon\Carbon::parse($lastLog->created_at)->diffInMinutes(now()) <= 15) {
                    $isOnline = true;
                }
            @endphp
            
            @if($isOnline)
            <div class="bg-white dark:bg-[#344034] px-[12px] py-[4px] rounded-full border border-[#e2f1e2] dark:border-transparent flex items-center gap-2 shadow-sm">
                <div class="w-[7px] h-[7px] rounded-full bg-[#6BBF6B]"></div>
                <span class="text-[#6BBF6B] font-[700] text-[12px]">Online</span>
            </div>
            @else
            <div class="bg-white dark:bg-[#402929] px-[12px] py-[4px] rounded-full border border-[#fde8e8] dark:border-transparent flex items-center gap-2 shadow-sm">
                <div class="w-[7px] h-[7px] rounded-full bg-[#e02424]"></div>
                <span class="text-[#e02424] font-[700] text-[12px]">Offline</span>
            </div>
            @endif
        </div>
        <template x-if="!isEditing">
            <p class="text-[13px] text-[#555] dark:text-[#a5a5d1] font-medium mb-[20px]" x-text="lokasi"></p>
        </template>
        <template x-if="isEditing">
            <input type="text" x-model="lokasi" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[6px] px-2 py-1 text-[13px] font-medium dark:text-white w-full mb-[20px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
        </template>
        <div class="flex flex-col gap-[12px] text-[13px] flex-1">
            <div>
                <p class="font-[800] text-black dark:text-white mb-[2px]">MAC Address</p>
                <p class="text-[#555] dark:text-[#a5a5d1] font-[500]">{{ $device->mac_address }}</p>
            </div>
            <div>
                <p class="font-[800] text-black dark:text-white mb-[2px]">Terakhir Aktif</p>
                <p class="text-[#555] dark:text-[#a5a5d1] font-[500]">{{ $lastLog ? \Carbon\Carbon::parse($lastLog->created_at)->diffForHumans() : 'Belum pernah terhubung' }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Error Logs Section -->
<div class="mt-[32px] bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[20px] md:p-[24px] shadow-sm modern-card animate-fade-in-up stagger-5">
    <div class="flex items-center gap-3 mb-[20px]">
        <div class="w-10 h-10 rounded-full bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.1)] flex items-center justify-center text-[#e02424]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div>
            <h3 class="text-[18px] font-bold tracking-tight text-black dark:text-white">Log Error & Pemecahan Masalah</h3>
            <p class="text-[13px] text-[#555] dark:text-[#a5a5d1]">Catatan kegagalan sensor, koneksi, atau notifikasi untuk analisis troubleshooting.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] text-[12px] uppercase tracking-wider text-[#9292C5] font-[800]">
                    <th class="pb-[12px] px-[16px]">Waktu</th>
                    <th class="pb-[12px] px-[16px]">Jenis Error</th>
                    <th class="pb-[12px] px-[16px]">Pesan Detail</th>
                </tr>
            </thead>
            <tbody class="text-[13px] font-[500] text-[#333] dark:text-[#d1d1d6]">
                @if(isset($errorLogs) && count($errorLogs) > 0)
                    @foreach($errorLogs as $log)
                    <tr class="border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.02)] hover:bg-white dark:hover:bg-[rgba(255,255,255,0.02)] transition-colors">
                        <td class="py-[16px] px-[16px] whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}</td>
                        <td class="py-[16px] px-[16px]">
                            <span class="bg-[#fde8e8] dark:bg-[rgba(224,36,36,0.1)] text-[#e02424] px-2 py-1 rounded-md text-[11px] font-[700]">
                                {{ $log->error_type }}
                            </span>
                        </td>
                        <td class="py-[16px] px-[16px] text-[#555] dark:text-[#a5a5d1]">{{ $log->message }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="py-[24px] text-center text-[#9292C5] italic">Tidak ada log error yang tercatat sejauh ini. Sistem berjalan normal.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
