<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">{{ $noteId ? 'Sửa ghi chú' : 'Thêm ghi chú mới' }}</h3>
            <a href="{{ route('admin.agencies.detail', $agencyId) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <form wire:submit="save">
            <div class="space-y-4">
                <!-- Loại -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại ghi chú *</label>
                    <select wire:model="loai" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="hop_dong">📄 Hợp đồng thuê</option>
                        <option value="chi_phi">💰 Chi phí (điện, nước, nhà...)</option>
                        <option value="cong_an">👮 Công an</option>
                        <option value="vat_dung">🪑 Vật dụng</option>
                        <option value="bien_bao">🪧 Biển bảo quảng cáo</option>
                        <option value="khac">📝 Khác</option>
                    </select>
                    @error('loai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Tiêu đề -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                    <input type="text" wire:model="tieu_de" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="VD: Hợp đồng thuê 2024">
                    @error('tieu_de') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Nội dung -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                    <textarea wire:model="noi_dung" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Nhập mô tả..."></textarea>
                </div>

                <!-- Mức độ quan trọng -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mức độ quan trọng</label>
                    <select wire:model="muc_do_quan_trong" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="thap">Thấp</option>
                        <option value="trung_binh">Trung bình</option>
                        <option value="cao">Cao</option>
                        <option value="khan_cap">Khẩn cấp</option>
                    </select>
                </div>

                <!--  Ngày nhắc nhở -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày nhắc nhở</label>
                    <input type="date" wire:model="ngay_nhac_nho" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <!-- Upload images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh minh chứng</label>
                    <input type="file" wire:model="images" multiple accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Có thể chọn nhiều ảnh. Tối đa 2MB/ảnh</p>
                    @error('images.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Image previews -->
                @if(count($existingImages) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh hiện tại</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($existingImages as $index => $image)
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $image) }}" class="w-full h-24 object-cover rounded">
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-700">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($images) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh mới</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($images as $image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-24 object-cover rounded">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.agencies.detail', $agencyId) }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Hủy
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ $noteId ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
