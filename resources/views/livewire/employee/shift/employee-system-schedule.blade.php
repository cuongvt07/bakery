<div class="p-4 space-y-4 pb-20">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Lịch Chung Hệ Thống</h2>
            <p class="text-sm text-gray-500">Xem lịch làm việc của tất cả các điểm</p>
        </div>
    </div>

    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
            <p class="text-xs text-blue-600 font-medium">Tổng ca</p>
            <p class="text-xl font-bold text-blue-700">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-indigo-50 px-3 py-2 rounded-lg border border-indigo-200">
            <p class="text-xs text-indigo-600 font-medium">Sắp tới</p>
            <p class="text-xl font-bold text-indigo-700">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
            <p class="text-xs text-gray-600 font-medium">Đã xong</p>
            <p class="text-xl font-bold text-gray-700">{{ $stats['completed'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 space-y-3">
        <!-- Date Range -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" wire:model.live="dateFrom"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" wire:model.live="dateTo"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>

        <!-- Search -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <input type="text" wire:model.live.debounce.300ms="search"
                class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Tên nhân viên...">
        </div>

        <!-- Advanced Filters -->
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Điểm bán</label>
                <select wire:model.live="agencyFilter"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Tất cả</option>
                    @foreach ($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nhân viên</label>
                <select wire:model.live="employeeFilter"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Tất cả nhân viên</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->ho_ten }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="statusFilter"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="approved">🔵 Sắp tới</option>
                    <option value="completed">⚪ Đã kết thúc</option>
                    <option value="pending">⏳ Chờ duyệt</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Calendar Grid by Agency -->
    <div class="space-y-4">
        @php
            $agencyColors = [
                'bg-blue-100 text-blue-900 border-blue-200',
                'bg-orange-100 text-orange-900 border-orange-200',
                'bg-green-100 text-green-900 border-green-200',
                'bg-purple-100 text-purple-900 border-purple-200',
                'bg-pink-100 text-pink-900 border-pink-200',
                'bg-teal-100 text-teal-900 border-teal-200',
            ];
        @endphp
        @foreach ($groupedAgencies as $agency)
            @php
                $themeClass = $agencyColors[$loop->index % count($agencyColors)];
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-3 py-2 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-sm">
                        🏪 {{ $agency->ten_diem_ban }}
                    </h3>
                    <span class="text-xs font-medium bg-white px-2 py-1 rounded border">
                        {{ count($agency->shiftSchedules) }} ca
                    </span>
                </div>

                <div class="p-3 overflow-x-auto">
                    @if ($agency->shiftTemplates->isEmpty())
                        <p class="text-gray-400 text-sm text-center italic py-2">Chưa có mẫu ca</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 border text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 bg-gray-50 z-10 border-r min-w-[100px]">
                                        Ca
                                    </th>
                                    @php
                                        $startDate = \Carbon\Carbon::parse($dateFrom);
                                        $endDate = \Carbon\Carbon::parse($dateTo);
                                        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
                                    @endphp
                                    @foreach ($period as $date)
                                        <th
                                            class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase min-w-[80px] border-l">
                                            {{ $date->format('d/m') }}<br>
                                            <span class="text-[10px]">{{ $date->locale('vi')->dayName }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($agency->shiftTemplates->where('is_active', true)->sortBy('start_time') as $template)
                                    <tr class="hover:bg-gray-50">
                                        <td
                                            class="px-2 py-2 whitespace-nowrap text-xs font-medium text-gray-900 sticky left-0 bg-white z-10 border-r">
                                            <div class="font-semibold">{{ $template->name }}</div>
                                            <div class="text-[10px] text-gray-500">
                                                {{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}
                                            </div>
                                        </td>

                                        @foreach ($period as $date)
                                            @php
                                                $currentDate = $date->format('Y-m-d');
                                                $cellShifts = $agency->shiftSchedules->filter(function ($s) use (
                                                    $currentDate,
                                                    $template,
                                                ) {
                                                    return \Carbon\Carbon::parse($s->ngay_lam)->isSameDay(
                                                        $currentDate,
                                                    ) &&
                                                        $s->shift_template_id == $template->id &&
                                                        $s->trang_thai !== 'rejected';
                                                });
                                            @endphp
                                            <td class="px-1 py-1 text-center border-l align-top text-[10px]">
                                                @if ($cellShifts->isNotEmpty())
                                                    <div class="flex flex-col gap-1">
                                                        @foreach ($cellShifts as $shift)
                                                            @php
                                                                $departmentColor =
                                                                    $shift->user && $shift->user->department
                                                                        ? $shift->user->department->ma_mau
                                                                        : '#9CA3AF';

                                                                $hex = ltrim($departmentColor, '#');
                                                                $r = hexdec(substr($hex, 0, 2));
                                                                $g = hexdec(substr($hex, 2, 2));
                                                                $b = hexdec(substr($hex, 4, 2));
                                                                $bgColor = "rgba({$r}, {$g}, {$b}, 0.15)";
                                                                $borderColor = "rgba({$r}, {$g}, {$b}, 0.3)";
                                                            @endphp
                                                            <div wire:click="openDetail({{ $shift->id }})"
                                                                class="cursor-pointer p-1 rounded border shadow-sm transition-opacity text-left
                                                                 {{ $shift->trang_thai == 'pending' ? 'opacity-60 border-dashed' : '' }}"
                                                                style="background-color: {{ $bgColor }}; border-color: {{ $borderColor }}; color: {{ $departmentColor }};">
                                                                <div class="truncate">
                                                                    {{ $shift->user->ho_ten ?? 'Unknown' }}</div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Detail Modal (Read-only) -->
    @if ($showDetailModal && $selectedShift)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="px-4 py-3 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-900">Chi tiết ca làm việc</h3>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nhân viên</p>
                            <p class="font-semibold text-base">{{ $selectedShift->user->ho_ten ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $selectedShift->user->ma_nhan_vien ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Điểm bán</p>
                            <p class="font-semibold text-base">{{ $selectedShift->agency->ten_diem_ban ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Thời gian</p>
                            <p class="font-medium text-base">
                                {{ \Carbon\Carbon::parse($selectedShift->gio_bat_dau)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($selectedShift->gio_ket_thuc)->format('H:i') }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($selectedShift->ngay_lam)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Shift Summary (Check-in to Closing) --}}
                    <div class="border-t pt-4 space-y-4">
                        <h4 class="font-bold text-gray-900">Tổng hợp ca làm việc</h4>

                        {{-- Check-in Info --}}
                        @if ($selectedShift->trang_thai_checkin)
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <h5 class="font-semibold text-blue-900 mb-2 flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Check-in
                                </h5>
                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Thời gian:</span>
                                        <span
                                            class="font-medium">{{ $selectedShift->thoi_gian_checkin ? \Carbon\Carbon::parse($selectedShift->thoi_gian_checkin)->format('H:i d/m/Y') : '-' }}</span>
                                    </div>
                                    @if ($selectedShift->tien_mat_dau_ca)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tiền mặt đầu ca:</span>
                                            <span
                                                class="font-medium">{{ number_format($selectedShift->tien_mat_dau_ca) }}đ</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Closing Info (if exists) --}}
                        @if ($selectedShift->phieuChotCa)
                            <div class="bg-green-50 p-3 rounded-lg">
                                <h5 class="font-semibold text-green-900 mb-2 flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Chốt ca
                                </h5>
                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Thời gian:</span>
                                        <span
                                            class="font-medium">{{ \Carbon\Carbon::parse($selectedShift->phieuChotCa->gio_chot)->format('H:i d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tiền mặt:</span>
                                        <span
                                            class="font-medium">{{ number_format($selectedShift->phieuChotCa->tien_mat) }}đ</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Chuyển khoản:</span>
                                        <span
                                            class="font-medium">{{ number_format($selectedShift->phieuChotCa->tien_chuyen_khoan) }}đ</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-1">
                                        <span class="text-gray-600">Tổng thực tế:</span>
                                        <span
                                            class="font-bold">{{ number_format($selectedShift->phieuChotCa->tong_tien_thuc_te) }}đ</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 p-3 rounded-lg text-center text-gray-500 italic text-sm">
                                Chưa chốt ca
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-4 py-3 flex justify-end border-t">
                    <button wire:click="closeDetail"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
