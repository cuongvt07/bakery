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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã vị trí (tự động)</label>
                        <input type="text" wire:model="ma_vi_tri" readonly class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed uppercase">
                        @error('ma_vi_tri') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
