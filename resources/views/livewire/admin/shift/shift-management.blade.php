<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Quản lý Ca Làm Việc</h2>
            <p class="text-sm text-gray-500 mt-1">Giám sát và quản lý lịch làm việc của nhân viên</p>
        </div>
        <div class="flex gap-3">
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
        </div>
    </div>

    @if(session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- TABS: Workshop vs Stores -->
    <div class="mb-6 flex space-x-1 p-1 bg-gray-100 rounded-lg w-fit">
        <button wire:click="setTab('stores')" 
                class="px-6 py-2 rounded-md text-sm font-medium transition-colors {{ $activeTab === 'stores' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            🏪 Các điểm bán
        </button>
        <button wire:click="setTab('workshop')" 
                class="px-6 py-2 rounded-md text-sm font-medium transition-colors {{ $activeTab === 'workshop' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            🏭 Xưởng sản xuất
        </button>
    </div>

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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" wire:model.live="dateFrom" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" wire:model.live="dateTo" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="md:col-span-2 flex items-end">
                    <div class="flex gap-2 w-full bg-gray-100 p-1 rounded-lg">
                        <button wire:click="toggleViewMode('monitoring')" class="flex-1 p-1.5 rounded {{ $viewMode === 'monitoring' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:bg-gray-200' }}" title="Chế độ Giám sát">
                            📊 Giám sát
                        </button>
                        <button wire:click="toggleViewMode('list')" class="flex-1 p-1.5 rounded {{ $viewMode === 'list' ? 'bg-indigo-600 text-white shadow' : 'text-gray-500 hover:bg-gray-200' }}" title="Chế độ Danh sách">
                            📋 List
                        </button>
                    </div>
                </div>
            </div>

            <!-- Row 2: Advanced Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Only show Agency Filter if Monitoring? Or always? -->
                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-1">Lọc Điểm bán</label>
                     <select wire:model.live="agencyFilter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nhân viên</label>
                    <select wire:model.live="employeeFilter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả nhân viên</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->ho_ten }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select wire:model.live="statusFilter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả trạng thái</option>
                        <option value="approved">🔵 Sắp tới (Approved)</option>
                        <option value="completed">⚪ Đã kết thúc (Completed)</option>
                        <option value="pending">⏳ Chờ duyệt (Pending)</option>
                        <option value="rejected">🔴 Từ chối (Rejected)</option>
                    </select>
                </div>
            </div>
             <!-- Row 3: Bulk Actions (Only in List Mode) -->
            @if($viewMode === 'list' && count($selectedShifts) > 0)
            <div class="flex items-center gap-3 p-3 bg-indigo-50 border border-indigo-100 rounded-lg animate-fade-in mt-2">
                <span class="text-sm font-medium text-indigo-700">Đã chọn {{ count($selectedShifts) }} ca</span>
                <div class="h-4 w-px bg-indigo-200"></div>
                <button wire:click="bulkDelete" wire:confirm="Bạn có chắc muốn xóa các ca đã chọn?" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa
                </button>
                <button wire:click="bulkUpdateStatus('completed')" wire:confirm="Đánh dấu các ca này là đã kết thúc?" class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                    📍 Đánh dấu kết thúc
                </button>
                <button wire:click="bulkUpdateStatus('approved')" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    ✅ Duyệt/Sắp tới
                </button>
            </div>
            @endif
        </div>
    </div>

    @if($viewMode === 'monitoring')
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
                    'bg-yellow-100 text-yellow-900 border-yellow-200'
                ];
            @endphp
            @foreach($groupedAgencies as $agency)
                @php
                    $themeClass = $agencyColors[$loop->index % count($agencyColors)];
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            @if($activeTab === 'workshop') 🏭 @else 🏪 @endif
                            {{ $agency->ten_diem_ban }}
                        </h3>
                        <span class="text-xs font-medium bg-white px-2 py-1 rounded border">
                            {{ count($agency->shiftSchedules) }} ca đăng ký
                        </span>
                    </div>
                    
                    <div class="p-4 overflow-x-auto">
                        @if($agency->shiftTemplates->isEmpty())
                            <p class="text-gray-400 text-sm text-center italic py-2">Chưa cấu hình ca làm việc (Shift Templates)</p>
                        @elseif($agency->shiftSchedules->isEmpty())
                            <p class="text-gray-400 text-sm text-center italic py-2">Chưa có lịch đăng ký trong giai đoạn này</p>
                        @else
                            {{-- Calendar Grid --}}
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 border-r min-w-[150px]">
                                            Ca làm việc
                                        </th>
                                        {{-- Generate Date Headers --}}
                                        @php
                                            $startDate = \Carbon\Carbon::parse($dateFrom);
                                            $endDate = \Carbon\Carbon::parse($dateTo);
                                            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
                                        @endphp
                                        @foreach($period as $date)
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px] border-l">
                                                {{ $date->format('d/m') }} <br>
                                                {{ $date->locale('vi')->dayName }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($agency->shiftTemplates as $template)
                                        <tr>
                                            <td class="px-3 py-2 text-sm font-medium text-gray-900 sticky left-0 bg-white z-10 border-r">
                                                {{ $template->name }} <br>
                                                <span class="text-xs text-gray-500 font-normal">
                                                    {{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}
                                                </span>
                                            </td>
                                            
                                            @foreach($period as $date)
                                                @php
                                                    $currentDate = $date->format('Y-m-d');
                                                    // Filter shifts for this cell: Match Date AND Match Template ID
                                                    $cellShifts = $agency->shiftSchedules->filter(function($s) use ($currentDate, $template) {
                                                        return \Carbon\Carbon::parse($s->ngay_lam)->isSameDay($currentDate) 
                                                            && $s->shift_template_id == $template->id;
                                                    });
                                                @endphp
                                                <td class="px-2 py-2 text-center border-l align-top text-xs h-16">
                                                    @if($cellShifts->isNotEmpty())
                                                        <div class="flex flex-col gap-1">
                                                            @foreach($cellShifts as $shift)
                                                                <div wire:click="openDetail({{ $shift->id }})" 
                                                                     class="cursor-pointer p-1.5 rounded border shadow-sm transition-opacity text-left {{ $themeClass }}
                                                                     {{ $shift->trang_thai == 'pending' ? 'opacity-60 border-dashed' : '' }}
                                                                     {{ $shift->trang_thai == 'rejected' ? 'line-through bg-gray-100 text-gray-400 border-gray-200' : '' }}
                                                                     ">
                                                                    <div class="truncate text-sm">{{ $shift->user->ho_ten ?? 'Unknown' }}</div>
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
    @else
        <!-- List View (Legacy) -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                {{-- Existing Table Code... --}}
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 w-8 text-center">
                                <input type="checkbox" wire:model.live="selectAll" wire:click="toggleSelectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giờ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm bán</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Chốt ca</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($shifts as $shift)
                            <tr class="hover:bg-gray-50 group">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" wire:model.live="selectedShifts" value="{{ $shift->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($shift->ngay_lam)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }}</span>
                                    - {{ \Carbon\Carbon::parse($shift->gio_ket_thuc)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-3">
                                            {{ substr($shift->user->ho_ten ?? 'Unknown', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $shift->user->ho_ten ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $shift->user->ma_nhan_vien ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $shift->agency->ten_diem_ban ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ match($shift->trang_thai) {
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-gray-100 text-gray-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        } }}">
                                        {{ match($shift->trang_thai) {
                                            'approved' => '🔵 Sắp tới',
                                            'completed' => '⚪ Đã kết thúc',
                                            'pending' => '⏳ Chờ duyệt',
                                            'rejected' => '🔴 Từ chối',
                                            default => $shift->trang_thai
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if($shift->shiftClosing)
                                        <span class="text-green-600 text-lg" title="Đã chốt ca">✓</span>
                                    @else
                                        <span class="text-gray-300 text-lg">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="openEditModal({{ $shift->id }})" class="text-blue-600 hover:text-blue-900" title="Sửa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button wire:click="openDetail({{ $shift->id }})" class="text-indigo-600 hover:text-indigo-900" title="Chi tiết">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button wire:click="deleteShift({{ $shift->id }})" wire:confirm="Xóa ca làm việc này?" class="text-red-600 hover:text-red-900" title="Xóa">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-lg font-medium">Không tìm thấy ca làm việc nào</p>
                                        <p class="text-sm">Thử thay đổi bộ lọc hoặc thêm ca làm việc mới</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($shifts->hasPages())
                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Edit Modal (Shared) -->
    @if($showEditModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
            <!-- Modal Content (Use existing) -->
             <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Sửa ca làm việc</h3>
                <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày làm</label>
                    <input type="date" wire:model="editDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giờ bắt đầu</label>
                        <input type="time" wire:model="editStartTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giờ kết thúc</label>
                        <input type="time" wire:model="editEndTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
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
                    <button wire:click="closeEditModal" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Hủy</button>
                    <button wire:click="saveEdit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Modal (Shared) -->
    @if($showDetailModal && $selectedShift)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
             <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-900">Chi tiết ca làm việc</h3>
                <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nhân viên</p>
                        <p class="font-semibold text-lg">{{ $selectedShift->user->ho_ten ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $selectedShift->user->ma_nhan_vien ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Điểm bán</p>
                        <p class="font-semibold text-lg">{{ $selectedShift->agency->ten_diem_ban ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Thời gian</p>
                        <p class="font-medium text-lg">
                            {{ \Carbon\Carbon::parse($selectedShift->gio_bat_dau)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($selectedShift->gio_ket_thuc)->format('H:i') }}
                        </p>
                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($selectedShift->ngay_lam)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Trạng thái</p>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ match($selectedShift->trang_thai) {
                                'approved' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-gray-100 text-gray-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            } }}">
                            {{ match($selectedShift->trang_thai) {
                                'approved' => '🔵 Sắp tới',
                                'completed' => '⚪ Đã kết thúc',
                                'pending' => '⏳ Chờ duyệt',
                                'rejected' => '🔴 Từ chối',
                                default => $selectedShift->trang_thai
                            } }}
                        </span>
                    </div>
                </div>

                <!-- Shift Closing Info -->
                @if($selectedShift->shiftClosing)
                <div class="border-t pt-6">
                    <h4 class="font-bold text-gray-900 mb-4">Thông tin chốt ca</h4>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tổng doanh thu (lý thuyết):</span>
                            <span class="font-medium">{{ number_format($selectedShift->shiftClosing->tong_doanh_thu_ly_thuyet ?? 0) }}đ</span>
                        </div>
                         <div class="flex justify-between">
                            <span class="text-gray-600">Tiền mặt thực tế:</span>
                            <span class="font-medium">{{ number_format($selectedShift->shiftClosing->tien_mat_thuc_te ?? 0) }}đ</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="text-gray-600">Chênh lệch:</span>
                            <span class="font-bold {{ ($selectedShift->shiftClosing->chenh_lech ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($selectedShift->shiftClosing->chenh_lech ?? 0) }}đ
                            </span>
                        </div>
                    </div>
                </div>
                @else
                <div class="border-t pt-6 text-center text-gray-500 italic">
                    Chưa có thông tin chốt ca
                </div>
                @endif
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button wire:click="closeDetail" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium">
                    Đóng
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
