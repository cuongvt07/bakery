<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $department ? 'Cập nhật Phòng Ban' : 'Thêm mới Phòng Ban' }}</h1>
        <a href="{{ route('admin.departments.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mã phòng ban -->
                @if($department)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã phòng ban</label>
                    <input type="text" value="{{ $ma_phong_ban }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Mã được hệ thống tự động sinh</p>
                </div>
                @endif

                <!-- Tên phòng ban -->
                <div class="{{ $department ? '' : 'md:col-span-2' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên phòng ban <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="ten_phong_ban" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Ví dụ: Phòng Bán Hàng">
                    @error('ten_phong_ban') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Mã màu -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Màu phòng ban <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-4">
                        <input type="color" wire:model.live="ma_mau" class="h-12 w-24 border border-gray-300 rounded cursor-pointer">
                        <input type="text" wire:model="ma_mau" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="#3B82F6">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Chọn màu để phân biệt phòng ban trong hệ thống</p>
                    @error('ma_mau') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Trạng thái -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model="trang_thai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="tam_ngung">Tạm ngừng</option>
                    </select>
                    @error('trang_thai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.departments.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">
                    Hủy
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $department ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
