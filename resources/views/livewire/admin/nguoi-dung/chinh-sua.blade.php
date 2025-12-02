<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Chỉnh sửa thông tin</h2>
            <p class="text-sm text-gray-500 mt-1">Cập nhật thông tin cho người dùng: {{ $ho_ten }}</p>
        </div>
        
        <form wire:submit="submit" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Họ tên -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="ho_ten" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('ho_ten') border-red-500 @enderror">
                    @error('ho_ten') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Email -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Số điện thoại -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('so_dien_thoai') border-red-500 @enderror">
                    @error('so_dien_thoai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Vai trò -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
                    <select wire:model="vai_tro" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="nhan_vien">Nhân viên</option>
                        <option value="admin">Quản trị viên (Admin)</option>
                    </select>
                    @error('vai_tro') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Mật khẩu -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới (Để trống nếu không đổi)</label>
                    <input type="password" wire:model="mat_khau" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('mat_khau') border-red-500 @enderror">
                    @error('mat_khau') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Xác nhận mật khẩu -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới</label>
                    <input type="password" wire:model="mat_khau_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                
                <!-- Địa chỉ -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <textarea wire:model="dia_chi" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="col-span-2 border-t border-gray-200 my-2"></div>
                
                <!-- Thông tin lương & Công việc -->
                <div class="col-span-2">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Thông tin công việc & Lương</h3>
                </div>
                
                <!-- Ngày vào làm -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày vào làm</label>
                    <input type="date" wire:model="ngay_vao_lam" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                
                <!-- Trạng thái -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select wire:model="trang_thai" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="khoa">Bị khóa</option>
                    </select>
                </div>
                
                <!-- Loại lương -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại lương <span class="text-red-500">*</span></label>
                    <select wire:model="loai_luong" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="theo_ngay">Theo ngày công</option>
                        <option value="theo_gio">Theo giờ làm</option>
                    </select>
                </div>
                
                <!-- Lương cơ bản -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lương cơ bản (VNĐ) <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="luong_co_ban" min="0" step="1000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('luong_co_ban') border-red-500 @enderror">
                    @error('luong_co_ban') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Ảnh đại diện -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện</label>
                    <div class="mt-1 flex items-center">
                        @if ($anh_dai_dien)
                            <img src="{{ $anh_dai_dien->temporaryUrl() }}" class="h-12 w-12 rounded-full object-cover mr-4">
                        @elseif($anh_dai_dien_cu)
                             <img src="{{ asset('storage/' . $anh_dai_dien_cu) }}" class="h-12 w-12 rounded-full object-cover mr-4">
                        @else
                            <span class="h-12 w-12 rounded-full overflow-hidden bg-gray-100 mr-4">
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                        @endif
                        <input type="file" wire:model="anh_dai_dien" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    @error('anh_dai_dien') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.nguoidung.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
                        wire:loading.attr="disabled">
                    <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
