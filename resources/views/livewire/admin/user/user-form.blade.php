<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $user ? 'Cập nhật Người dùng' : 'Thêm mới Người dùng' }}</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            
            <!-- SECTION 1: BASIC INFO & AVATAR -->
            <div class="flex flex-col md:flex-row gap-6 mb-8">
                <!-- Avatar Column -->
                <div class="w-full md:w-48 flex-shrink-0 flex flex-col items-center pt-2">
                    <div class="w-32 h-32 mb-4 relative rounded-full overflow-hidden border-4 border-white shadow-md bg-gray-100 group">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($existing_avatar)
                            <img src="{{ asset('storage/' . $existing_avatar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50">
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-30 hidden group-hover:flex items-center justify-center transition-all">
                            <span class="text-white text-xs font-semibold">Thay đổi</span>
                        </div>
                    </div>
                    
                    <label class="cursor-pointer bg-white border border-gray-300 rounded-md px-3 py-1.5 text-xs hover:bg-gray-50 font-medium text-gray-700 shadow-sm transition-colors">
                        📷 Đổi ảnh
                        <input type="file" wire:model="image" accept="image/*" class="hidden">
                    </label>
                    @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Basic Fields Column -->
                <div class="flex-grow grid grid-cols-1 lg:grid-cols-2 gap-5">
                     <!-- Mã nhân viên -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mã nhân viên</label>
                        <input type="text" wire:model="ma_nhan_vien" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                    </div>
                    
                    <!-- Họ tên -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Họ Tên <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="ho_ten" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @error('ho_ten') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Mật khẩu -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu @if(!$user) <span class="text-red-500">*</span> @endif</label>
                        <input type="password" wire:model="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="{{ $user ? 'Giữ nguyên nếu không đổi' : '' }}">
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Vai trò -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
                        <select wire:model="vai_tro" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                            <option value="nhan_vien">Nhân viên</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <!-- Trạng thái -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Trạng thái</label>
                        <select wire:model="trang_thai" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                            <option value="hoat_dong">Hoạt động</option>
                            <option value="khoa">Khóa</option>
                        </select>
                    </div>
                    
                    <!-- Facebook -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Facebook</label>
                        <input type="text" wire:model="facebook" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Link profile facebook...">
                    </div>
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <!-- SECTION 2: IDENTITY & CONTACT -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                👤 Thông tin cá nhân & Liên hệ
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" wire:model="dia_chi" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- CMND/CCCD -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số CMND/CCCD</label>
                    <input type="text" wire:model="so_cmnd" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày cấp</label>
                    <input type="date" wire:model="ngay_cap_cmnd" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nơi cấp</label>
                    <input type="text" wire:model="noi_cap_cmnd" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <!-- Emergency Contact -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Liên hệ khẩn cấp (Tên)</label>
                    <input type="text" wire:model="nguoi_lien_he_khan_cap" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SĐT liên hệ khẩn cấp</label>
                    <input type="text" wire:model="sdt_lien_he_khan_cap" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <!-- SECTION 3: WORK & ASSIGNMENT -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                🏢 Công việc & Phân bổ
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày vào làm</label>
                    <input type="date" wire:model="ngay_vao_lam" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Agency Assignment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phân bổ điểm bán</label>
                    <div class="border border-gray-300 rounded-md p-3 max-h-48 overflow-y-auto">
                        @if($agencies->isEmpty())
                            <p class="text-sm text-gray-500">Chưa có điểm bán nào.</p>
                        @else
                            @foreach($agencies as $agency)
                                <div class="flex items-center mb-2 last:mb-0">
                                    <input type="checkbox" 
                                           id="agency_{{ $agency->id }}" 
                                           value="{{ $agency->id }}" 
                                           wire:model="selectedAgencies"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="agency_{{ $agency->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $agency->ten_diem_ban }}
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Nhân viên sẽ được gán vào các điểm bán này.</p>
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <!-- SECTION 4: CONTRACT -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                📋 Hợp đồng lao động
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Loại hợp đồng -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại hợp đồng</label>
                    <select wire:model="loai_hop_dong" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">-- Chọn loại --</option>
                        <option value="thu_viec">Thử việc</option>
                        <option value="chinh_thuc">Chính thức</option>
                        <option value="hop_tac">Hợp tác</option>
                    </select>
                </div>

                <!-- Ngày ký -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày ký HĐ</label>
                    <input type="date" wire:model="ngay_ky_hop_dong" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Ngày hết hạn -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                    <input type="date" wire:model="ngay_het_han_hop_dong" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                 <!-- Ngày thử việc -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu thử việc</label>
                    <input type="date" wire:model="ngay_thu_viec" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Ngày chính thức -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày chuyển chính thức</label>
                    <input type="date" wire:model="ngay_chinh_thuc" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- File -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File hợp đồng (PDF/Img)</label>
                    <input type="file" wire:model="file_hop_dong" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    @if($existing_file)
                        <div class="mt-1">
                            <a href="{{ asset('storage/' . $existing_file) }}" target="_blank" class="text-xs text-indigo-600 hover:underline">
                                📎 Xem file hiện tại
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Ghi chú -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú hợp đồng</label>
                    <textarea wire:model="ghi_chu_hop_dong" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <!-- SECTION 5: FINANCE & BANKING -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                💰 Tài chính & Ngân hàng
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Banking -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên Ngân hàng</label>
                    <input type="text" wire:model="ngan_hang" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="VD: Vietcombank">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản</label>
                    <input type="text" wire:model="so_tai_khoan" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chủ tài khoản</label>
                    <input type="text" wire:model="chu_tai_khoan" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <!-- Salary -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại lương</label>
                    <select wire:model="loai_luong" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="theo_ngay">Theo ngày</option>
                        <option value="theo_gio">Theo giờ</option>
                    </select>
                </div>
                
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LCB/Lương đóng BH</label>
                    <input type="number" wire:model="luong_co_ban" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="0">
                </div>
                <!-- Spacer -->
                <div class="hidden md:block"></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lương thử việc</label>
                    <input type="number" wire:model="luong_thu_viec" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lương chính thức</label>
                    <input type="number" wire:model="luong_chinh_thuc" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="0">
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="mt-8 flex justify-end gap-3 border-t pt-4">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700">
                    Hủy
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-medium">
                    {{ $user ? 'Lưu thay đổi' : 'Thêm nhân viên' }}
                </button>
            </div>
        </form>
    </div>
</div>
