<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $product ? 'Cập nhật Sản phẩm' : 'Thêm mới Sản phẩm' }}</h1>
        <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã sản phẩm</label>
                    <input type="text" wire:model="ma_san_pham" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="VD: SP001">
                    @error('ma_san_pham') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm</label>
                    <input type="text" wire:model="ten_san_pham" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ten_san_pham') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                    <select wire:model="danh_muc_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->ten_danh_muc }}</option>
                        @endforeach
                    </select>
                    @error('danh_muc_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá bán (VNĐ)</label>
                    <input type="number" wire:model="gia_ban" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('gia_ban') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đơn vị tính</label>
                    <input type="text" wire:model="don_vi_tinh" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="cái, khay, hộp">
                    @error('don_vi_tinh') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model="trang_thai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="con_hang">Còn hàng</option>
                        <option value="het_hang">Hết hàng</option>
                        <option value="ngung_ban">Ngừng bán</option>
                    </select>
                    @error('trang_thai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                    <textarea wire:model="mo_ta" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
                    @error('mo_ta') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $product ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
