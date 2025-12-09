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
                    <select wire:model.live="loai" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">-- Chọn loại --</option>
                        @foreach($noteTypes as $type)
                            <option value="{{ $type->ma_loai }}">{{ $type->display_label }}</option>
                        @endforeach
                    </select>
                    @error('loai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Item-specific fields (only for vat_dung) -->
                @if($loai === 'vat_dung')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
                        <h4 class="font-semibold text-sm text-gray-700 mb-2">Thông tin vật dụng</h4>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên vật dụng *</label>
                                <input type="text" wire:model.live="tieu_de" class="w-full px-3 py-2 border rounded-lg" placeholder="VD: Băng keo">
                                @error('tieu_de') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mã vật dụng (tự động)</label>
                                <input type="text" wire:model="ma_vat_dung" readonly class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" placeholder="Tự động tạo...">
                                @error('ma_vat_dung') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí *</label>
                                <select wire:model.live="vi_tri_id" class="w-full px-3 py-2 border rounded-lg">
                                    <option value="">-- Chọn vị trí --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->ma_vi_tri }} - {{ $loc->ten_vi_tri }}</option>
                                    @endforeach
                                </select>
                                @error('vi_tri_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả vị trí</label>
                                <input type="text" wire:model="mo_ta_vi_tri" readonly class="w-full px-3 py-2 border rounded-lg bg-gray-50">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
                                <input type="text" wire:model="dia_diem" readonly class="w-full px-3 py-2 border rounded-lg bg-gray-50">
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Tiêu đề (for non-item notes) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                        <input type="text" wire:model="tieu_de" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="VD: Hợp đồng thuê 2024">
                        @error('tieu_de') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
@endif

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
                    <input type="file" 
                           wire:model="images" 
                           multiple 
                           accept="image/*"
                           onchange="compressImages(this)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">💡 Ảnh tự động nén &lt;500KB. Hỗ trợ: JPG, PNG, HEIC</p>
                    @error('images.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Image previews -->
                @if(count($existingImages) > 0)
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh đã tải lên</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($existingImages as $index => $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Existing" 
                                         class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                    
                                    <!-- Delete button -->
                                    <button type="button"
                                            wire:click="removeExistingImage({{ $index }})"
                                            class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif


                @if(count($images) > 0)
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh mới</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($images as $index => $image)
                                <div class="relative group">
                                    <img src="{{ $image->temporaryUrl() }}" 
                                         alt="Preview" 
                                         class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                    
                                    <!-- Delete button -->
                                    <button type="button"
                                            wire:click="removeNewImage({{ $index }})"
                                            class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        ×
                                    </button>
                                    
                                    <!-- File size badge -->
                                    <div class="absolute bottom-1 left-1 bg-black/60 text-white text-xs px-2 py-1 rounded">
                                        {{ number_format($image->getSize() / 1024, 0) }} KB
                                    </div>
                                </div>
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
