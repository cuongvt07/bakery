<div wire:poll.30s class="p-3 space-y-3 pb-20">
    {{-- Header --}}
    <div class="flex items-center gap-3 pb-2">
        <a href="{{ route('employee.shifts.schedule') }}" class="p-2 bg-white rounded-lg shadow-sm border border-gray-100 text-gray-500 hover:text-indigo-600 active:scale-95 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900">Đăng ký ca làm</h1>
                <div wire:loading wire:target="$refresh" class="flex items-center gap-1 text-xs text-indigo-600">
                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-gray-500 text-xs text-xs">Chọn ca để đăng ký ngay lập tức</p>
        </div>
    </div>

    {{-- Agency Selector --}}
    <div class="bg-white rounded-xl shadow-sm p-3">
        <select wire:model.live="selectedAgencyId" class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
            @foreach($agencies as $agency)
                <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
            @endforeach
        </select>
    </div>

    {{-- Month Navigation --}}
    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center justify-between">
        <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="text-center">
            <div class="font-bold text-gray-900 text-sm">Tháng {{ $currentMonth->format('m/Y') }}</div>
        </div>
        <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    {{-- Monthly Grid --}}
    <div class="space-y-3">
        @foreach($days as $day)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            {{-- Day Header --}}
            <div class="bg-gray-50 px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                <span class="font-bold text-gray-900 text-sm capitalize">{{ $day->locale('vi')->dayName }} <span class="text-gray-400 font-normal">({{ $day->format('d/m') }})</span></span>
                @if($day->isToday()) <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold">Hôm nay</span> @endif
            </div>

            {{-- Time Slots --}}
            <div class="p-2 grid grid-cols-2 gap-2">
                @forelse($availableSlots as $slot)
                @php
                    $startTime = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                    $key = $day->format('Y-m-d') . '_' . $startTime;
                    $isRegistered = isset($registeredShifts[$key]);
                @endphp
                
                <button 
                    wire:click="toggleShift('{{ $day->format('Y-m-d') }}', {{ $slot->id }})"
                    wire:loading.class="opacity-50 cursor-wait"
                    class="relative p-2 rounded-lg border text-left transition-all duration-200 {{ $isRegistered ? 'bg-green-50 border-green-500 shadow-sm' : 'bg-white border-gray-200 hover:border-gray-400' }}"
                >
                    <!-- Loading Spinner -->
                    <div wire:loading wire:target="toggleShift('{{ $day->format('Y-m-d') }}', {{ $slot->id }})" class="absolute inset-0 flex items-center justify-center bg-white/50 rounded-lg">
                        <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold {{ $isRegistered ? 'text-green-800' : 'text-gray-700' }}">{{ $slot->name ?? $slot->ten_mau }}</div>
                            <div class="text-[10px] {{ $isRegistered ? 'text-green-600' : 'text-gray-500' }} mt-0.5">
                                {{ $startTime }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                            </div>
                        </div>
                        <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $isRegistered ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-300' }}">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                    </div>
                </button>
                @empty
                <div class="col-span-2 text-center text-xs text-gray-400 py-2">-- Không có ca --</div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
