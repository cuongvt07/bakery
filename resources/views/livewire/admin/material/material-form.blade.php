<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ $materialId ? 'Sửa vật tư' : 'Thêm vật tư mới' }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">Quản lý vật tư & nguyên liệu</p>
        </div>
        <a href="{{ route('admin.materials.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
            ← Quay lại danh sách
        </a>
    </div>

    <!-- Messages -->
    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('message') }}</div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit="save">
            <div class="space-y-4">
                <!-- Đại lý -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đại lý *</label>
                    <select wire:model.live="diem_ban_id" class="w-full px-3 py-2 border rounded-lg" {{ $materialId ? 'disabled' : '' }}>
                        <option value="">-- Chọn đại lý --</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                        @endforeach
                    </select>
                    @error('diem_ban_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Grid 2 columns -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Tên vật tư -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên vật tư *</label>
                        <input type="text" 
                               wire:model.live="ten_vat_tu" 
                               class="w-full px-3 py-2 border rounded-lg" 
                               placeholder="VD: Băng keo lớn">
                        @error('ten_vat_tu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Mã vật tư -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã vật tư (tự động)</label>
                        <input type="text" 
                               wire:model="ma_vat_tu" 
                               readonly 
                               class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                        @error('ma_vat_tu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Vị trí -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí *</label>
                    <select wire:model.live="vi_tri_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">-- Chọn vị trí --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->ma_vi_tri }} - {{ $location->ten_vi_tri }}</option>
                        @endforeach
                    </select>
                    @error('vi_tri_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    
                    @if(!$diem_ban_id)
                        <p class="text-xs text-gray-500 mt-1">Vui lòng chọn đại lý trước</p>
                    @endif
                </div>

                <!-- Mô tả vị trí - EDITABLE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả vị trí</label>
                    <input type="text" 
                           wire:model="mo_ta_vi_tri" 
                           class="w-full px-3 py-2 border rounded-lg" 
                           placeholder="Nhập mô tả vị trí...">
                </div>

                <!-- Địa điểm -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
                    <input type="text" 
                           wire:model="dia_diem" 
                           class="w-full px-3 py-2 border rounded-lg" 
                           placeholder="Nhập địa điểm...">
                </div>

                <!-- Mô tả -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                    <textarea wire:model="mo_ta" 
                              rows="3" 
                              class="w-full px-3 py-2 border rounded-lg" 
                              placeholder="Nhập mô tả về vật tư..."></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                <a href="{{ route('admin.materials.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ $materialId ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
