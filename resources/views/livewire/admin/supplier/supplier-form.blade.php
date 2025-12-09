<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $supplier ? 'Cập nhật Nhà cung cấp' : 'Thêm mới Nhà cung cấp' }}</h1>
        <a href="{{ route('admin.suppliers.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mã nhà cung cấp (Auto-generated, Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mã nhà cung cấp
                        <span class="text-xs text-gray-500">(Tự động)</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="ma_ncc" 
                        readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600 cursor-not-allowed"
                    >
                </div>

                <!-- Tên nhà cung cấp -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên nhà cung cấp *</label>
                    <input type="text" wire:model="ten_ncc" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ten_ncc') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Người đại diện liên hệ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Người đại diện liên hệ</label>
                    <input type="text" wire:model="nguoi_dai_dien" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="VD: Nguyễn Văn A">
                    @error('nguoi_dai_dien') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="0901234567">
                    @error('so_dien_thoai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Số điện thoại Zalo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Số điện thoại Zalo
                        <span class="text-xs text-gray-500">(Nếu khác SĐT thường)</span>
                    </label>
                    <input type="text" wire:model="so_dien_thoai_zalo" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="0907654321">
                    @error('so_dien_thoai_zalo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="supplier@example.com">
                    @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Địa chỉ -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                    <input type="text" wire:model="dia_chi" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="123 Đường ABC, Quận XYZ, TP.HCM">
                    @error('dia_chi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Sản phẩm cung cấp -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sản phẩm cung cấp</label>
                    <textarea wire:model="san_pham_cung_cap" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="2" placeholder="VD: Bột mì, Đường, Bơ, Trứng gà..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Liệt kê các sản phẩm chính mà nhà cung cấp này cung cấp</p>
                    @error('san_pham_cung_cap') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Các nội dung thỏa thuận -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Các nội dung thỏa thuận</label>
                    <textarea wire:model="noi_dung_thoa_thuan" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="VD: Giá niêm yết theo tháng, Giao hàng trong vòng 2 ngày, Thanh toán cuối tháng..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Ghi chú các điều khoản thỏa thuận, giá cả, thời gian giao hàng, thanh toán...</p>
                    @error('noi_dung_thoa_thuan') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Ghi chú -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                    <textarea wire:model="ghi_chu" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="2" placeholder="Ghi chú thêm nếu có..."></textarea>
                    @error('ghi_chu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $supplier ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
