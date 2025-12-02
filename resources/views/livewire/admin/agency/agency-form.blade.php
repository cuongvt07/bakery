<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $agency ? 'Cập nhật Điểm bán' : 'Thêm mới Điểm bán' }}</h1>
        <a href="{{ route('admin.agencies.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mã điểm bán -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã điểm bán</label>
                    <input type="text" wire:model="ma_diem_ban" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="VD: DB001">
                    @error('ma_diem_ban') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tên điểm bán -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên điểm bán</label>
                    <input type="text" wire:model="ten_diem_ban" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ten_diem_ban') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Địa chỉ -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                    <input type="text" wire:model="dia_chi" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('dia_chi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('so_dien_thoai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Trạng thái -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model="trang_thai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="dong_cua">Đóng cửa</option>
                    </select>
                    @error('trang_thai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Ghi chú -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                    <textarea wire:model="ghi_chu" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
                    @error('ghi_chu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $agency ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
