<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Thông tin điểm bán</h2>
            <p class="text-sm text-gray-500 mt-1">Điền thông tin chi tiết cho điểm bán mới.</p>
        </div>
        
        <form wire:submit="submit" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Mã điểm bán -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã điểm bán <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="ma_diem_ban" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-gray-50" readonly>
                    @error('ma_diem_ban') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Tên điểm bán -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên điểm bán <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="ten_diem_ban" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('ten_diem_ban') border-red-500 @enderror">
                    @error('ten_diem_ban') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Số điện thoại -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('so_dien_thoai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- Trạng thái -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select wire:model="trang_thai" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="tam_ngung">Tạm ngưng</option>
                        <option value="dong_cua">Đóng cửa</option>
                    </select>
                </div>
                
                <!-- Địa chỉ -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="dia_chi" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('dia_chi') border-red-500 @enderror">
                    @error('dia_chi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                
                <!-- GPS -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vĩ độ (Latitude)</label>
                    <input type="number" step="any" wire:model="vi_do" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kinh độ (Longitude)</label>
                    <input type="number" step="any" wire:model="kinh_do" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                
                <!-- Ghi chú -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea wire:model="ghi_chu" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>
                
                <div class="col-span-2 border-t border-gray-200 my-2"></div>
                
                <!-- Vật dụng -->
                <div class="col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-800">Danh sách vật dụng</h3>
                        <button type="button" wire:click="addVatDung" 
                                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Thêm vật dụng
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($vat_dungs as $index => $vat_dung)
                            <div class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                <div class="flex-1">
                                    <input type="text" wire:model="vat_dungs.{{ $index }}.ten" placeholder="Tên vật dụng"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                                <div class="w-24">
                                    <input type="number" wire:model="vat_dungs.{{ $index }}.so_luong" placeholder="SL" min="1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                                <div class="w-32">
                                    <select wire:model="vat_dungs.{{ $index }}.tinh_trang" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                        <option value="tot">Tốt</option>
                                        <option value="hong">Hỏng</option>
                                        <option value="can_sua">Cần sửa</option>
                                    </select>
                                </div>
                                <button type="button" wire:click="removeVatDung({{ $index }})" class="text-red-500 hover:text-red-700 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.diemban.index') }}" 
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
                    Tạo điểm bán
                </button>
            </div>
        </form>
    </div>
</div>
