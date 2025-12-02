<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Sản phẩm</h2>
        <div class="flex gap-3">
            <!-- Export Excel -->
            <button wire:click="exportExcel" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Xuất Excel
            </button>
            
            <a href="{{ route('admin.products.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm sản phẩm
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <!-- Row 1: Search (Limited Width) -->
        <div class="mb-4 max-w-lg">
            <x-search-bar placeholder="Tìm theo tên, mã sản phẩm..." />
        </div>
        
        <!-- Row 2: Filters Grid (Max 4 columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filter 1: Danh mục -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select wire:model.live="categoryId" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->ten_danh_muc }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter 2: Trạng thái -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="trangThai" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="con_hang">Còn hàng</option>
                    <option value="het_hang">Hết hàng</option>
                    <option value="ngung_ban">Ngừng bán</option>
                </select>
            </div>
            
            <!-- Filter 3: Empty -->
            <div></div>
            
            <!-- Filter 4: Reset Button (Compact) -->
            <div class="flex items-end">
                <x-reset-button wire:click="resetFilters" />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden relative">
        <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600 font-medium">Đang tải...</span>
            </div>
        </div>
        
        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">{{ session('message') }}</div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="ma_san_pham" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase">Mã SP</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="ten_san_pham" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase">Tên SP</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Danh mục</th>
                        <th class="px-6 py-3 text-left">
                            <x-sort-icon field="gia_ban" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase">Giá bán</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $product->ma_san_pham }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->ten_san_pham }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category?->ten_danh_muc ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->gia_ban, 0, ',', '.') }} đ</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $product->trang_thai === 'con_hang' ? 'bg-green-100 text-green-800' : ($product->trang_thai === 'het_hang' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $product->trang_thai === 'con_hang' ? 'Còn hàng' : ($product->trang_thai === 'het_hang' ? 'Hết hàng' : 'Ngừng bán') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</a>
                                <button wire:click="delete({{ $product->id }})" wire:confirm="Bạn có chắc muốn xóa?" class="text-red-600 hover:text-red-900">Xóa</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Không tìm thấy sản phẩm nào
                                @if($search || $categoryId || $trangThai)
                                    <button wire:click="resetFilters" class="block mt-2 text-blue-600 hover:text-blue-800 mx-auto">Xóa bộ lọc</button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            <x-pagination-controls :paginator="$products" />
        </div>
    </div>
</div>
