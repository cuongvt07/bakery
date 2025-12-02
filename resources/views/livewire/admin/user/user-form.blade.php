<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $user ? 'Cập nhật Người dùng' : 'Thêm mới Người dùng' }}</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Họ tên -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Họ Tên</label>
                    <input type="text" wire:model="ho_ten" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ho_ten') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Mật khẩu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu {{ $user ? '(Để trống nếu không đổi)' : '' }}</label>
                    <input type="password" wire:model="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('so_dien_thoai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Vai trò -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
                    <select wire:model="vai_tro" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="nhan_vien">Nhân viên</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('vai_tro') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Trạng thái -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model="trang_thai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="khoa">Khóa</option>
                    </select>
                    @error('trang_thai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Lương cơ bản -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lương cơ bản</label>
                    <input type="number" wire:model="luong_co_ban" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('luong_co_ban') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Loại lương -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại lương</label>
                    <select wire:model="loai_luong" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="theo_ngay">Theo ngày</option>
                        <option value="theo_gio">Theo giờ</option>
                    </select>
                    @error('loai_luong') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $user ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
