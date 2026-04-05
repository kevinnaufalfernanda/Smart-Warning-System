@extends('layouts.app')
@section('title', 'Perangkat')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-12 gap-[24px]">
    <div class="col-span-12 md:col-span-5 flex flex-col gap-[24px]">
        <!-- Online Card -->
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col justify-between h-[160px] transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white">Perangkat Online</p>
            <p class="text-[48px] font-[800] text-black dark:text-[#9292C5] text-right mt-2">1/3</p>
        </div>
        <!-- Rumus -->
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm text-[#9292C5] dark:text-[#a5a5d1] font-[500] text-[14px] transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white mb-[12px]">Rumus Perhitungan</p>
            <p class="mb-[12px] opacity-80">Mengukur jarak pantulan gelombang suara ke permukaan air</p>
            <p class="font-[700]">Level Air = Tinggi Sensor – Jarak Terukur</p>
        </div>
    </div>
    
    <div class="col-span-12 md:col-span-7">
        <!-- Panduan -->
        <div class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm h-full flex flex-col transition-colors duration-300">
            <p class="text-[22px] font-bold tracking-tight text-black dark:text-white mb-[16px]">Panduan Koneksi NodeMCU</p>
            <div class="text-[13px] text-black dark:text-[#d1d1d6] space-y-4">
                <div>
                    <p class="font-[800] mb-1">Topologi Koneksi</p>
                    <ol class="list-decimal pl-4 space-y-1 text-[#333] dark:text-[#a5a5d1]">
                        <li>Sensor HC-SR04 -> NodeMCU ESP8266</li>
                        <li>NodeMCU -> WiFi Hotspot HP/Router</li>
                        <li>Laptop (server) <- WiFi yang sama</li>
                        <li>NodeMCU POST ke http://[IP_LAPTOP]:8000/api/sensor</li>
                    </ol>
                </div>
                <div>
                    <p class="font-[800] mb-1">Wiring HC-SR04</p>
                    <div class="grid grid-cols-[100px__20px__1fr] text-[#333] dark:text-[#a5a5d1] space-y-1">
                        <div class="font-[700]">VCC</div><div>&rarr;</div><div class="text-right">5V (NodeMCU VIN)</div>
                        <div class="font-[700]">GND</div><div>&rarr;</div><div class="text-right">GND</div>
                        <div class="font-[700]">TRIG</div><div>&rarr;</div><div class="text-right">D1 (GPIO5)</div>
                        <div class="font-[700]">ECHO</div><div>&rarr;</div><div class="text-right">D2 (GPIO4) via Voltage Divider</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] mt-[24px]">
    <!-- EWS 1 -->
    <div x-data="{ isEditing: false, nama: 'EWS 1', lokasi: 'Soekarno-Hatta' }" class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col h-[280px] transition-colors duration-300">
        <div class="flex justify-between items-start mb-4">
            <template x-if="!isEditing">
                <h3 class="font-bold tracking-tight text-[22px] dark:text-white" x-text="nama"></h3>
            </template>
            <template x-if="isEditing">
                <input type="text" x-model="nama" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[8px] px-2 py-1 text-[20px] font-bold dark:text-white w-[120px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
            </template>
            <div class="bg-white dark:bg-[#344034] px-[12px] py-[6px] rounded-[14px] border border-[#e2f1e2] dark:border-transparent flex items-center gap-2 shadow-sm">
                <div class="w-[8px] h-[8px] rounded-full bg-[#6BBF6B]"></div>
                <span class="text-[#6BBF6B] font-[700] text-[13px] tracking-wide">Online</span>
            </div>
        </div>
        <div class="flex-1 flex flex-col justify-center gap-3 text-[13px] text-[#555] dark:text-[#a5a5d1]">
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2 items-center">
                <span class="font-[600]">Lokasi</span>
                <template x-if="!isEditing">
                    <span class="text-black dark:text-white font-[700]" x-text="lokasi"></span>
                </template>
                <template x-if="isEditing">
                    <input type="text" x-model="lokasi" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[6px] px-2 py-1 text-[13px] font-[700] text-right dark:text-white w-[130px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
                </template>
            </div>
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2">
                <span class="font-[600]">MAC Address</span>
                <span class="text-black dark:text-white font-[500] opacity-80">00:1B:44:11:3A:B7</span>
            </div>
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2">
                <span class="font-[600]">Tinggi Instalasi</span>
                <span class="text-black dark:text-white font-[500] opacity-80">200 cm</span>
            </div>
        </div>
        <button @click="isEditing = !isEditing" :class="isEditing ? 'bg-[#6BBF6B] hover:bg-[#5aa85a]' : 'bg-[#9292C5] hover:bg-[#7b7bb2]'" class="mt-4 w-full text-white font-[700] py-[10px] rounded-[12px] text-[13px] transition-colors duration-300 flex items-center justify-center gap-2">
            <template x-if="!isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </template>
            <template x-if="isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </template>
            <span x-text="isEditing ? 'Simpan Perubahan' : 'Edit Detail'"></span>
        </button>
    </div>

    <!-- EWS 2 -->
    <div x-data="{ isEditing: false, nama: 'EWS 2', lokasi: 'B. Katulampa' }" class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col h-[280px] transition-colors duration-300">
        <div class="flex justify-between items-start mb-4">
            <template x-if="!isEditing">
                <h3 class="font-bold tracking-tight text-[22px] dark:text-white" x-text="nama"></h3>
            </template>
            <template x-if="isEditing">
                <input type="text" x-model="nama" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[8px] px-2 py-1 text-[20px] font-bold dark:text-white w-[120px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
            </template>
            <div class="bg-white dark:bg-[#402929] px-[12px] py-[6px] rounded-[14px] border border-[#fde8e8] dark:border-transparent flex items-center gap-2 shadow-[0_2px_10px_rgba(240,82,82,0.2)]">
                <div class="w-[8px] h-[8px] rounded-full bg-[#e02424]"></div>
                <span class="text-[#e02424] font-[700] text-[13px] tracking-wide">Offline</span>
            </div>
        </div>
        <div class="flex-1 flex flex-col justify-center gap-3 text-[13px] text-[#555] dark:text-[#a5a5d1]">
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2 items-center">
                <span class="font-[600]">Lokasi</span>
                <template x-if="!isEditing">
                    <span class="text-black dark:text-white font-[700]" x-text="lokasi"></span>
                </template>
                <template x-if="isEditing">
                    <input type="text" x-model="lokasi" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[6px] px-2 py-1 text-[13px] font-[700] text-right dark:text-white w-[130px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
                </template>
            </div>
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2">
                <span class="font-[600]">MAC Address</span>
                <span class="text-black dark:text-white font-[500] opacity-80">00:1B:44:11:3B:8C</span>
            </div>
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2">
                <span class="font-[600]">Tinggi Instalasi</span>
                <span class="text-black dark:text-white font-[500] opacity-80">350 cm</span>
            </div>
        </div>
        <button @click="isEditing = !isEditing" :class="isEditing ? 'bg-[#6BBF6B] hover:bg-[#5aa85a]' : 'bg-[#9292C5] hover:bg-[#7b7bb2]'" class="mt-4 w-full text-white font-[700] py-[10px] rounded-[12px] text-[13px] transition-colors duration-300 flex items-center justify-center gap-2">
            <template x-if="!isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </template>
            <template x-if="isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </template>
            <span x-text="isEditing ? 'Simpan Perubahan' : 'Edit Detail'"></span>
        </button>
    </div>

    <!-- EWS 3 -->
    <div x-data="{ isEditing: false, nama: 'EWS 3', lokasi: 'Sungai Brantas' }" class="bg-[#F3F3F3] dark:bg-[#20212a] border border-transparent dark:border-[rgba(255,255,255,0.05)] rounded-[24px] p-[24px] shadow-sm flex flex-col h-[280px] transition-colors duration-300">
        <div class="flex justify-between items-start mb-4">
            <template x-if="!isEditing">
                <h3 class="font-bold tracking-tight text-[22px] dark:text-white" x-text="nama"></h3>
            </template>
            <template x-if="isEditing">
                <input type="text" x-model="nama" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[8px] px-2 py-1 text-[20px] font-bold dark:text-white w-[120px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
            </template>
            <div class="bg-white dark:bg-[#402929] px-[12px] py-[6px] rounded-[14px] border border-[#fde8e8] dark:border-transparent flex items-center gap-2 shadow-[0_2px_10px_rgba(240,82,82,0.2)]">
                <div class="w-[8px] h-[8px] rounded-full bg-[#e02424]"></div>
                <span class="text-[#e02424] font-[700] text-[13px] tracking-wide">Offline</span>
            </div>
        </div>
        <div class="flex-1 flex flex-col justify-center gap-3 text-[13px] text-[#555] dark:text-[#a5a5d1]">
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2 items-center">
                <span class="font-[600]">Lokasi</span>
                <template x-if="!isEditing">
                    <span class="text-black dark:text-white font-[700]" x-text="lokasi"></span>
                </template>
                <template x-if="isEditing">
                    <input type="text" x-model="lokasi" class="bg-white dark:bg-[#1a1b24] border border-[#E5E5EF] dark:border-[rgba(255,255,255,0.1)] rounded-[6px] px-2 py-1 text-[13px] font-[700] text-right dark:text-white w-[130px] focus:outline-none focus:ring-2 focus:ring-[#9292C5]">
                </template>
            </div>
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2">
                <span class="font-[600]">MAC Address</span>
                <span class="text-black dark:text-white font-[500] opacity-80">00:1B:44:11:3C:9A</span>
            </div>
            <div class="flex justify-between border-b border-[#E5E5EF] dark:border-[rgba(255,255,255,0.05)] pb-2">
                <span class="font-[600]">Tinggi Instalasi</span>
                <span class="text-black dark:text-white font-[500] opacity-80">150 cm</span>
            </div>
        </div>
        <button @click="isEditing = !isEditing" :class="isEditing ? 'bg-[#6BBF6B] hover:bg-[#5aa85a]' : 'bg-[#9292C5] hover:bg-[#7b7bb2]'" class="mt-4 w-full text-white font-[700] py-[10px] rounded-[12px] text-[13px] transition-colors duration-300 flex items-center justify-center gap-2">
            <template x-if="!isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </template>
            <template x-if="isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </template>
            <span x-text="isEditing ? 'Simpan Perubahan' : 'Edit Detail'"></span>
        </button>
    </div>
</div>
@endsection
