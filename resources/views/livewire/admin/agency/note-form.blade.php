<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-4 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-semibold">{{ $noteId ? 'Sửa ghi chú' : 'Thêm ghi chú mới' }}</h3>
            <a href="{{ route('admin.agencies.detail', $agencyId) }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>

        <form wire:submit="save">
            <div class="space-y-3">
                <!-- Loại ghi chú in compact row -->
                <div class="grid grid-cols-3 gap-3 items-start">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Loại ghi chú *</label>
                        <select wire:model.live="loai" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                            <option value="">-- Chọn loại --</option>
                            @foreach($noteTypes as $type)
                                <option value="{{ $type->ma_loai }}">{{ $type->display_label }}</option>
                            @endforeach
                        </select>
                        @error('loai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mức độ quan trọng</label>
                        <select wire:model="muc_do_quan_trong" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                            <option value="thap">Thấp</option>
                            <option value="trung_binh">Trung bình</option>
                            <option value="cao">Cao</option>
                            <option value="khan_cap">Khẩn cấp</option>
                        </select>
                    </div>
                </div>

                <!-- Item-specific fields (only for vat_dung) -->
                @if($loai === 'vat_dung')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <h4 class="font-semibold text-xs text-gray-700 mb-2">Thông tin vật dụng</h4>
                        
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tên vật dụng *</label>
                                <input type="text" wire:model.live="tieu_de" class="w-full px-2 py-1.5 text-sm border rounded-lg" placeholder="VD: Băng keo">
                                @error('tieu_de') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Mã (tự động)</label>
                                <input type="text" wire:model="ma_vat_dung" readonly class="w-full px-2 py-1.5 text-sm border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                                @error('ma_vat_dung') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Vị trí *</label>
                                <select wire:model.live="vi_tri_id" class="w-full px-2 py-1.5 text-sm border rounded-lg">
                                    <option value="">-- Chọn --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->ma_vi_tri }}</option>
                                    @endforeach
                                </select>
                                @error('vi_tri_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Mô tả vị trí</label>
                                <input type="text" wire:model="mo_ta_vi_tri" readonly class="w-full px-2 py-1.5 text-sm border rounded-lg bg-gray-50">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Địa điểm</label>
                                <input type="text" wire:model="dia_diem" readonly class="w-full px-2 py-1.5 text-sm border rounded-lg bg-gray-50">
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Tiêu đề (for non-item notes) -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tiêu đề *</label>
                        <input type="text" wire:model="tieu_de" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg" placeholder="VD: Hợp đồng thuê 2024">
                        @error('tieu_de') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
@endif

                <!-- Nội dung & Ngày nhắc in 2 columns -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                        <textarea wire:model="noi_dung" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg" placeholder="Nhập mô tả..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ngày nhắc nhở</label>
                        <input type="date" wire:model="ngay_nhac_nho" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                    </div>
                </div>

                <!-- Upload images -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Hình ảnh minh chứng</label>
                    <input type="file" 
                           wire:model="images" 
                           multiple 
                           accept="image/*"
                           onchange="compressImages(this)"
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-0.5">💡 Ảnh tự động nén &lt;500KB. Hỗ trợ: JPG, PNG, HEIC</p>
                    @error('images.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Image previews - Compact grid -->
                @if(count($existingImages) > 0)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ảnh đã tải lên</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($existingImages as $index => $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Existing" 
                                         class="w-full h-20 object-cover rounded border border-gray-200">
                                    
                                    <button type="button"
                                            wire:click="removeExistingImage({{ $index }})"
                                            class="absolute top-0.5 right-0.5 bg-red-600 hover:bg-red-700 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($images) > 0)
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ảnh mới</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($images as $index => $image)
                                <div class="relative group">
                                    <img src="{{ $image->temporaryUrl() }}" 
                                         alt="Preview" 
                                         class="w-full h-20 object-cover rounded border border-gray-200">
                                    
                                    <button type="button"
                                            wire:click="removeNewImage({{ $index }})"
                                            class="absolute top-0.5 right-0.5 bg-red-600 hover:bg-red-700 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs">
                                        ×
                                    </button>
                                    
                                    <div class="absolute bottom-0.5 left-0.5 bg-black/60 text-white text-xs px-1 py-0.5 rounded">
                                        {{ number_format($image->getSize() / 1024, 0) }}KB
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-4 flex justify-end gap-2 border-t pt-3">
                <a href="{{ route('admin.agencies.detail', $agencyId) }}" 
                   class="px-4 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                    Hủy
                </a>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    {{ $noteId ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
