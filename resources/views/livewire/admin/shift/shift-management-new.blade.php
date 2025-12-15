<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Quản lý Ca Làm Việc</h2>
            <p class="text-sm text-gray-500 mt-1">Đăng ký và quản lý ca làm việc theo địa điểm</p>
        </div>
        
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin())
            <button wire:click="openTemplateManagement" 
                    class="px-3 py-1.5 text-sm font-semibold bg-white text-gray-900 border-2 border-gray-900 rounded-lg hover:bg-gray-900 hover:text-white transition-all shadow-md hover:shadow-lg flex items-center whitespace-nowrap">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Mẫu ca
            </button>
            @endif
            
            @if(!auth()->user()->isAdmin())
            <button wire:click="openRegistrationModal" 
                    class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Đăng ký ca
            </button>
            @endif
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex space-x-2 p-2 min-w-max">
                @if($canViewAllTab)
                    <button wire:click="switchTab('all')" 
                            class="px-4 py-2 rounded-lg font-medium transition-all {{ $selectedTab === 'all' ? 'bg-amber-500 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Tất Cả
                    </button>
                @endif
                
                @foreach($availableLocations as $location)
                    <button wire:click="switchTab({{ $location->id }})" 
                            class="px-4 py-2 rounded-lg font-medium transition-all {{ $selectedTab == $location->id ? 'bg-amber-500 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        {{ $location->ten_diem_ban }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Quick Stats for Selected Location -->
        @if($selectedTab !== 'all' && $stats)
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_shifts'] }}</p>
                        <p class="text-sm text-gray-500">Tổng ca</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $stats['active_shifts'] }}</p>
                        <p class="text-sm text-gray-500">Đang làm</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['employees'] }}</p>
                        <p class="text-sm text-gray-500">Nhân viên</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="space-y-3">
            <!-- Row 1: Search + Dates -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
                           placeholder="Nhân viên, điểm bán...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" wire:model.live="dateFrom" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" wire:model.live="dateTo" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                </div>
            </div>

            <!-- Row 2: Status Filter -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select wire:model.live="statusFilter" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 sm:text-sm">
                        <option value="">Tất cả</option>
                        <option value="pending">⏳ Chờ duyệt</option>
                        <option value="approved">✅ Đã duyệt</option>
                        <option value="rejected">❌ Từ chối</option>
                        <option value="completed">🔒 Hoàn thành</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Shifts List -->
    @if($shifts->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm bán</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày & Giờ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mẫu ca</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($shifts as $shift)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $shift->agency->ten_diem_ban ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $shift->nguoiDung->ho_ten ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                <div>{{ \Carbon\Carbon::parse($shift->ngay_lam)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($shift->gio_ket_thuc)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                @if($shift->shiftTemplate)
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ $shift->shiftTemplate->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Tùy chỉnh</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($shift->trang_thai === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ Chờ duyệt
                                    </span>
                                @elseif($shift->trang_thai === 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Đã duyệt
                                    </span>
                                @elseif($shift->trang_thai === 'rejected')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        ❌ Từ chối
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        🔒 Hoàn thành
                                    </span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdmin())
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                <!-- Admin actions here -->
                                <button class="text-amber-600 hover:text-amber-800 font-medium">Chi tiết</button>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($shifts->hasPages())
                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500 border border-gray-200">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Không tìm thấy ca làm việc nào</p>
        </div>
    @endif


    <!-- Registration Modal - Weekly Calendar -->
    @if($showRegistrationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden" wire:click.stop>
                <!-- Modal Header -->
                <div class="bg-amber-500 rounded-t-xl px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">📅 Đăng ký ca làm việc</h3>
                    <button wire:click="$set('showRegistrationModal', false)" class="text-white hover:bg-white/20 rounded-lg p-1.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Week Navigation -->
                <div class="px-6 py-3 bg-gray-50 border-b flex justify-between items-center">
                    <button type="button" wire:click="previousWeek" class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-100">
                        ← Tuần trước
                    </button>
                    <span class="text-sm font-medium text-gray-700">
                        Tuần {{ \Carbon\Carbon::parse($weekStartDate)->weekOfYear }}, {{ \Carbon\Carbon::parse($weekStartDate)->year }}
                    </span>
                    <button type="button" wire:click="nextWeek" class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-100">
                        Tuần sau →
                    </button>
                </div>

                <!-- Modal Content - Calendar Grid -->
                <div class="overflow-y-auto" style="max-height: calc(90vh - 200px);">
                    <form wire:submit.prevent="registerShift" class="p-6">
                        <div class="grid grid-cols-7 gap-2 mb-6">
                            @foreach($weekDays as $index => $day)
                                <div class="border rounded-lg p-2 {{ in_array($index, $selectedDays) ? 'bg-amber-50 border-amber-500 ring-2 ring-amber-200' : 'bg-white border-gray-200' }}">
                                    <!-- Day Header + Checkbox -->
                                    <label class="flex items-center justify-between cursor-pointer mb-2">
                                        <div>
                                            <div class="text-xs font-semibold text-gray-600">{{ $day['dayShort'] }}</div>
                                            <div class="text-sm font-bold">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</div>
                                        </div>
                                        <input type="checkbox" 
                                               wire:click="toggleDay({{ $index }})"
                                               {{ in_array($index, $selectedDays) ? 'checked' : '' }}
                                               class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                                    </label>

                                    <!-- Agency Selection -->
                                    <div class="mt-2">
                                        <select wire:model="weekDays.{{ $index }}.agencyId" 
                                                class="block w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-amber-500 focus:border-amber-500"
                                                {{ !in_array($index, $selectedDays) ? 'disabled' : '' }}>
                                            <option value="">Điểm bán...</option>
                                            @foreach($availableLocations as $location)
                                                <option value="{{ $location->id }}">{{ $location->ten_diem_ban }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Shift Template Selection -->
                                    <div class="mt-2">
                                        <select wire:model="weekDays.{{ $index }}.templateId" 
                                                class="block w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-amber-500 focus:border-amber-500"
                                                {{ !in_array($index, $selectedDays) || empty($weekDays[$index]['agencyId']) ? 'disabled' : '' }}>
                                            <option value="">Chọn ca...</option>
                                            @if(!empty($weekDays[$index]['agencyId']))
                                                @php
                                                    $templates = \App\Models\ShiftTemplate::where('diem_ban_id', $weekDays[$index]['agencyId'])
                                                        ->where('is_active', true)
                                                        ->get();
                                                @endphp
                                                @foreach($templates as $template)
                                                    <option value="{{ $template->id }}">
                                                        {{ $template->name }} ({{ $template->start_time->format('H:i') }}-{{ $template->end_time->format('H:i') }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button type="button" wire:click="$set('showRegistrationModal', false)"
                                    class="px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                                Hủy
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-semibold">
                                ✓ Đăng ký {{ count($selectedDays) > 0 ? count($selectedDays) . ' ca' : '' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    <!-- Template Management Modal -->
    @if($showTemplateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[85vh] overflow-hidden" wire:click.stop>
                <!-- Modal Header -->
                <div class="bg-gray-900 rounded-t-xl px-4 py-3 flex justify-between items-center">
                    <h3 class="text-base font-bold text-white">Quản lý Mẫu Ca</h3>
                    <button wire:click="closeTemplateModal" class="text-white hover:bg-white/20 rounded-lg p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="overflow-y-auto" style="max-height: calc(85vh - 120px);">
                    <div class="p-4">
                        <!-- Create/Edit Form -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-4">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3">
                                {{ $editingTemplateId ? 'Chỉnh sửa mẫu ca' : 'Tạo mẫu ca mới' }}
                            </h4>
                            
                            <form wire:submit.prevent="saveTemplate" class="space-y-3">
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Tên ca -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tên ca <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="templateForm.name" 
                                               class="block w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-gray-900 focus:border-gray-900"
                                               placeholder="Ca sáng, Ca 1...">
                                        @error('templateForm.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Điểm bán -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Điểm bán <span class="text-red-500">*</span></label>
                                        <select wire:model="templateForm.diem_ban_id" 
                                                class="block w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-gray-900 focus:border-gray-900">
                                            <option value="">Chọn...</option>
                                            @foreach($availableLocations as $location)
                                                <option value="{{ $location->id }}">{{ $location->ten_diem_ban }}</option>
                                            @endforeach
                                        </select>
                                        @error('templateForm.diem_ban_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Giờ bắt đầu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Giờ bắt đầu <span class="text-red-500">*</span></label>
                                        <input type="time" wire:model="templateForm.start_time" 
                                               class="block w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-gray-900 focus:border-gray-900">
                                        @error('templateForm.start_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Giờ kết thúc -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Giờ kết thúc <span class="text-red-500">*</span></label>
                                        <input type="time" wire:model="templateForm.end_time" 
                                               class="block w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-gray-900 focus:border-gray-900">
                                        @error('templateForm.end_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Checkboxes -->
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="templateForm.is_default" 
                                               class="rounded text-gray-900 focus:ring-gray-900">
                                        <span class="ml-1.5 text-xs text-gray-700">Mặc định</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="templateForm.is_active" 
                                               class="rounded text-gray-900 focus:ring-gray-900">
                                        <span class="ml-1.5 text-xs text-gray-700">Kích hoạt</span>
                                    </label>
                                </div>

                                <!-- Buttons -->
                                <div class="flex justify-end gap-2 pt-2">
                                    @if($editingTemplateId)
                                        <button type="button" wire:click="createTemplate" 
                                                class="px-4 py-2 text-sm font-medium border-2 border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                            Hủy
                                        </button>
                                    @endif
                                    <button type="submit" 
                                            class="px-4 py-2 text-sm font-semibold bg-gray-900 text-white rounded hover:bg-gray-800 shadow-sm">
                                        {{ $editingTemplateId ? '💾 Cập nhật' : '➕ Tạo mới' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Templates List -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-2">Danh sách mẫu ca</h4>
                            
                            @if($allTemplates->count() > 0)
                                <div class="bg-white border border-gray-200 rounded overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tên ca</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Điểm bán</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Giờ</th>
                                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($allTemplates as $template)
                                                <tr class="hover:bg-gray-50 {{ $editingTemplateId == $template->id ? 'bg-blue-50' : '' }}">
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <span class="text-sm font-medium text-gray-900">{{ $template->name }}</span>
                                                            @if($template->is_default)
                                                                <span class="ml-2 px-1.5 py-0.5 text-xs rounded bg-amber-100 text-amber-800">Mặc định</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                                        {{ $template->agency->ten_diem_ban ?? '-' }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600">
                                                        {{ $template->start_time->format('H:i') }} - {{ $template->end_time->format('H:i') }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-center">
                                                        @if($template->is_active)
                                                            <span class="px-2 py-0.5 text-xs rounded bg-green-100 text-green-800">Active</span>
                                                        @else
                                                            <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-600">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap text-right space-x-1">
                                                        <button wire:click="editTemplate({{ $template->id }})" 
                                                                class="px-2 py-1 text-xs font-medium bg-white border border-gray-900 text-gray-900 rounded hover:bg-gray-900 hover:text-white">
                                                            Sửa
                                                        </button>
                                                        <button wire:click="deleteTemplate({{ $template->id }})" 
                                                                onclick="return confirm('Bạn có chắc muốn xóa mẫu ca này?')"
                                                                class="px-2 py-1 text-xs font-medium bg-white border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white">
                                                            Xóa
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-6 text-sm text-gray-500 bg-gray-50 rounded">
                                    <p>Chưa có mẫu ca nào. Tạo mẫu ca đầu tiên!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</div>
