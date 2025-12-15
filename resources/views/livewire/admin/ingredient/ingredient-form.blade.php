<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $ingredient ? 'Cập nhật Nguyên liệu' : 'Thêm mới Nguyên liệu' }}</h1>
        <a href="{{ route('admin.ingredients.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã nguyên liệu</label>
                    @if($ingredient)
                        <input type="text" value="{{ $ingredient->ma_nguyen_lieu }}" disabled 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Mã không thể thay đổi</p>
                    @else
                        <input type="text" value="Tự động tạo (NL####)" disabled 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Mã sẽ được tự động tạo khi lưu</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên nguyên liệu *</label>
                    <input type="text" wire:model="ten_nguyen_lieu" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ten_nguyen_lieu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đơn vị tính *</label>
                    <input type="text" wire:model.live="don_vi_tinh" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="kg, lít, gói">
                    @error('don_vi_tinh') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng nhập *</label>
                    <input type="number" step="0.01" wire:model.live="ton_kho_hien_tai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="VD: 10">
                    @error('ton_kho_hien_tai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tổng tiền (đ) *</label>
                    <input type="number" step="1" wire:model.live="tong_tien_nhap" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="VD: 500000">
                    @error('tong_tien_nhap') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    
                    <!-- Real-time calculated price -->
                    @if($ton_kho_hien_tai > 0 && $tong_tien_nhap > 0)
                        @php
                            $giaNhapTinhToan = number_format(round($tong_tien_nhap / $ton_kho_hien_tai, 2), 0, ',', '.');
                        @endphp
                        <div class="mt-2 p-3 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-lg">
                            <p class="text-sm text-green-800">
                                💰 <strong>Giá nhập tính toán:</strong> 
                                <span class="font-bold text-lg text-green-700">{{ $giaNhapTinhToan }}đ</span>
                                <span class="text-gray-600">/ {{ $don_vi_tinh ?: 'đơn vị' }}</span>
                            </p>
                        </div>
                    @else
                        <p class="text-xs text-indigo-600 mt-1 font-medium">💡 Giá nhập tự động tính = Tổng tiền ÷ Số lượng</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tồn kho tối thiểu (cảnh báo)</label>
                    <input type="number" step="0.01" wire:model="ton_kho_toi_thieu" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ton_kho_toi_thieu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $ingredient ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
