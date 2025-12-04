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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đơn vị bán lẻ</label>
                    <input type="text" wire:model.live="don_vi_tinh" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="cái, chiếc, hộp">
                    <p class="text-xs text-gray-500 mt-1">Đơn vị khi bán lẻ cho khách hàng</p>
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

                <!-- Quy cách đóng gói -->
                <div class="md:col-span-2 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Quy cách đóng gói (Tùy chọn)
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Nếu sản phẩm được đóng gói (VD: Khay, Hộp), hãy điền thông tin bên dưới</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Đơn vị đóng gói</label>
                            <input type="text" wire:model.live="don_vi_phan_phoi" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white" placeholder="VD: Khay, Hộp, Thùng">
                            <p class="text-xs text-gray-500 mt-1">Để trống nếu không có đóng gói</p>
                            @error('don_vi_phan_phoi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng / 1 đơn vị</label>
                            <input type="number" wire:model.live="so_luong_quy_doi" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-white" min="1" placeholder="VD: 10">
                            <p class="text-xs text-gray-500 mt-1">Có bao nhiêu {{ $don_vi_tinh }} trong 1 {{ $don_vi_phan_phoi ?: 'đơn vị' }}?</p>
                            @error('so_luong_quy_doi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($don_vi_phan_phoi && $so_luong_quy_doi > 1)
                        <div class="mt-4 p-3 bg-white border-l-4 border-indigo-600 rounded">
                            <p class="text-sm font-semibold text-gray-800">
                                ✓ Quy cách: <span class="text-indigo-600">1 {{ $don_vi_phan_phoi }} = {{ $so_luong_quy_doi }} {{ $don_vi_tinh }}</span>
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                Khi phân bổ, bạn có thể nhập theo {{ $don_vi_phan_phoi }} hoặc {{ $don_vi_tinh }}
                            </p>
                        </div>
                    @endif
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
