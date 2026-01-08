<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Nguyên liệu</h2>
        <div class="flex gap-3">
            <button wire:click="exportExcel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Xuất Excel
            </button>
            <a href="{{ route('admin.ingredients.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm nguyên liệu
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="mb-4 max-w-lg">
            <x-search-bar placeholder="Tìm theo tên, mã nguyên liệu..." />
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bộ lọc đặc biệt</label>
                <label class="flex items-center h-10 px-3 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" wire:model.live="lowStock" class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500">
                    <span class="ml-2 text-sm text-gray-700">🔥 Tồn kho thấp</span>
                </label>
            </div>
            
            <div></div>
            <div></div>
            
            <div class="flex items-end">
                <x-reset-button wire:click="$set('lowStock', false)" />
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden relative">
        <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
            <svg class="w-6 h-6 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        
        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('message') }}</div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">{{ session('error') }}</div>
        @endif
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <x-sort-icon field="ma_nguyen_lieu" :currentField="$sortField" :direction="$sortDirection">
                            <span class="text-xs font-medium text-gray-500 uppercase">Mã NL</span>
                        </x-sort-icon>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <x-sort-icon field="ten_nguyen_lieu" :currentField="$sortField" :direction="$sortDirection">
                            <span class="text-xs font-medium text-gray-500 uppercase">Tên nguyên liệu</span>
                        </x-sort-icon>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đơn vị</th>
                    <th class="px-6 py-3 text-left">
                        <x-sort-icon field="ton_kho_hien_tai" :currentField="$sortField" :direction="$sortDirection">
                            <span class="text-xs font-medium text-gray-500 uppercase">Tồn kho</span>
                        </x-sort-icon>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <x-sort-icon field="gia_nhap" :currentField="$sortField" :direction="$sortDirection">
                            <span class="text-xs font-medium text-gray-500 uppercase">Giá nhập</span>
                        </x-sort-icon>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tồn tối thiểu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($ingredients as $ingredient)
                    @php
                        $isLowStock = $ingredient->ton_kho_hien_tai < $ingredient->ton_kho_toi_thieu;
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $isLowStock ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $ingredient->ma_nguyen_lieu }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ingredient->ten_nguyen_lieu }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ingredient->don_vi_tinh }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isLowStock ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                            {{ number_format($ingredient->ton_kho_hien_tai, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-700">
                            {{ number_format($ingredient->gia_nhap, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($ingredient->ton_kho_toi_thieu, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isLowStock)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    ⚠️ Tồn kho thấp
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ Bình thường
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.ingredients.edit', $ingredient->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</a>
                            <button wire:click="delete({{ $ingredient->id }})" wire:confirm="Xóa nguyên liệu?" class="text-red-600 hover:text-red-900">Xóa</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            Không có nguyên liệu nào
                            @if($lowStock)
                                <button wire:click="$set('lowStock', false)" class="block mt-2 text-blue-600 hover:text-blue-800 mx-auto">
                                    Xem tất cả nguyên liệu
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            <x-pagination-controls :paginator="$ingredients" />
        </div>
    </div>
</div>
