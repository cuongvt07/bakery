<div wire:poll.30s class="p-3 space-y-3 pb-20">
    {{-- Header --}}
    <div class="flex items-center gap-3 pb-2">
        <a href="{{ route('employee.shifts.schedule') }}"
            class="p-2 bg-white rounded-lg shadow-sm border border-gray-100 text-gray-500 hover:text-indigo-600 active:scale-95 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900">Đăng ký ca làm</h1>
                <div wire:loading wire:target="$refresh" class="flex items-center gap-1 text-xs text-indigo-600">
                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
            <p class="text-gray-500 text-xs text-xs">Chọn ca để đăng ký ngay lập tức</p>
        </div>
        <button wire:click="openRequestModal('ticket', null)"
            class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-md active:scale-95 transition-all"
            title="Gửi yêu cầu khẩn cấp">
            🚨
        </button>
    </div>

    {{-- Agency Selector --}}
    <div class="bg-white rounded-xl shadow-sm p-3">
        <select wire:model.live="selectedAgencyId"
            class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
            @foreach ($agencies as $agency)
                <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
            @endforeach
        </select>
    </div>

    {{-- Month Navigation --}}
    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center justify-between">
        <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <div class="text-center">
            <div class="font-bold text-gray-900 text-sm">Tháng {{ $currentMonth->format('m/Y') }}</div>
        </div>
        <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    {{-- View Mode Toggle --}}
    <div class="flex gap-2">
        <button wire:click="toggleViewMode"
            class="flex-1 btn-mobile {{ $viewMode === 'calendar' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            📅 Lịch
        </button>
        <button wire:click="toggleViewMode"
            class="flex-1 btn-mobile {{ $viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            📋 Danh sách
        </button>
    </div>

    @if ($viewMode === 'calendar')
        {{-- Monthly Grid --}}
        <div class="space-y-3">
            @foreach ($days as $day)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    {{-- Day Header --}}
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                        <span class="font-bold text-gray-900 text-sm capitalize">{{ $day->locale('vi')->dayName }} <span
                                class="text-gray-400 font-normal">({{ $day->format('d/m') }})</span></span>
                        @if ($day->isToday())
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold">Hôm
                                nay</span>
                        @endif
                    </div>

                    {{-- Time Slots --}}
                    <div class="p-2 grid grid-cols-2 gap-2">
                        @forelse($availableSlots as $slot)
                            @php
                                $startTime = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                                $key = $day->format('Y-m-d') . '_' . $startTime;
                                $isRegistered = isset($registeredShifts[$key]);
                            @endphp

                            <button wire:click="toggleShift('{{ $day->format('Y-m-d') }}', {{ $slot->id }})"
                                wire:loading.class="opacity-50 cursor-wait"
                                class="relative p-2 rounded-lg border text-left transition-all duration-200 {{ $isRegistered ? 'bg-green-50 border-green-500 shadow-sm' : 'bg-white border-gray-200 hover:border-gray-400' }}">
                                <!-- Loading Spinner -->
                                <div wire:loading
                                    wire:target="toggleShift('{{ $day->format('Y-m-d') }}', {{ $slot->id }})"
                                    class="absolute inset-0 flex items-center justify-center bg-white/50 rounded-lg">
                                    <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <div
                                            class="text-xs font-bold {{ $isRegistered ? 'text-green-800' : 'text-gray-700' }}">
                                            {{ $slot->name ?? $slot->ten_mau }}</div>
                                        <div
                                            class="text-[10px] {{ $isRegistered ? 'text-green-600' : 'text-gray-500' }} mt-0.5">
                                            {{ $startTime }} -
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                        </div>
                                    </div>
                                    <div
                                        class="w-5 h-5 rounded-full flex items-center justify-center {{ $isRegistered ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-300' }}">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
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
    @else
        {{-- List View --}}
        <div class="space-y-3">
            @php
                $allSchedules = collect();
                foreach ($registeredShifts as $key => $scheduleId) {
                    $schedule = \App\Models\ShiftSchedule::with(['shiftTemplate', 'agency'])->find($scheduleId);
                    if ($schedule) {
                        $allSchedules->push($schedule);
                    }
                }
                $allSchedules = $allSchedules->sortBy('ngay_lam');
            @endphp

            @forelse($allSchedules as $schedule)
                @php
                    $isPast = $schedule->ngay_lam->isPast();
                @endphp

                <div
                    class="bg-white rounded-xl shadow-sm p-4 border {{ $isPast ? 'opacity-60 bg-gray-50' : 'border-gray-100' }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="font-bold text-gray-900">{{ $schedule->ngay_lam->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500 capitalize">
                                {{ $schedule->ngay_lam->locale('vi')->dayName }}</div>
                        </div>
                        @if ($schedule->ngay_lam->isToday())
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold">Hôm
                                nay</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 mb-3 text-sm text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $schedule->shiftTemplate->name ?? 'Ca tùy chỉnh' }}</span>
                    </div>

                    <div class="text-xs text-gray-500 mb-3">
                        {{ \Carbon\Carbon::parse($schedule->gio_bat_dau)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($schedule->gio_ket_thuc)->format('H:i') }}
                    </div>

                    @if (!$isPast)
                        {{-- Show pending request badge if exists --}}
                        @if (isset($pendingRequests[$schedule->id]))
                            <div class="pt-3 border-t border-gray-100">
                                <div
                                    class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs text-center">
                                    ⏳
                                    {{ $pendingRequests[$schedule->id]['type'] === 'doi_ca' ? 'Đang chờ duyệt đổi ca' : 'Đang chờ duyệt xin nghỉ' }}
                                </div>
                            </div>
                        @else
                            <div class="flex gap-2 pt-3 border-t border-gray-100">
                                <button wire:click="openRequestModal('doi_ca', {{ $schedule->id }})"
                                    class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-full text-sm font-medium text-center shadow-md active:scale-95 transition-all">
                                    🔁 Đổi ca
                                </button>
                                <button wire:click="openRequestModal('xin_nghi', {{ $schedule->id }})"
                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-full text-sm font-medium text-center shadow-md active:scale-95 transition-all">
                                    🛑 Xin nghỉ
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="pt-3 border-t border-gray-100 text-center text-xs text-gray-400">
                            Không thể thay đổi ca đã qua
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-gray-50 rounded-2xl p-8 text-center">
                    <div class="text-4xl mb-2">📅</div>
                    <p class="text-gray-600">Chưa có ca nào được đăng ký</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Success Message --}}
    @if (session('success'))
        <div
            class="fixed bottom-20 left-4 right-4 bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl shadow-lg z-40">
            {{ session('success') }}
        </div>
    @endif

    {{-- Request Modal --}}
    @if ($showRequestModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div
                    class="{{ $requestType === 'xin_nghi' ? 'bg-red-600' : ($requestType === 'ticket' ? 'bg-red-600' : 'bg-orange-600') }} px-6 py-4 rounded-t-xl">
                    <h3 class="text-white font-bold text-lg">
                        @if ($requestType === 'xin_nghi')
                            🛑 Xin nghỉ ca
                        @elseif($requestType === 'ticket')
                            🚨 Gửi Ticket Khẩn Cấp
                        @else
                            🔁 Đổi ca làm việc
                        @endif
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    {{-- For shift change: show shift selection --}}
                    @if ($requestType === 'doi_ca')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn điểm bán</label>
                            <select wire:model.live="newAgencyId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                @foreach ($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ngày</label>
                            <input type="date" wire:model.live="newShiftDate"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ca muốn đổi sang *</label>
                            <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                                @forelse($availableShiftsForChange as $shift)
                                    <button type="button"
                                        wire:click="$set('selectedNewShiftId', {{ $shift->id }})"
                                        class="p-3 border-2 rounded-lg text-left transition-all {{ $selectedNewShiftId == $shift->id ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-orange-300' }}">
                                        <div class="font-bold text-sm">{{ $shift->name ?? $shift->ten_mau }}</div>
                                        <div class="text-xs text-gray-600">
                                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</div>
                                    </button>
                                @empty
                                    <div class="col-span-2 text-center text-sm text-gray-500 py-4">Chọn điểm bán và
                                        ngày để xem ca available</div>
                                @endforelse
                            </div>
                            @error('selectedNewShiftId')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lý do *</label>
                        <textarea wire:model="requestReason" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                            placeholder="Nhập lý do chi tiết (ít nhất 10 ký tự)..."></textarea>
                        @error('requestReason')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="closeRequestModal"
                            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg">Hủy</button>
                        <button wire:click="submitRequest"
                            class="flex-1 {{ $requestType === 'xin_nghi' ? 'bg-red-600' : 'bg-orange-600' }} text-white py-3 rounded-lg font-bold">Gửi
                            đơn</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
