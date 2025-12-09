<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Nhà cung cấp</h2>
        <div class="flex gap-3">
            <button wire:click="exportExcel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Xuất Excel
            </button>
            <a href="{{ route('admin.suppliers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm NCC
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <x-search-bar placeholder="Tìm theo tên, mã, người đại diện, SĐT, Zalo..." />
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden relative">
        <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
            <svg class="w-6 h-6 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        
        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">{{ session('message') }}</div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left w-24">
                            <x-sort-icon field="ma_ncc" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase">Mã NCC</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-3 py-3 text-left">
                            <x-sort-icon field="ten_ncc" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase">Tên NCC</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Người đại diện</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">SĐT Zalo</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung thỏa thuận</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase w-24">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($suppliers as $supplier)
                        <tr class="hover:bg-gray-50">
                            <!-- Mã NCC -->
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-mono text-indigo-600">
                                {{ $supplier->ma_ncc }}
                            </td>
                            
                            <!-- Tên NCC -->
                            <td class="px-3 py-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $supplier->ten_ncc }}</div>
                            </td>
                            
                            <!-- Người đại diện -->
                            <td class="px-3 py-3 text-sm text-gray-600">
                                {{ $supplier->nguoi_dai_dien ?? '-' }}
                            </td>
                            
                            <!-- SĐT Zalo -->
                            <td class="px-3 py-3 text-sm text-gray-600">
                                @if($supplier->so_dien_thoai_zalo)
                                    <div class="flex items-center gap-1">
                                        <span>💬</span>
                                        <span>{{ $supplier->so_dien_thoai_zalo }}</span>
                                    </div>
                                @elseif($supplier->so_dien_thoai)
                                    <div class="flex items-center gap-1">
                                        <span>📞</span>
                                        <span>{{ $supplier->so_dien_thoai }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Sản phẩm -->
                            <td class="px-3 py-3 text-sm text-gray-600">
                                @if($supplier->san_pham_cung_cap)
                                    <div class="max-w-xs truncate" title="{{ $supplier->san_pham_cung_cap }}">
                                        {{ $supplier->san_pham_cung_cap }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Nội dung thỏa thuận -->
                            <td class="px-3 py-3 text-sm text-gray-600">
                                @if($supplier->noi_dung_thoa_thuan)
                                    <span title="{{ $supplier->noi_dung_thoa_thuan }}">
                                        {{ Str::limit($supplier->noi_dung_thoa_thuan, 25) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Ghi chú -->
                            <td class="px-3 py-3 text-sm text-gray-500">
                                @if($supplier->ghi_chu)
                                    <span title="{{ $supplier->ghi_chu }}">
                                        {{ Str::limit($supplier->ghi_chu, 25) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Thao tác -->
                            <td class="px-3 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</a>
                                <button wire:click="delete({{ $supplier->id }})" wire:confirm="Xóa NCC này?" class="text-red-600 hover:text-red-900">Xóa</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">Không có nhà cung cấp nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            <x-pagination-controls :paginator="$suppliers" />
        </div>
    </div>
</div>
