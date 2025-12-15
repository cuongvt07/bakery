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
        <!-- Mode Toggle (only for create) -->
        @if(!$materialId)
            <div class="mb-6 flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Chế độ:</span>
                <button type="button" 
                        wire:click="$set('bulkMode', false)"
                        class="px-4 py-2 text-sm rounded {{ !$bulkMode ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border' }}">
                    Thêm đơn
                </button>
                <button type="button" 
                        wire:click="$set('bulkMode', true)"
                        class="px-4 py-2 text-sm rounded {{ $bulkMode ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border' }}">
                    Thêm hàng loạt
                </button>
            </div>
        @endif

        @if($bulkMode)
            <!-- BULK ADD MODE -->
            <form wire:submit="saveBulk">
                <div class="space-y-4">
                    <!-- Vị trí (will auto-assign agency) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí * (cố định cho tất cả)</label>
                        <select wire:model.live="vi_tri_id" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Chọn vị trí --</option>
                            @php
                                $groupedLocations = $locations->groupBy('diem_ban_id');
                            @endphp
                            @foreach($groupedLocations as $agencyId => $agencyLocations)
                                <optgroup label="{{ $agencyLocations->first()->agency->ten_diem_ban }}">
                                    @foreach($agencyLocations as $location)
                                        <option value="{{ $location->id }}">{{ $location->ma_vi_tri }} - {{ $location->ten_vi_tri }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('vi_tri_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">💡 Đại lý sẽ tự động được gán dựa trên vị trí bạn chọn</p>
                    </div>

                    <!-- Bulk Material Names -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh sách vật tư * (mỗi dòng một tên)</label>
                        <textarea wire:model="bulk_materials" 
                                  rows="8" 
                                  class="w-full px-3 py-2 border rounded-lg font-mono text-sm" 
                                  placeholder="Dầu ăn&#10;Táo đỏ&#10;Băng keo lớn&#10;..."></textarea>
                        @error('bulk_materials') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">💡 Nhập mỗi dòng một tên vật tư. Mã sẽ tự động tạo.</p>
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
                        Thêm hàng loạt
                    </button>
                </div>
            </form>
        @else
            <!-- SINGLE ADD MODE -->
            <form wire:submit="save">
                <div class="space-y-4">
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

                    <!-- Vị trí (will auto-assign agency) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí *</label>
                        <select wire:model.live="vi_tri_id" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Chọn vị trí --</option>
                            @php
                                $groupedLocations = $locations->groupBy('diem_ban_id');
                            @endphp
                            @foreach($groupedLocations as $agencyId => $agencyLocations)
                                <optgroup label="{{ $agencyLocations->first()->agency->ten_diem_ban }}">
                                    @foreach($agencyLocations as $location)
                                        <option value="{{ $location->id }}">{{ $location->ma_vi_tri }} - {{ $location->ten_vi_tri }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('vi_tri_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">💡 Đại lý sẽ tự động được gán dựa trên vị trí bạn chọn</p>
                    </div>

                    <!-- Mô tả vị trí - EDITABLE -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả vị trí</label>
                        <input type="text" 
                               wire:model="mo_ta_vi_tri" 
                               class="w-full px-3 py-2 border rounded-lg" 
                               placeholder="Nhập mô tả vị trí...">
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
        @endif
    </div>
</div>
