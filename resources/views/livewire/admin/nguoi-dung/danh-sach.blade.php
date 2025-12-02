<div>
    <!-- Header with Actions -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Người dùng</h2>
        <div class="flex gap-3">
            <!-- Export Excel Button -->
            <button wire:click="exportExcel" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Xuất Excel
            </button>
            
            <!-- Add Button -->
            <a href="{{ route('admin.nguoidung.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm người dùng
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <!-- Row 1: Search (Limited Width) -->
        <div class="mb-4 max-w-lg">
            <x-search-bar placeholder="Tìm theo tên, email, số điện thoại..." />
        </div>
        
        <!-- Row 2: Date Range (Full Width) -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Lọc theo ngày tạo</label>
            <x-date-range-picker />
        </div>
        
        <!-- Row 3: Filters Grid (Max 4 columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filter 1: Vai trò -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                <select wire:model.live="vaiTro" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="admin">Quản trị viên</option>
                    <option value="nhan_vien">Nhân viên</option>
                </select>
            </div>
            
            <!-- Filter 2: Trạng thái -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="trangThai" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="hoat_dong">Hoạt động</option>
                    <option value="khoa">Bị khóa</option>
                </select>
            </div>
            
            <!-- Filter 3: Điểm bán -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Điểm bán</label>
                <select wire:model.live="diemBanId" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($diemBans as $db)
                        <option value="{{ $db->id }}">{{ $db->ten_diem_ban }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter 4: Reset Button (Small) -->
            <div class="flex items-end">
                <button wire:click="resetFilters" 
                        class="px-3 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1"
                        title="Xóa bộ lọc">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden relative">
        <!-- Loading Overlay -->
        <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600 font-medium">Đang tải...</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="ho_ten" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Họ tên</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="email" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số ĐT</th>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="vai_tro" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="trang_thai" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="luong_co_ban" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Lương CB</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->ho_ten) }}&background=4F46E5&color=fff" 
                                         alt="{{ $user->ho_ten }}" 
                                         class="w-10 h-10 rounded-full">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->ho_ten }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->so_dien_thoai ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->vai_tro === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Nhân viên' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->trang_thai === 'hoat_dong' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Bị khóa' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($user->luong_co_ban, 0, ',', '.') }} đ</div>
                                <div class="text-xs text-gray-500">{{ $user->loai_luong === 'theo_ngay' ? 'Theo ngày' : 'Theo giờ' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.nguoidung.show', $user->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Xem chi tiết">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.nguoidung.edit', $user->id) }}" 
                                       class="text-yellow-600 hover:text-yellow-900" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="delete({{ $user->id }})" 
                                            wire:confirm="Bạn có chắc muốn xóa người dùng này?"
                                            class="text-red-600 hover:text-red-900" title="Xóa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-gray-500">Không tìm thấy người dùng nào</p>
                                @if($search || $vaiTro || $trangThai || $dateFrom || $dateTo)
                                    <button wire:click="resetFilters" class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                        Xóa bộ lọc để xem tất cả
                                    </button>
                                @else
                                    <a href="{{ route('admin.nguoidung.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                                        Thêm người dùng mới
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <x-pagination-controls :paginator="$users" />
        </div>
    </div>
</div>
