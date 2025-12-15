<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Vị trí vật dụng</h2>
        <button wire:click="openAddModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm vị trí
        </button>
    </div>

    <!-- Messages -->
    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">{{ session('error') }}</div>
    @endif

    <!-- Add/Edit Form (Inline) -->
    @if($showModal)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold mb-4">{{ $editingLocation ? 'Sửa vị trí' : 'Thêm vị trí mới' }}</h3>
            
            <form wire:submit="save">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên vị trí *</label>
                        <input type="text" wire:model.live="ten_vi_tri" class="w-full px-3 py-2 border rounded-lg" placeholder="VD: Giỏ 1, Tủ 2">
                        @error('ten_vi_tri') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã vị trí *</label>
                        <div class="relative">
                            <input type="text" 
                                   wire:model.live="ma_vi_tri" 
                                   class="w-full px-3 py-2 border rounded-lg uppercase {{ $isDuplicate ? 'border-red-500 bg-red-50' : '' }}"
                                   placeholder="VD: GI1, T2, K5">
                            <!-- Loading spinner khi đang check -->
                            <div wire:loading wire:target="ma_vi_tri" class="absolute right-3 top-2.5">
                                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('ma_vi_tri') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @if($isDuplicate)
                            <div class="mt-1 flex items-center gap-1 text-red-600 text-sm font-medium">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $duplicateMessage }}</span>
                            </div>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">💡 Tự động tạo từ tên, bạn có thể chỉnh sửa</p>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                        <textarea wire:model="mo_ta" rows="2" class="w-full px-3 py-2 border rounded-lg" placeholder="VD: Đồ văn phòng, Thực phẩm khô..."></textarea>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                        <input type="text" wire:model="dia_chi" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" wire:click="showModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Hủy</button>
                    <button type="submit" 
                            {{ $isDuplicate ? 'disabled' : '' }}
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 {{ $isDuplicate ? 'opacity-50 cursor-not-allowed' : '' }}">
                        {{ $editingLocation ? 'Cập nhật' : 'Thêm mới' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- List -->
    <div class="bg-white rounded-lg border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">Mã vị trí</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên vị trí</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Địa chỉ</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số vật dụng</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($locations as $location)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-indigo-600">
                            {{ $location->ma_vi_tri }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $location->ten_vi_tri }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $location->mo_ta ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $location->dia_chi ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                {{ $location->notes()->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="openEditModal({{ $location->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</button>
                            <button wire:click="delete({{ $location->id }})" wire:confirm="Xóa vị trí này?" class="text-red-600 hover:text-red-900">Xóa</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có vị trí nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
