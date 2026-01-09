<div class="p-4 space-y-4">
    {{-- Header with Month Navigation --}}
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <div class="flex items-center justify-between mb-4">
            <button wire:click="previousMonth" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <h2 class="text-xl font-bold text-gray-900">
                {{ $currentMonth->format('m/Y') }}
            </h2>

            <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-4 gap-2 mb-4">
            <div class="text-center p-2 bg-blue-50 rounded-lg">
                <div class="font-bold text-blue-600">{{ $monthlyStats['total'] }}</div>
                <div class="text-xs text-gray-600">Tổng</div>
            </div>

            <div class="text-center p-2 bg-purple-50 rounded-lg">
                <div class="font-bold text-purple-600">{{ $monthlyStats['upcoming'] }}</div>
                <div class="text-xs text-gray-600">Sắp tới</div>
            </div>
            <div class="text-center p-2 bg-green-50 rounded-lg">
                <div class="font-bold text-green-600">{{ $monthlyStats['completed'] }}</div>
                <div class="text-xs text-gray-600">Đã xong</div>
            </div>
            {{-- Placeholder for alignment or removed stat --}}
        </div>

        {{-- View Toggle --}}
        <div class="flex gap-2">
            <button wire:click="toggleViewMode"
                class="flex-1 btn-mobile {{ $viewMode === 'calendar' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                📅 Lịch
            </button>
            <button wire:click="toggleViewMode"
                class="flex-1 btn-mobile {{ $viewMode === 'list' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                📋 Danh sách
            </button>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-2">
        <button wire:click="setFilter('')"
            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $filterStatus === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            Tất cả
        </button>
        <button wire:click="setFilter('approved')"
            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $filterStatus === 'approved' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            Sắp tới
        </button>
        <button wire:click="setFilter('completed')"
            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $filterStatus === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            Đã xong
        </button>
    </div>

    @if ($viewMode === 'calendar')
        {{-- Calendar View --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            {{-- Day Headers --}}
            <div class="grid grid-cols-7 bg-gray-50 border-b">
                @foreach (['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'] as $day)
                    <div class="text-center py-2 text-xs font-semibold text-gray-600">{{ $day }}</div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            <div class="grid grid-cols-7">
                @foreach ($calendarDays as $day)
                    <div
                        class="border-b border-r min-h-[80px] p-1 {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : '' }} {{ $day['isToday'] ? 'bg-blue-50' : '' }}">
                        <div
                            class="text-xs font-semibold {{ !$day['isCurrentMonth'] ? 'text-gray-400' : 'text-gray-700' }} {{ $day['isToday'] ? 'text-blue-600' : '' }} mb-1">
                            {{ $day['date']->day }}
                        </div>

                        @foreach ($day['shifts'] as $shift)
                            <div
                                class="text-[10px] px-1 py-0.5 rounded mb-1 border {{ match ($shift->trang_thai) {
                                    'approved' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'rejected' => 'bg-red-50 text-red-800 border-red-200',
                                    default => 'bg-gray-100',
                                } }}">
                                <div class="font-bold truncate text-[9px]">{{ $shift->agency->ten_diem_ban ?? 'KĐ' }}
                                </div>
                                {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }}
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- List View --}}
        <div class="space-y-3">
            @forelse($shifts as $shift)
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($shift->ngay_lam)->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($shift->gio_ket_thuc)->format('H:i') }}
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium {{ match ($shift->trang_thai) {
                                'approved' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-gray-100 text-gray-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100',
                            } }}">
                            {{ match ($shift->trang_thai) {
                                'approved' => '🔵 Sắp tới',
                                'completed' => '⚪ Đã xong',
                                'rejected' => '🔴 Từ chối',
                                default => $shift->trang_thai,
                            } }}
                        </span>
                    </div>

                    @if ($shift->agency)
                        <div class="text-sm text-gray-600 mb-3">
                            📍 {{ $shift->agency->ten_diem_ban }}
                        </div>
                    @endif

                    {{-- Only show actions for approved upcoming shifts --}}
                    @if ($shift->trang_thai === 'approved')
                        {{-- Show pending request badge if exists --}}
                        @if (isset($pendingRequests[$shift->id]))
                            <div
                                class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs text-center">
                                ⏳
                                {{ $pendingRequests[$shift->id]['type'] === 'doi_ca' ? 'Đang chờ duyệt đổi ca' : 'Đang chờ duyệt xin nghỉ' }}
                            </div>
                        @else
                            <div class="flex gap-2">
                                <button wire:click="requestChange({{ $shift->id }})"
                                    class="flex-1 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-full text-sm font-medium shadow-md active:scale-95 transition-all">
                                    🔁 Đổi ca
                                </button>
                                <button wire:click="requestOff({{ $shift->id }})"
                                    class="flex-1 py-2 bg-red-500 hover:bg-red-600 text-white rounded-full text-sm font-medium shadow-md active:scale-95 transition-all">
                                    🛑 Xin nghỉ
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            @empty
                <div class="bg-gray-50 rounded-2xl p-8 text-center">
                    <div class="text-4xl mb-2">📅</div>
                    <p class="text-gray-600">Không có ca làm việc nào</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Sub Navigation / Quick Actions --}}
    <div class="pt-4 pb-20">
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('employee.shifts.register') }}"
                class="flex items-center justify-center gap-2 bg-indigo-600 text-white rounded-xl py-3 font-bold shadow-md active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Đăng ký ca
            </a>
            <a href="{{ route('employee.shifts.requests') }}"
                class="flex items-center justify-center gap-2 bg-white text-gray-700 border border-gray-200 rounded-xl py-3 font-bold shadow-sm active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Yêu cầu
            </a>
        </div>
    </div>

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
                <div class="{{ $requestType === 'xin_nghi' ? 'bg-red-600' : 'bg-orange-600' }} px-6 py-4 rounded-t-xl">
                    <h3 class="text-white font-bold text-lg">
                        {{ $requestType === 'xin_nghi' ? '🛑 Xin nghỉ ca' : '🔁 Đổi ca làm việc' }}
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lý do</label>
                        <textarea wire:model="requestReason" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                            placeholder="Nhập lý do (tùy chọn, tối thiểu 10 ký tự nếu có)..."></textarea>
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
