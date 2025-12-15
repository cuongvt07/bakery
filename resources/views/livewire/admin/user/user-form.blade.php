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
                <!-- Mã nhân viên (Auto-generated, Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mã nhân viên 
                        <span class="text-xs text-gray-500">(Tự động)</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="ma_nhan_vien" 
                        readonly
                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600 cursor-not-allowed"
                    >
                </div>

                <!-- Họ tên -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Họ Tên *</label>
                    <input type="text" wire:model="ho_ten" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ho_ten') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Mật khẩu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu {{ $user ? '(Để trống nếu không đổi)' : '*' }}</label>
                    <input type="password" wire:model="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Vai trò -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò *</label>
                    <select wire:model="vai_tro" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="nhan_vien">Nhân viên</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Trạng thái -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model="trang_thai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="khoa">Khóa</option>
                    </select>
                </div>

                <!-- CONTRACT SECTION -->
                <div class="col-span-2 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">📋 Thông tin hợp đồng</h3>
                </div>

                <!-- Loại hợp đồng -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại hợp đồng</label>
                    <select wire:model="loai_hop_dong" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn loại --</option>
                        <option value="thu_viec">Thử việc</option>
                        <option value="chinh_thuc">Chính thức</option>
                        <option value="hop_tac">Hợp tác</option>
                    </select>
                </div>

                <!-- File hợp đồng -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File hợp đồng (PDF, Image)</label>
                    <input type="file" 
                           wire:model="file_hop_dong" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    @error('file_hop_dong') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    
                    <div wire:loading wire:target="file_hop_dong" class="text-sm text-indigo-600 mt-1">
                        ⏳ Đang upload...
                    </div>
                    
                    @if($existing_file)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $existing_file) }}" 
                               target="_blank"
                               class="text-sm text-indigo-600 hover:underline inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                                </svg>
                                Xem file hiện tại
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Ngày ký hợp đồng -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày ký HĐ (Bắt đầu)</label>
                    <input type="date" wire:model="ngay_ky_hop_dong" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Ngày hết hạn hợp đồng -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày hết hạn (Kết thúc)</label>
                    <input type="date" wire:model="ngay_het_han_hop_dong" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Ngày thử việc -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu thử việc</label>
                    <input type="date" wire:model="ngay_thu_viec" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Ngày chính thức -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày chuyển chính thức</label>
                    <input type="date" wire:model="ngay_chinh_thuc" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Ghi chú hợp đồng -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú hợp đồng</label>
                    <textarea wire:model="ghi_chu_hop_dong" 
                              rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-md"
                              placeholder="Ghi chú về hợp đồng..."></textarea>
                </div>

                <!-- SALARY SECTION -->
                <div class="col-span-2 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">💰 Thông tin lương</h3>
                </div>

                <!-- Loại lương -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại lương</label>
                    <select wire:model="loai_luong" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                        <option value="theo_ngay">Theo ngày</option>
                        <option value="theo_gio">Theo giờ</option>
                    </select>
                </div>

                <!-- placeholder div -->
                <div></div>

                <!-- Lương thử việc -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lương thử việc</label>
                    <input type="number" wire:model="luong_thu_viec" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Lương chính thức -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lương chính thức</label>
                    <input type="number" wire:model="luong_chinh_thuc" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-4">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    {{ $user ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
