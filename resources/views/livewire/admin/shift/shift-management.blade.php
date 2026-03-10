<div wire:poll.30s>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Quản lý Ca Làm Việc</h2>
            <div class="flex items-center gap-2 mt-1">
                <p class="text-sm text-gray-500">Giám sát và quản lý lịch làm việc của nhân viên</p>
                <div wire:loading wire:target="$refresh" class="flex items-center gap-1 text-xs text-indigo-600">
                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>Đang cập nhật...</span>
                </div>
            </div>
        </div>
        <div class="flex gap-3 items-center">
            <div class="bg-blue-50 px-4 py-2 rounded-lg border border-blue-200">
                <p class="text-xs text-blue-600 font-medium">Tổng ca</p>
                <p class="text-2xl font-bold text-blue-700">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-200">
                <p class="text-xs text-indigo-600 font-medium">Sắp tới</p>
                <p class="text-2xl font-bold text-indigo-700">{{ $stats['approved'] }}</p>
            </div>
            <div class="bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                <p class="text-xs text-gray-600 font-medium">Đã xong</p>
                <p class="text-2xl font-bold text-gray-700">{{ $stats['completed'] }}</p>
            </div>

            <!-- Template Manager Button -->
            <button wire:click="openTemplateManager"
                class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 font-medium flex items-center gap-2 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V6a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h6zm0 0V4m0 2h6m-6 0a2 2 0 012-2h2a 2 0 012 2v2m0 0v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V8z" />
                </svg>
                Quản lý Mẫu Ca
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Hidden: TABS: Workshop vs Stores --}}
    {{-- <div class="mb-6 flex space-x-1 p-1 bg-gray-100 rounded-lg w-fit">
        <button wire:click="setTab('stores')" 
                class="px-6 py-2 rounded-md text-sm font-medium transition-colors {{ $activeTab === 'stores' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            🏪 Các điểm bán
        </button>
        <button wire:click="setTab('workshop')" 
                class="px-6 py-2 rounded-md text-sm font-medium transition-colors {{ $activeTab === 'workshop' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            🏭 Xưởng sản xuất
        </button>
    </div> --}}

    <!-- Filters & Actions -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="space-y-4">
            <!-- Row 1: Search + Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Tên nhân viên, mã NV...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" wire:model.live="dateFrom"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" wire:model.live="dateTo"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                {{-- Hidden: View mode toggle - Always use monitoring mode --}}
                {{-- <div class="md:col-span-2 flex items-end">
                    <div class="flex gap-2 w-full bg-gray-100 p-1 rounded-lg">
                        <button wire:click="toggleViewMode('monitoring')" class="flex-1 p-1.5 rounded {{ $viewMode === 'monitoring' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:bg-gray-200' }}" title="Chế độ Giám sát">
                            📊 Giám sát
                        </button>
                        <button wire:click="toggleViewMode('list')" class="flex-1 p-1.5 rounded {{ $viewMode === 'list' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:bg-gray-200' }}" title="Chế độ Danh sách">
                            📋 List
                        </button>
                    </div>
                </div> --}}
            </div>

            <!-- Row 2: Advanced Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Only show Agency Filter if Monitoring? Or always? -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lọc Điểm bán</label>
                    <select wire:model.live="agencyFilter"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả</option>
                        @foreach ($agencies as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nhân viên</label>
                    <select wire:model.live="employeeFilter"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả nhân viên</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->ho_ten }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select wire:model.live="statusFilter"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả trạng thái</option>
                        <option value="approved">🔵 Sắp tới (Approved)</option>
                        <option value="completed">⚪ Đã kết thúc (Completed)</option>
                        <option value="pending">⏳ Chờ duyệt (Pending)</option>
                        <option value="rejected">🔴 Từ chối (Rejected)</option>
                    </select>
                </div>
            </div>
            <!-- Row 3: Bulk Actions (Only in List Mode) -->
            @if ($viewMode === 'list' && count($selectedShifts) > 0)
                <div
                    class="flex items-center gap-3 p-3 bg-indigo-50 border border-indigo-100 rounded-lg animate-fade-in mt-2">
                    <span class="text-sm font-medium text-indigo-700">Đã chọn {{ count($selectedShifts) }} ca</span>
                    <div class="h-4 w-px bg-indigo-200"></div>
                    <button wire:click="bulkDelete" wire:confirm="Bạn có chắc muốn xóa các ca đã chọn?"
                        class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Xóa
                    </button>
                    <button wire:click="bulkUpdateStatus('completed')"
                        wire:confirm="Đánh dấu các ca này là đã kết thúc?"
                        class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                        📍 Đánh dấu kết thúc
                    </button>
                    <button wire:click="bulkUpdateStatus('approved')"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        ✅ Duyệt/Sắp tới
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if (false) {{-- Deprecated: Batch management moved to BatchMonitoring --}}
        <!-- BATCH MANAGEMENT VIEW -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Danh sách các mẻ đang hoạt động
                </h3>
                <div class="mt-3 flex sm:mt-0 sm:ml-4">
                    <div class="flex rounded-md shadow-sm">
                        <input wire:model.live.debounce.300ms="batchSearch" type="text"
                            placeholder="Tìm tên sản phẩm..."
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">
                        <span
                            class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            @if ($loadingBatches)
                <div class="p-8 text-center text-gray-500">
                    <svg class="animate-spin h-8 w-8 text-indigo-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Đang tải danh sách mẻ...
                </div>
            @elseif(empty($activeBatches))
                <div class="p-8 text-center text-gray-500 bg-gray-50">
                    <p class="mb-2">Không tìm thấy mẻ sản phẩm nào còn hạn sử dụng tại các điểm bán đã chọn.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Điểm Bán</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mẻ / Sản Phẩm</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hạn Sử Dụng</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Đã Phân Bổ</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-red-600 uppercase tracking-wider">
                                    Hỏng (Tổng)</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-indigo-600 uppercase tracking-wider">
                                    Còn (Mẻ Gốc)</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($activeBatches as $batch)
                                <tr x-data="{ showAdjust: false, qty: 0, note: '' }" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $batch['shop_name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="font-bold text-gray-900">{{ $batch['batch_code'] }}</div>
                                        <div class="text-indigo-600">{{ $batch['product_name'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        {{ \Carbon\Carbon::parse($batch['expiry_date'])->format('d/m/Y') }}
                                        @if (\Carbon\Carbon::parse($batch['expiry_date'])->isToday())
                                            <span
                                                class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Hết
                                                hạn hôm nay</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-medium">
                                        {{ number_format($batch['distributed_qty']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-bold">
                                        {{ number_format($batch['global_failed_qty']) }}
                                        <div class="text-xs text-gray-400 font-normal">Tại shop:
                                            {{ number_format($batch['failed_qty']) }}</div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-right text-indigo-700 font-bold bg-indigo-50">
                                        {{ number_format($batch['initial_qty'] - $batch['global_failed_qty']) }}
                                        <!-- Simple calc, likely needs global actual -->
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium relative">
                                        <button @click="showAdjust = !showAdjust"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded">
                                            Báo hỏng
                                        </button>

                                        <!-- Inline Popover for Adjustment -->
                                        <div x-show="showAdjust" @click.away="showAdjust = false" x-transition
                                            class="absolute right-10 top-0 w-80 bg-white shadow-2xl rounded-lg border border-gray-200 p-4 z-50 text-left">
                                            <div class="flex justify-between items-center mb-3">
                                                <h5 class="font-bold text-gray-800">Cập nhật Hỏng/Thất thoát</h5>
                                                <button @click="showAdjust = false"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Số
                                                        lượng hỏng thêm:</label>
                                                    <input type="number" x-model="qty" min="1"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                                        placeholder="Nhập số lượng...">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-500 mb-1">Ghi chú
                                                        (Lý do)
                                                        :</label>
                                                    <textarea x-model="note" rows="2"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                                        placeholder="VD: Rơi vỡ, hết hạn..."></textarea>
                                                </div>
                                                <div class="pt-2">
                                                    <button
                                                        @click="$wire.updateBatchFailureGeneric('{{ $batch['id'] }}', '{{ $batch['detail_id'] }}', qty, note, '{{ $batch['shop_id'] }}'); showAdjust = false; qty = 0; note = ''"
                                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        Xác nhận Cập nhật
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- History Row -->
                                @if (count($batch['history']) > 0)
                                    <tr>
                                        <td colspan="7" class="bg-gray-50 px-6 py-2">
                                            <details class="group">
                                                <summary
                                                    class="flex items-center cursor-pointer text-xs font-medium text-gray-500 hover:text-indigo-600 focus:outline-none">
                                                    <svg class="mr-2 h-4 w-4 transition-transform group-open:rotate-90 text-gray-400"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                    Xem lịch sử cập nhật ({{ count($batch['history']) }})
                                                </summary>
                                                <div class="mt-3 pl-6 border-l-2 border-indigo-200 space-y-3 pb-2">
                                                    @foreach ($batch['history'] as $log)
                                                        <div class="text-xs">
                                                            <div class="flex items-center space-x-2">
                                                                <span
                                                                    class="font-bold text-gray-700">{{ $log->nguoiCapNhat->ho_ten ?? 'Unknown' }}</span>
                                                                <span class="text-gray-400">&bull;</span>
                                                                <span
                                                                    class="text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i d/m/Y') }}</span>
                                                            </div>
                                                            <div class="mt-1 flex items-start">
                                                                <span
                                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mr-2">
                                                                    {{ $log->so_luong_doi > 0 ? '+' : '' }}{{ $log->so_luong_doi }}
                                                                </span>
                                                                <span class="text-gray-600">{{ $log->ghi_chu }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </details>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @elseif ($viewMode === 'monitoring')
        <!-- Monitoring View: Grouped by Agency -->
        <!-- Monitoring View: Grouped by Agency -->
        <div class="grid grid-cols-1 gap-6">
            @php
                $agencyColors = [
                    'bg-blue-100 text-blue-900 border-blue-200',
                    'bg-orange-100 text-orange-900 border-orange-200',
                    'bg-green-100 text-green-900 border-green-200',
                    'bg-purple-100 text-purple-900 border-purple-200',
                    'bg-pink-100 text-pink-900 border-pink-200',
                    'bg-teal-100 text-teal-900 border-teal-200',
                    'bg-yellow-100 text-yellow-900 border-yellow-200',
                ];
            @endphp
            @foreach ($groupedAgencies as $agency)
                @php
                    $themeClass = $agencyColors[$loop->index % count($agencyColors)];
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            @if ($activeTab === 'workshop')
                                🏭
                            @else
                                🏪
                            @endif
                            {{ $agency->ten_diem_ban }}
                        </h3>
                        <span class="text-xs font-medium bg-white px-2 py-1 rounded border">
                            {{ count($agency->shiftSchedules) }} ca đăng ký
                        </span>
                    </div>

                    <div class="p-4 overflow-x-auto">
                        @if ($agency->shiftTemplates->isEmpty())
                            <p class="text-gray-400 text-sm text-center italic py-2">Chưa có mẫu ca nào được cấu hình
                                cho điểm này</p>
                        @else
                            {{-- Calendar Grid - Always show even if no shifts --}}
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 border-r min-w-[150px]">
                                            Ca làm việc
                                        </th>
                                        {{-- Generate Date Headers --}}
                                        @php
                                            $startDate = \Carbon\Carbon::parse($dateFrom);
                                            $endDate = \Carbon\Carbon::parse($dateTo);
                                            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
                                        @endphp
                                        @foreach ($period as $date)
                                            <th
                                                class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px] border-l">
                                                {{ $date->format('d/m') }} <br>
                                                {{ $date->locale('vi')->dayName }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($agency->shiftTemplates->where('is_active', true)->sortBy('start_time') as $template)
                                        <tr class="hover:bg-gray-50">
                                            <td
                                                class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 sticky left-0 bg-white z-10 border-r">
                                                <div class="font-semibold">{{ $template->name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}
                                                </div>
                                            </td>

                                            @foreach ($period as $date)
                                                @php
                                                    $currentDate = $date->format('Y-m-d');
                                                    // Filter shifts for this cell: Match Date AND Match Template ID
                                                    $cellShifts = $agency->shiftSchedules->filter(function ($s) use (
                                                        $currentDate,
                                                        $template,
                                                    ) {
                                                        return \Carbon\Carbon::parse($s->ngay_lam)->isSameDay(
                                                            $currentDate,
                                                        ) && $s->shift_template_id == $template->id;
                                                    });
                                                @endphp
                                                <td
                                                    class="px-2 py-2 text-center border-l align-top text-xs h-16 relative group">
                                                    @if ($cellShifts->isNotEmpty())
                                                        <div class="flex flex-col gap-1 pb-8">
                                                            @foreach ($cellShifts as $shift)
                                                                @php
                                                                    // Get department color instead of agency color
                                                                    $departmentColor =
                                                                        $shift->user && $shift->user->department
                                                                            ? $shift->user->department->ma_mau
                                                                            : '#9CA3AF'; // Gray-400 as default

                                                                    // Convert hex to RGB for background with opacity
                                                                    $hex = ltrim($departmentColor, '#');
                                                                    $r = hexdec(substr($hex, 0, 2));
                                                                    $g = hexdec(substr($hex, 2, 2));
                                                                    $b = hexdec(substr($hex, 4, 2));
                                                                    $bgColor = "rgba({$r}, {$g}, {$b}, 0.15)";
                                                                    $borderColor = "rgba({$r}, {$g}, {$b}, 0.3)";
                                                                @endphp
                                                                <div wire:click="openDetail({{ $shift->id }})"
                                                                    class="cursor-pointer p-1.5 rounded border shadow-sm transition-opacity text-left
                                                                     {{ $shift->trang_thai == 'pending' ? 'opacity-60 border-dashed' : '' }}
                                                                     {{ $shift->trang_thai == 'rejected' ? 'line-through bg-gray-50 text-gray-400 border-gray-200' : '' }}"
                                                                    style="background-color: {{ $bgColor }}; border-color: {{ $borderColor }}; color: {{ $departmentColor }};">
                                                                    <div class="truncate text-sm">
                                                                        {{ $shift->user->ho_ten ?? 'Unknown' }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <!-- Add button at bottom when has shifts -->
                                                        <button
                                                            wire:click="openAddShiftModal({{ $agency->id }}, {{ $template->id }}, '{{ $currentDate }}')"
                                                            class="absolute bottom-1 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity
                                                                       w-6 h-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-lg"
                                                            title="Thêm ca">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <!-- Add button in center when no shifts -->
                                                        <button
                                                            wire:click="openAddShiftModal({{ $agency->id }}, {{ $template->id }}, '{{ $currentDate }}')"
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity
                                                                       w-8 h-8 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-lg mx-auto"
                                                            title="Thêm ca">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </button>
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
    @else
        <!-- List View (Legacy) -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                {{-- Existing Table Code... --}}
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 w-8 text-center">
                                <input type="checkbox" wire:model.live="selectAll" wire:click="toggleSelectAll"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giờ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm bán</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tổng H
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Chốt ca</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($shifts as $shift)
                            <tr class="hover:bg-gray-50 group">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" wire:model.live="selectedShifts"
                                        value="{{ $shift->id }}"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($shift->ngay_lam)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    <span
                                        class="font-medium">{{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }}</span>
                                    - {{ \Carbon\Carbon::parse($shift->gio_ket_thuc)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-3">
                                            {{ substr($shift->user->ho_ten ?? 'Unknown', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $shift->user->ho_ten ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $shift->user->ma_nhan_vien ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $shift->agency->ten_diem_ban ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                    @if ($shift->thoi_gian_checkin)
                                        <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-1 rounded">
                                            {{ floor($shift->total_hours) }}h
                                            {{ round(($shift->total_hours - floor($shift->total_hours)) * 60) }}m
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ match ($shift->trang_thai) {
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-gray-100 text-gray-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        } }}">
                                        {{ match ($shift->trang_thai) {
                                            'approved' => '🔵 Sắp tới',
                                            'completed' => '⚪ Đã kết thúc',
                                            'pending' => '⏳ Chờ duyệt',
                                            'rejected' => '🔴 Từ chối',
                                            default => $shift->trang_thai,
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if ($shift->shiftClosing)
                                        <span class="text-green-600 text-lg" title="Đã chốt ca">✓</span>
                                    @else
                                        <span class="text-gray-300 text-lg">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div
                                        class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="openEditModal({{ $shift->id }})"
                                            class="text-blue-600 hover:text-blue-900" title="Sửa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="openDetail({{ $shift->id }})"
                                            class="text-indigo-600 hover:text-indigo-900" title="Chi tiết">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteShift({{ $shift->id }})"
                                            wire:confirm="Xóa ca làm việc này?"
                                            class="text-red-600 hover:text-red-900" title="Xóa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-lg font-medium">Không tìm thấy ca làm việc nào</p>
                                        <p class="text-sm">Thử thay đổi bộ lọc hoặc thêm ca làm việc mới</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($shifts->hasPages())
                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Edit Modal (Shared) -->
    @if ($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <!-- Modal Content (Use existing) -->
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Sửa ca làm việc</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày làm</label>
                        <input type="date" wire:model="editDate"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giờ bắt đầu</label>
                            <input type="time" wire:model="editStartTime"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giờ kết thúc</label>
                            <input type="time" wire:model="editEndTime"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select wire:model="editStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="approved">Approved (Sắp tới)</option>
                            <option value="completed">Completed (Đã xong)</option>
                            <option value="pending">Pending (Chờ duyệt)</option>
                            <option value="rejected">Rejected (Từ chối)</option>
                        </select>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button wire:click="closeEditModal"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Hủy</button>
                        <button wire:click="saveEdit"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Lưu thay
                            đổi</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Detail Modal (Enhanced with Edit/Delete) -->
    @if ($showDetailModal && $selectedShift)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-900">Chi tiết ca làm việc</h3>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    @if ($editingShift)
                        <!-- Edit Mode -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Điểm bán <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="editAgencyId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="">-- Chọn điểm bán --</option>
                                    @foreach ($agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                                    @endforeach
                                </select>
                                @error('editAgencyId')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($editAgencyId)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ca làm việc <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="editShiftTemplateId"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="">-- Chọn ca --</option>
                                        @php
                                            $selectedAgency = $agencies->firstWhere('id', $editAgencyId);
                                            $templates = $selectedAgency ? $selectedAgency->shiftTemplates : collect();
                                        @endphp
                                        @foreach ($templates as $template)
                                            <option value="{{ $template->id }}">
                                                {{ $template->name }}
                                                ({{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editShiftTemplateId')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày làm</label>
                                <input type="date" wire:model="editDate"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('editDate')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                <select wire:model="editStatus"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="approved">🔵 Approved (Sắp tới)</option>
                                    <option value="completed">⚪ Completed (Đã xong)</option>
                                    <option value="pending">⏳ Pending (Chờ duyệt)</option>
                                    <option value="rejected">🔴 Rejected (Từ chối)</option>
                                </select>
                                @error('editStatus')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @else
                        <!-- View Mode -->

                        <!-- Tab 1: Shift Information (Existing Content) -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Nhân viên</p>
                                <p class="font-semibold text-lg">{{ $selectedShift->user->ho_ten ?? '-' }}</p>
                                <p class="text-sm text-gray-500">{{ $selectedShift->user->ma_nhan_vien ?? '' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Điểm bán</p>
                                <p class="font-semibold text-lg">{{ $selectedShift->agency->ten_diem_ban ?? '-' }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Thời gian</p>
                                <p class="font-medium text-lg">
                                    {{ \Carbon\Carbon::parse($selectedShift->ngay_lam)->format('d/m/Y') }} <br>
                                    {{ \Carbon\Carbon::parse($selectedShift->gio_bat_dau)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($selectedShift->gio_ket_thuc)->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- Shift Summary (Check-in to Closing) --}}
                        <div class="border-t pt-6 space-y-6 mt-4">
                            <h4 class="font-bold text-gray-900">Tổng hợp ca làm việc</h4>

                            {{-- Check-in Info --}}
                            @if ($selectedShift->trang_thai_checkin)
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h5 class="font-semibold text-blue-900 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Check-in
                                    </h5>
                                    <div class="space-y-2 text-sm">
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
                                        @if ($selectedShift->ghi_chu)
                                            <div>
                                                <span class="text-gray-600">Ghi chú:</span>
                                                <p class="mt-1 text-gray-800">{{ $selectedShift->ghi_chu }}</p>
                                            </div>
                                        @endif

                                        {{-- Check-in Images --}}
                                        @php
                                            $checkinImages = $selectedShift->hinh_anh_checkin
                                                ? json_decode($selectedShift->hinh_anh_checkin, true)
                                                : [];
                                        @endphp
                                        @if (!empty($checkinImages))
                                            <div>
                                                <span class="text-gray-600 block mb-2">Ảnh check-in:</span>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($checkinImages as $img)
                                                        <img src="{{ asset('storage/' . $img) }}"
                                                            onclick="openImageModal('{{ asset('storage/' . $img) }}')"
                                                            class="w-20 h-20 object-cover rounded border-2 border-blue-200 cursor-pointer hover:border-blue-400 transition"
                                                            alt="Check-in">
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Closing Info (if exists) --}}
                            @if ($selectedShift->phieuChotCa)
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h5 class="font-semibold text-green-900 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Chốt ca
                                    </h5>
                                    <div class="space-y-2 text-sm">
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
                                        <div class="flex justify-between border-t pt-2">
                                            <span class="text-gray-600">Tổng thực tế:</span>
                                            <span
                                                class="font-bold">{{ number_format($selectedShift->phieuChotCa->tong_tien_thuc_te) }}đ</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tổng lý thuyết:</span>
                                            <span
                                                class="font-medium">{{ number_format($selectedShift->phieuChotCa->tong_tien_ly_thuyet) }}đ</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Chênh lệch:</span>
                                            <span
                                                class="font-bold {{ $selectedShift->phieuChotCa->tien_lech < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ number_format($selectedShift->phieuChotCa->tien_lech) }}đ
                                            </span>
                                        </div>

                                        @if ($selectedShift->phieuChotCa->ghi_chu)
                                            <div class="border-t pt-2">
                                                <span class="text-gray-600">Ghi chú:</span>
                                                <p class="mt-1 text-gray-800">
                                                    {{ $selectedShift->phieuChotCa->ghi_chu }}</p>
                                            </div>
                                        @endif

                                        {{-- Closing Images --}}
                                        @php
                                            $cashImages = $selectedShift->phieuChotCa->anh_tien_mat
                                                ? json_decode($selectedShift->phieuChotCa->anh_tien_mat, true)
                                                : [];
                                            $stockImages = $selectedShift->phieuChotCa->anh_hang_hoa
                                                ? json_decode($selectedShift->phieuChotCa->anh_hang_hoa, true)
                                                : [];
                                        @endphp

                                        @if (!empty($cashImages))
                                            <div class="border-t pt-2">
                                                <span class="text-gray-600 block mb-2">Ảnh tiền mặt:</span>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($cashImages as $img)
                                                        <img src="{{ asset('storage/' . $img) }}"
                                                            onclick="openImageModal('{{ asset('storage/' . $img) }}')"
                                                            class="w-20 h-20 object-cover rounded border-2 border-green-200 cursor-pointer hover:border-green-400 transition"
                                                            alt="Tiền mặt">
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if (!empty($stockImages))
                                            <div class="border-t pt-2">
                                                <span class="text-gray-600 block mb-2">Ảnh hàng hóa:</span>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($stockImages as $img)
                                                        <img src="{{ asset('storage/' . $img) }}"
                                                            onclick="openImageModal('{{ asset('storage/' . $img) }}')"
                                                            class="w-20 h-20 object-cover rounded border-2 border-green-200 cursor-pointer hover:border-green-400 transition"
                                                            alt="Hàng hóa">
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500 italic">
                                    Chưa chốt ca
                                </div>
                            @endif
                        </div>
                        @if (false)
                            <!-- Tab 2: Batch Management (Deprecated/Moved) -->
                            <div class="space-y-6">
                                @if ($loadingBatches)
                                    <div class="py-8 text-center text-gray-500 flex flex-col items-center">
                                        <svg class="animate-spin h-8 w-8 text-indigo-500 mb-2"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <p>Đang tải dữ liệu mẻ...</p>
                                    </div>
                                @elseif(empty($shiftBatches))
                                    <div
                                        class="py-8 text-center text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                        <p>Không tìm thấy mẻ sản phẩm nào được phân bổ cho ca này hoặc điểm bán này.</p>
                                    </div>
                                @else
                                    <div class="overflow-x-auto border rounded-xl shadow-sm">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Mẻ / Mã</th>
                                                    <th
                                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Sản phẩm</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                        HSD</th>
                                                    <th
                                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                        Phân Bổ</th>
                                                    <th
                                                        class="px-4 py-3 text-right text-xs font-medium text-red-500 uppercase">
                                                        Hỏng (Tổng)</th>
                                                    <th
                                                        class="px-4 py-3 text-right text-xs font-medium text-indigo-600 uppercase">
                                                        Còn lại (TT)</th>
                                                    <th
                                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                        Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($shiftBatches as $batch)
                                                    <tr x-data="{ showAdjust: false, qty: 0, note: '' }" class="hover:bg-gray-50">
                                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                            {{ $batch['batch_code'] }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-gray-600">
                                                            {{ $batch['product_name'] }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-center text-gray-500">
                                                            {{ \Carbon\Carbon::parse($batch['expiry_date'])->format('d/m/Y') }}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm text-right font-medium">
                                                            {{ number_format($batch['initial_qty']) }}
                                                        </td>
                                                        <td
                                                            class="px-4 py-3 text-sm text-right text-red-600 font-bold">
                                                            {{ number_format($batch['failed_qty']) }}
                                                        </td>
                                                        <td
                                                            class="px-4 py-3 text-sm text-right text-indigo-700 font-bold bg-indigo-50">
                                                            {{ number_format($batch['actual_qty']) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-center relative">
                                                            <button @click="showAdjust = !showAdjust"
                                                                class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded border">
                                                                Điều chỉnh
                                                            </button>

                                                            <!-- Inline Popover for Adjustment -->
                                                            <div x-show="showAdjust" @click.away="showAdjust = false"
                                                                x-transition
                                                                class="absolute right-0 top-10 w-72 bg-white shadow-xl rounded-lg border p-4 z-20">
                                                                <h5 class="font-bold text-gray-800 mb-2 text-left">Báo
                                                                    hỏng / Điều chỉnh</h5>
                                                                <div class="space-y-3">
                                                                    <div>
                                                                        <label
                                                                            class="block text-xs font-medium text-gray-500 text-left mb-1">Số
                                                                            lượng hỏng/thất thoát thêm:</label>
                                                                        <input type="number" x-model="qty"
                                                                            min="1"
                                                                            class="w-full px-2 py-1 text-sm border rounded focus:ring-indigo-500 focus:border-indigo-500">
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="block text-xs font-medium text-gray-500 text-left mb-1">Ghi
                                                                            chú (Lý do):</label>
                                                                        <textarea x-model="note" rows="2"
                                                                            class="w-full px-2 py-1 text-sm border rounded focus:ring-indigo-500 focus:border-indigo-500"
                                                                            placeholder="VD: Rơi vỡ, hết hạn..."></textarea>
                                                                    </div>
                                                                    <div class="flex gap-2">
                                                                        <button @click="showAdjust = false"
                                                                            class="flex-1 px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Hủy</button>
                                                                        <button
                                                                            @click="$wire.updateBatchFailure('{{ $batch['id'] }}', '{{ $batch['detail_id'] }}', qty, note); showAdjust = false; qty = 0; note = ''"
                                                                            class="flex-1 px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                                                            Xác nhận
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- History Row expanded (optional) -->
                                                    @if (count($batch['history']) > 0)
                                                        <tr>
                                                            <td colspan="7" class="bg-gray-50 px-4 py-2 text-xs">
                                                                <details>
                                                                    <summary
                                                                        class="cursor-pointer text-indigo-600 font-medium hover:underline">
                                                                        Xem lịch sử cập nhật
                                                                        ({{ count($batch['history']) }})
                                                                    </summary>
                                                                    <div
                                                                        class="mt-2 pl-4 border-l-2 border-indigo-200 space-y-2">
                                                                        @foreach ($batch['history'] as $log)
                                                                            <div
                                                                                class="flex justify-between items-start">
                                                                                <div>
                                                                                    <span
                                                                                        class="font-semibold">{{ $log->nguoiCapNhat->ho_ten ?? 'Unknown' }}:</span>
                                                                                    <span
                                                                                        class="text-red-600 font-bold">{{ $log->so_luong_doi > 0 ? '+' : '' }}{{ $log->so_luong_doi }}</span>
                                                                                    <span
                                                                                        class="text-gray-500 ml-1">({{ $log->ghi_chu }})</span>
                                                                                </div>
                                                                                <div class="text-gray-400">
                                                                                    {{ \Carbon\Carbon::parse($log->created_at)->format('H:i d/m') }}
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </details>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-6 py-4 flex justify-between border-t">
                    @if ($editingShift)
                        <!-- Edit Mode Actions -->
                        <button wire:click="cancelEdit"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">
                            Hủy
                        </button>
                        <button wire:click="saveEdit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                            Lưu thay đổi
                        </button>
                    @else
                        <!-- View Mode Actions -->
                        <div class="flex gap-2">
                            <button wire:click="confirmDeleteShift"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                                🗑️ Xóa ca
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="startEdit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                                ✏️ Sửa
                            </button>
                            <button wire:click="closeDetail"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">
                                Đóng
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteConfirm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-900">Xác nhận xóa ca</h3>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-gray-600">Bạn có chắc muốn xóa ca làm việc này? Ca sẽ chuyển sang trạng thái "Từ
                        chối".</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lý do xóa (không bắt buộc)</label>
                        <textarea wire:model="deleteNote" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                            placeholder="Nhập lý do xóa ca..."></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button wire:click="cancelDelete"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">
                        Hủy
                    </button>
                    <button wire:click="executeDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        Xác nhận xóa
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Shift Modal -->
    @if ($showAddShiftModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b bg-indigo-50 flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Thêm ca làm việc</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $addShiftAgencyName ?? '' }}</p>
                    </div>
                    <button wire:click="closeAddShiftModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày làm</label>
                        <input type="date" wire:model="addShiftDate"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ca làm việc <span
                                class="text-red-500">*</span></label>
                        <div class="border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto space-y-2">
                            @if ($addShiftTemplates && count($addShiftTemplates) > 0)
                                @foreach ($addShiftTemplates as $template)
                                    <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                        <input type="checkbox" wire:model="selectedTemplates"
                                            value="{{ $template->id }}"
                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">{{ $template->name }}</span>
                                            <span class="text-gray-500 ml-2">
                                                ({{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }})
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500 text-center py-2">Chưa có ca làm việc nào được cấu hình
                                </p>
                            @endif
                        </div>
                        @error('selectedTemplates')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chọn nhân viên <span
                                class="text-red-500">*</span></label>
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <input type="text" wire:model.live="employeeSearch" @focus="open = true"
                                placeholder="Tìm theo tên hoặc mã nhân viên..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">

                            <!-- Dropdown -->
                            <div x-show="open"
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                @if ($filteredEmployees && count($filteredEmployees) > 0)
                                    @foreach ($filteredEmployees as $emp)
                                        <div wire:click="selectEmployee({{ $emp->id }})" @click="open = false"
                                            class="px-3 py-2 hover:bg-indigo-50 cursor-pointer {{ $addShiftEmployeeId == $emp->id ? 'bg-indigo-100' : '' }}">
                                            <div class="font-medium text-gray-900">{{ $emp->ho_ten }}</div>
                                            <div class="text-xs text-gray-500">{{ $emp->ma_nhan_vien }}</div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="px-3 py-2 text-sm text-gray-500 text-center">
                                        Không tìm thấy nhân viên
                                    </div>
                                @endif
                            </div>

                            <!-- Selected employee display -->
                            @if ($addShiftEmployeeId && $selectedEmployeeName)
                                <div
                                    class="mt-2 flex items-center gap-2 p-2 bg-indigo-50 rounded border border-indigo-200">
                                    <span class="text-sm text-indigo-900 flex-1">
                                        <strong>{{ $selectedEmployeeName }}</strong> ({{ $selectedEmployeeCode }})
                                    </span>
                                    <button wire:click="clearEmployee" type="button"
                                        class="text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        @error('addShiftEmployeeId')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button wire:click="closeAddShiftModal"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">
                        Hủy
                    </button>
                    <button wire:click="saveAddShift"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                        Tạo ca
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Template Manager Modal -->
    @if ($showTemplateManager)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="px-6 py-4 border-b bg-amber-50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Quản Lý Mẫu Ca Làm Việc</h3>
                    <button wire:click="closeTemplateManager" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <!-- Form Thêm/Sửa -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4">
                            {{ $editingTemplateId ? 'Sửa Mẫu Ca' : 'Thêm Mẫu Ca Mới' }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Điểm bán <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="templateAgencyId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="">-- Chọn điểm --</option>
                                    @foreach ($agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                                    @endforeach
                                </select>
                                @error('templateAgencyId')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên ca <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="templateName" placeholder="Ca 1, Ca Sáng..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('templateName')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giờ bắt đầu <span
                                        class="text-red-500">*</span></label>
                                <input type="time" wire:model="templateStartTime"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('templateStartTime')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giờ kết thúc <span
                                        class="text-red-500">*</span></label>
                                <input type="time" wire:model="templateEndTime"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @error('templateEndTime')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="templateIsActive"
                                    class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                                <span class="ml-2 text-sm text-gray-700">Kích hoạt</span>
                            </label>

                            <div class="flex-1"></div>

                            @if ($editingTemplateId)
                                <button wire:click="cancelEditTemplate"
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Hủy
                                </button>
                            @endif

                            <button wire:click="saveTemplate"
                                class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                                {{ $editingTemplateId ? 'Cập Nhật' : 'Thêm Mẫu Ca' }}
                            </button>
                        </div>
                    </div>

                    <!-- Table Mẫu Ca Theo Điểm -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach ($agencies as $index => $agency)
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider min-w-[220px] border-r-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100">
                                                <div class="font-bold">{{ $agency->ten_diem_ban }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @php
                                        $maxRows = $agencies->max(fn($a) => $a->shiftTemplates->count());
                                    @endphp
                                    @if ($maxRows > 0)
                                        @for ($i = 0; $i < $maxRows; $i++)
                                            <tr class="border-b border-gray-200">
                                                @foreach ($agencies as $agency)
                                                    <td
                                                        class="px-4 py-3 align-top border-r-2 border-gray-300 bg-white">
                                                        @if (isset($agency->shiftTemplates[$i]))
                                                            @php $template = $agency->shiftTemplates[$i]; @endphp
                                                            <div
                                                                class="p-3 rounded-lg border-2 {{ $template->is_active ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-50' }} shadow-sm">
                                                                <div class="font-semibold text-sm text-gray-900">
                                                                    {{ $template->name }}</div>
                                                                <div class="text-xs text-gray-600 mt-1">
                                                                    {{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}
                                                                </div>
                                                                <div class="flex gap-1 mt-2">
                                                                    <button
                                                                        wire:click="editTemplate({{ $template->id }})"
                                                                        class="flex-1 px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                                                                        ✏️ Sửa
                                                                    </button>
                                                                    <button
                                                                        wire:click="deleteTemplate({{ $template->id }})"
                                                                        wire:confirm="Xóa mẫu ca này?"
                                                                        class="flex-1 px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 font-medium">
                                                                        🗑️ Xóa
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endfor
                                    @else
                                        <tr>
                                            <td colspan="{{ count($agencies) }}"
                                                class="px-4 py-8 text-center text-gray-500">
                                                Chưa có mẫu ca nào. Hãy thêm mẫu ca mới ở form bên trên.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Image Modal --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-[100] hidden items-center justify-center p-4"
        onclick="closeImageModal()">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img id="modalImage" src="" class="max-w-full max-h-full object-contain"
            onclick="event.stopPropagation()">
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
        }

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>

    <!-- Toast Notification -->
    <div x-data="{
        show: false,
        message: '',
        count: 0,
        init() {
            // Livewire 3: Listen to component events
            Livewire.on('new-shift-detected', (event) => {
                console.log('🔔 Toast: New shift detected event received!', event);
                this.count = event.count;
                this.message = this.count > 1 ?
                    `Có ${this.count} ca làm việc mới được đăng ký!` :
                    'Có 1 ca làm việc mới được đăng ký!';
                this.show = true;
    
                console.log('✅ Toast displayed:', this.message);
    
                // Play notification sound (optional)
                // const audio = new Audio('/notification.mp3');
                // audio.play().catch(() => {});
    
                // Auto-hide after 5 seconds
                setTimeout(() => { this.show = false; }, 5000);
            });
        }
    }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" style="display: none;" class="fixed top-4 right-4 z-[70] max-w-sm">
        <div
            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-lg shadow-2xl border-2 border-green-300">
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center animate-pulse">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-base mb-1">🎉 Ca làm việc mới!</h4>
                    <p class="text-sm text-green-50" x-text="message"></p>
                    <p class="text-xs text-green-100 mt-1 opacity-75">Lịch đã được cập nhật tự động</p>
                </div>

                <!-- Close button -->
                <button @click="show = false" class="flex-shrink-0 text-white/70 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Progress bar -->
            <div class="mt-3 h-1 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white rounded-full animate-[shrink_5s_linear_forwards]"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes shrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
</div>
