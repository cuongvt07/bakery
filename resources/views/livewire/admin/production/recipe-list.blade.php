<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Quản lý Công thức Sản xuất</h2>
        <a href="{{ route('admin.recipes.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm Công thức
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="mb-4 max-w-lg">
            <input type="text" wire:model.live="search" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Tìm theo mã, tên công thức...">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục SP</label>
                <select wire:model.live="categoryId" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->ten_danh_muc }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="trangThai" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="hoat_dong">Hoạt động</option>
                    <option value="ngung_su_dung">Ngừng sử dụng</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                    Xóa bộ lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">{{ session('error') }}</div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã CT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Công thức</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SL/Mẻ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi phí</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($recipes as $recipe)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $recipe->ma_cong_thuc }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $recipe->ten_cong_thuc }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $recipe->product->ten_san_pham ?? '---' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $recipe->so_luong_san_xuat }} {{ $recipe->don_vi_san_xuat }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-gray-900 font-semibold">{{ number_format($recipe->chi_phi_uoc_tinh, 0, ',', '.') }} đ</div>
                                <div class="text-xs text-gray-500">{{ number_format($recipe->cost_per_unit, 0, ',', '.') }} đ/{{ $recipe->don_vi_san_xuat }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $recipe->trang_thai === 'hoat_dong' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $recipe->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Ngừng SD' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.recipes.edit', $recipe->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</a>
                                
                                @if($recipe->trang_thai === 'hoat_dong')
                                    <button wire:click="toggleStatus({{ $recipe->id }})" class="text-gray-500 hover:text-gray-700 mr-2" title="Ẩn khỏi danh sách">
                                        Ẩn
                                    </button>
                                @else
                                    <button wire:click="toggleStatus({{ $recipe->id }})" class="text-green-600 hover:text-green-900 mr-2" title="Hiện lại">
                                        Hiện
                                    </button>
                                    <button wire:click="delete({{ $recipe->id }})" wire:confirm="Xóa hoàn toàn công thức này? Hành động không thể hoàn tác." class="text-red-600 hover:text-red-900">
                                        Xóa
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Chưa có công thức nào
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
            {{ $recipes->links() }}
        </div>
    </div>
</div>
