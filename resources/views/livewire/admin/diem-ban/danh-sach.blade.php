<div>
    <!-- Header with Actions -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Điểm bán</h2>
        <div class="flex gap-3">
            <!-- Export Excel -->
            <button wire:click="exportExcel" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Xuất Excel
            </button>
            
            <!-- Add Button -->
            <a href="{{ route('admin.diemban.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm điểm bán
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <!-- Search (Limited Width) -->
        <div class="mb-4 max-w-lg">
            <x-search-bar placeholder="Tìm theo tên, mã, địa chỉ, SĐT..." />
        </div>
        
        <!-- Advanced Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="trangThai" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="hoat_dong">Hoạt động</option>
                    <option value="dong_cua">Đóng cửa</option>
                </select>
            </div>
            
            <!-- Reset Button (Compact) -->
            <div class="flex items-end">
                <x-reset-button wire:click="resetFilters" />
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
                            <x-sort-icon field="ma_diem_ban" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mã ĐB</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="ten_diem_ban" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tên điểm bán</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa chỉ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số ĐT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($agencies as $agency)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                {{ $agency->ma_diem_ban }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $agency->ten_diem_ban }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                {{ $agency->dia_chi }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $agency->so_dien_thoai ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $agency->trang_thai === 'hoat_dong' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $agency->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Đóng cửa' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.diemban.show', $agency->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Xem chi tiết">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.diemban.edit', $agency->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="delete({{ $agency->id }})" 
                                            wire:confirm="Bạn có chắc muốn xóa điểm bán này?"
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="mt-4 text-gray-500">Không tìm thấy điểm bán nào</p>
                                @if($search || $trangThai)
                                    <button wire:click="resetFilters" class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                        Xóa bộ lọc để xem tất cả
                                    </button>
                                @else
                                    <a href="{{ route('admin.diemban.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                                        Thêm điểm bán mới
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
            <x-pagination-controls :paginator="$agencies" />
        </div>
    </div>
</div>
