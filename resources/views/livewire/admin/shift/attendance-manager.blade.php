<div class="p-6" 
    x-data="{ syncPercent: 0, syncCurrent: 0, syncTotal: 0, syncing: false }"
    x-on:sync-progress.window="syncPercent = $event.detail.percent; syncCurrent = $event.detail.current; syncTotal = $event.detail.total; syncing = true">

    <!-- Fixed Bottom-Right Progress Notification -->
    <div wire:loading wire:target="syncAttendance" 
        class="fixed bottom-6 right-6 z-50 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 w-80 animate-pulse">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div>
                <p class="font-bold text-gray-800">Đang đồng bộ công...</p>
                <p class="text-sm text-gray-500">
                    Nhân viên <span x-text="syncCurrent" class="font-semibold text-green-600"></span> / <span x-text="syncTotal" class="font-semibold"></span>
                </p>
            </div>
        </div>
        <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
            <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-300"
                x-bind:style="'width: ' + syncPercent + '%'">
            </div>
        </div>
        <p class="text-center text-sm font-bold text-green-600 mt-2" x-text="syncPercent + '%'"></p>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Quản lý Chấm công</h2>

        <div class="flex items-center gap-4">
            <!-- Bulk Sync Button -->
            <button wire:click="syncAttendance" wire:loading.attr="disabled"
                class="bg-green-500 hover:bg-green-600 disabled:bg-green-300 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow-md transition-all">
                <span wire:loading.remove wire:target="syncAttendance">🔄 Đồng bộ toàn bộ</span>
                <span wire:loading wire:target="syncAttendance">⏳ Đang xử lý...</span>
            </button>

            <label class="text-gray-600 font-medium">Tháng:</label>
            <input type="month" wire:model.live="month"
                class="border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:ring-amber-500 focus:border-amber-500">
        </div>
    </div>

    <!-- Summary Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mã NV
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng
                        Đăng ký</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng
                        Công (Ca)</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng
                        Giờ</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hệ số
                        Lương (₫/h)</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lương
                        (VNĐ)</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hành
                        động</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($summary as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div
                                        class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold">
                                        {{ substr($row['user']->name ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $row['user']->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $row['user']->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $row['user']->ma_nhan_vien ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $row['registered_count'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $row['attended_count'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-800">
                            {{ $row['total_hours'] }} h
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-blue-700 bg-blue-50">
                            {{ number_format($row['hourly_rate']) }} ₫
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-700 bg-green-50">
                            {{ number_format($row['total_salary']) }} ₫
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button wire:click="showDetail({{ $row['user']->id }})"
                                class="text-amber-600 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition-colors">
                                Chi tiết
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-500 italic">
                            Không có dữ liệu chấm công cho tháng này
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Detail Modal -->
    @if ($showDetailModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                                Chi tiết chấm công - {{ $selectedUser->name ?? '' }} -
                                {{ \Carbon\Carbon::parse($month)->format('m/Y') }}
                            </h3>
                            <button wire:click="syncAttendance" wire:loading.attr="disabled"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm flex items-center">
                                <span wire:loading.remove wire:target="syncAttendance">🔄 Đồng bộ công</span>
                                <span wire:loading wire:target="syncAttendance">⏳ Đang xử lý...</span>
                            </button>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-2 overflow-hidden border border-gray-200 rounded-lg">
                            <table class="min-w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase w-10">
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Ngày
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Thứ
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Số
                                            ca</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Tổng
                                            giờ</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">
                                            Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100 text-sm">
                                    @foreach ($detailData as $index => $day)
                                        {{-- Skip days without any activity --}}
                                        @if (!$day['has_activity'])
                                            @continue
                                        @endif

                                        @php
                                            $isWeekend = in_array($day['day_of_week'], [0, 6]);
                                            $dayName = match ($day['day_of_week']) {
                                                0 => 'CN',
                                                1 => 'Hai',
                                                2 => 'Ba',
                                                3 => 'Tư',
                                                4 => 'Năm',
                                                5 => 'Sáu',
                                                6 => 'Bảy',
                                            };
                                        @endphp
                                        <!-- Main Day Row -->
                                <tbody x-data="{ expanded: false }" class="border-b border-gray-100">
                                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer {{ $isWeekend ? 'bg-orange-50/30' : '' }}"
                                        @click="expanded = !expanded">
                                        <td class="px-4 py-3 text-center">
                                            @if ($day['has_activity'])
                                                <button class="text-gray-400 hover:text-amber-600 transition-colors">
                                                    <svg x-show="!expanded" class="w-4 h-4" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                    <svg x-show="expanded" class="w-4 h-4" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $day['date']->format('d/m/Y') }}</td>
                                        <td
                                            class="px-4 py-3 {{ $day['day_of_week'] == 0 ? 'text-red-500 font-bold' : 'text-gray-600' }}">
                                            {{ $dayName }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($day['total_shifts'] > 0)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $day['total_shifts'] }} ca
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-800">
                                            {{ $day['total_hours'] > 0 ? $day['total_hours'] . 'h' : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-green-600 text-xs font-bold">Chi tiết</span>
                                        </td>
                                    </tr>

                                    <!-- Detailed Shifts Rows (Collapsible) -->
                                    @if ($day['has_activity'])
                                        <tr x-show="expanded" x-collapse style="display: none;">
                                            <td colspan="6" class="bg-gray-50/50 p-2">
                                                <div
                                                    class="rounded-lg border border-gray-200 overflow-hidden bg-white">
                                                    <table class="min-w-full text-xs">
                                                        <thead class="bg-amber-50">
                                                            <tr>
                                                                <th
                                                                    class="px-4 py-2 text-left font-medium text-amber-800">
                                                                    Tên Ca - Điểm bán</th>
                                                                <th
                                                                    class="px-4 py-2 text-center font-medium text-amber-800">
                                                                    Lịch ca</th>
                                                                <th
                                                                    class="px-4 py-2 text-center font-medium text-amber-800">
                                                                    Giờ vào</th>
                                                                <th
                                                                    class="px-4 py-2 text-center font-medium text-amber-800">
                                                                    Giờ ra</th>
                                                                <th
                                                                    class="px-4 py-2 text-center font-medium text-amber-800">
                                                                    Công (h)</th>
                                                                <th
                                                                    class="px-4 py-2 text-center font-medium text-amber-800">
                                                                    OT</th>
                                                                <th
                                                                    class="px-4 py-2 text-center font-medium text-amber-800">
                                                                    Ghi chú</th>
                                                                <th
                                                                    class="px-4 py-2 text-center font-medium text-amber-800">
                                                                    Sửa</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-100">
                                                            @foreach ($day['shifts'] as $shift)
                                                                <tr>
                                                                    <td
                                                                        class="px-4 py-2 font-medium {{ $shift['is_extra'] ? 'text-blue-600' : 'text-gray-800' }}">
                                                                        {{ $shift['name'] }}
                                                                        @if ($shift['is_extra'])
                                                                            <span
                                                                                class="ml-1 text-[10px] bg-blue-100 text-blue-800 px-1 rounded">Bổ
                                                                                sung</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-2 text-center text-gray-500">
                                                                        {{ $shift['schedule_time'] }}</td>
                                                                    <td
                                                                        class="px-4 py-2 text-center font-mono text-gray-700">
                                                                        {{ $shift['actual_in'] }}</td>
                                                                    <td
                                                                        class="px-4 py-2 text-center font-mono text-gray-700">
                                                                        {{ $shift['actual_out'] }}</td>
                                                                    <td
                                                                        class="px-4 py-2 text-center font-bold text-gray-900">
                                                                        {{ $shift['hours'] }}</td>
                                                                    <td class="px-4 py-2 text-center text-lg">
                                                                        @if ($shift['hours'] > 8)
                                                                            <span
                                                                                class="text-green-600 font-bold">✓</span>
                                                                        @else
                                                                            <span
                                                                                class="text-red-500 font-bold">✗</span>
                                                                        @endif

                                                                        @if ($shift['is_ot'])
                                                                            <span
                                                                                class="block text-[10px] bg-purple-100 text-purple-700 font-bold px-1 rounded mt-1">OT</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-2 text-center">
                                                                        @if ($shift['status'] == 'absent')
                                                                            <span
                                                                                class="text-red-500 font-bold">VẮNG</span>
                                                                        @elseif($shift['status'] == 'future')
                                                                            <span class="text-gray-400">Chưa đến</span>
                                                                        @else
                                                                            <span
                                                                                class="text-green-600 font-bold">OK</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-2 text-center flex justify-center gap-2">
                                                                        <button
                                                                            wire:click="editShift({{ $shift['id'] ?? 'null' }}, {{ $shift['schedule_id'] ?? 'null' }}, '{{ $day['date']->format('Y-m-d') }}')"
                                                                            class="text-blue-500 hover:text-blue-700" title="Sửa Lịch Ca">
                                                                            ✏️
                                                                        </button>
                                                                        @if(isset($shift['has_phieu']) && $shift['has_phieu'])
                                                                        <button
                                                                            wire:click="editShiftData({{ $shift['id'] }})"
                                                                            class="text-amber-500 hover:text-amber-700" title="Sửa Số Liệu (Tiền/Hàng)">
                                                                            📦
                                                                        </button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        {{-- Summary Row --}}
                                                        <tfoot
                                                            class="bg-gradient-to-r from-amber-100 to-yellow-100 border-t-2 border-amber-300">
                                                            <tr>
                                                                <td colspan="3"
                                                                    class="px-4 py-3 text-right font-bold text-gray-900 text-sm">
                                                                    📊 TỔNG HỢP THÁNG:
                                                                </td>
                                                                <td class="px-4 py-3 text-center">
                                                                    @php
                                                                        $totalShifts = collect($detailData)->sum(
                                                                            'total_shifts',
                                                                        );
                                                                    @endphp
                                                                    <span
                                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-600 text-white">
                                                                        {{ $totalShifts }} ca
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-3 text-center">
                                                                    @php
                                                                        $totalHours = collect($detailData)->sum(
                                                                            'total_hours',
                                                                        );
                                                                    @endphp
                                                                    <span
                                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-600 text-white">
                                                                        {{ round($totalHours, 2) }}h
                                                                    </span>
                                                                </td>
                                                                <td
                                                                    class="px-4 py-3 text-center text-gray-600 text-xs italic">
                                                                    -
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
    @endforeach
    </tbody>
    {{-- Summary Footer --}}
    <tfoot class="bg-gradient-to-r from-amber-50 to-yellow-50 border-t-2 border-amber-200">
        <tr>
            <td colspan="3" class="px-4 py-4 text-right font-bold text-gray-900">
                📊 TỔNG HỢP THÁNG:
            </td>
            <td class="px-4 py-4 text-center">
                @php
                    $totalShifts = collect($detailData)->sum('total_shifts');
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-blue-600 text-white shadow-sm">
                    {{ $totalShifts }} ca
                </span>
            </td>
            <td class="px-4 py-4 text-center">
                @php
                    $totalHours = collect($detailData)->sum('total_hours');
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-green-600 text-white shadow-sm">
                    {{ round($totalHours, 2) }}h
                </span>
            </td>
            <td class="px-4 py-4 text-center text-gray-500 text-sm">
                -
            </td>
        </tr>
    </tfoot>
    </table>
</div>
</div>
<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
    <button wire:click="closeModal" type="button"
        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
        Đóng
    </button>
</div>
</div>
</div>
</div>
@endif

<!-- Edit Shift Modal -->
@if ($showEditModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Chỉnh sửa chấm công
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Giờ vào</label>
                                    <input type="time" wire:model="editingCheckIn"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Giờ ra</label>
                                    <input type="time" wire:model="editingCheckOut"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="editingIsOt" id="is_ot"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_ot" class="ml-2 block text-sm text-gray-900">
                                        Xác nhận OT (Overtime)
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Ghi chú</label>
                                    <textarea wire:model="editingNote" rows="3"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                                </div>
                                <p class="text-xs text-gray-500 italic">Nếu tick OT, tổng giờ sẽ tính full thời gian
                                    thực tế. Nếu không, chỉ tính tối đa theo lịch (thương là 8h).</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="saveShift"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Lưu
                    </button>
                    <button type="button" wire:click="$set('showEditModal', false)"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Edit Shift Data (Stock/Cash) Modal -->
@if ($showEditDataModal)
    <div class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                
                <div class="bg-amber-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                        📦 Sửa Số Liệu Ca Làm Việc
                    </h3>
                    <button wire:click="$set('showEditDataModal', false)" class="text-white hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[70vh] overflow-y-auto">
                    <!-- Cash Input -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">💵 Tiền mặt đầu ca (VNĐ)</label>
                        <input type="number" wire:model="editingCash"
                            class="block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-amber-500 focus:border-amber-500 font-bold text-lg text-amber-700">
                        @error('editingCash') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Products Table -->
                    <div>
                        <h4 class="font-bold text-gray-800 mb-3 border-b pb-2">🥖 Danh sách bánh (Nhận / Bán)</h4>
                        
                        <div class="overflow-hidden border border-gray-200 rounded-lg">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Tên Sản Phẩm</th>
                                        <th class="px-4 py-2 text-center font-semibold text-gray-700 w-32 border-l border-gray-200">SL Nhận</th>
                                        <th class="px-4 py-2 text-center font-semibold text-gray-700 w-32 border-l border-gray-200">SL Bán</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($editingProducts as $productId => $data)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">
                                                {{ $data['name'] }}
                                            </td>
                                            <td class="px-2 py-2 border-l border-gray-200">
                                                <input type="number" wire:model="editingProducts.{{ $productId }}.nhan"
                                                    class="w-full border-gray-300 rounded shadow-sm focus:ring-amber-500 focus:border-amber-500 text-center font-bold">
                                                @error('editingProducts.'.$productId.'.nhan') <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span> @enderror
                                            </td>
                                            <td class="px-2 py-2 border-l border-gray-200 bg-amber-50/30">
                                                <input type="number" wire:model="editingProducts.{{ $productId }}.ban"
                                                    class="w-full border-gray-300 rounded shadow-sm focus:ring-amber-500 focus:border-amber-500 text-center font-bold text-amber-700">
                                                @error('editingProducts.'.$productId.'.ban') <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span> @enderror
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(empty($editingProducts))
                                        <tr>
                                            <td colspan="3" class="px-4 py-6 text-center text-gray-500 italic">
                                                Ca này chưa có dữ liệu hàng hóa.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 italic">
                            * Lưu ý: Khi lưu, hệ thống sẽ tự động tính lại số lượng Tồn (Nhận - Bán), và bù trừ lại Doanh thu Lý thuyết cho phiếu chốt ca.
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-3 border-t border-gray-200">
                    <button type="button" wire:click="$set('showEditDataModal', false)"
                        class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm transition">
                        Bỏ qua
                    </button>
                    <button type="button" wire:click="saveShiftData"
                        class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-2 bg-amber-600 text-base font-bold text-white hover:bg-amber-700 focus:outline-none sm:text-sm transition">
                        💾 Lưu Số Liệu
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

</div>
