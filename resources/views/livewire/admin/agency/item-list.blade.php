<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold text-gray-800">Danh sách vật dụng</h3>
        <button wire:click="openAddModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm vật dụng
        </button>
    </div>

    <!-- Messages -->
    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('message') }}</div>
    @endif

    <!-- Add/Edit Form (Inline) -->
    @if($showModal)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold">{{ $editingItem ? 'Sửa vật dụng' : 'Thêm vật dụng mới' }}</h4>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            
            <form wire:submit="save">
                <!-- All fields in ONE ROW -->
                <div class="grid grid-cols-5 gap-3 items-start">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tên vật dụng *</label>
                        <input type="text" wire:model.live="ten_vat_dung" class="w-full px-2 py-2 text-sm border rounded" placeholder="VD: Băng keo">
                        @error('ten_vat_dung') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mã (tự động)</label>
                        <input type="text" wire:model="ma_vat_dung" readonly class="w-full px-2 py-2 text-sm border rounded bg-gray-100 text-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Vị trí *</label>
                        <select wire:model.live="vi_tri_id" class="w-full px-2 py-2 text-sm border rounded">
                            <option value="">-- Chọn --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->ma_vi_tri }}</option>
                            @endforeach
                        </select>
                        @error('vi_tri_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mô tả vị trí</label>
                        <input type="text" wire:model="mo_ta_vi_tri" readonly class="w-full px-2 py-2 text-sm border rounded bg-gray-50">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-3 py-2 text-sm border rounded hover:bg-gray-50">Hủy</button>
                        <button type="submit" class="px-3 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            {{ $editingItem ? 'Cập nhật' : 'Thêm' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Mã</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên vật dụng</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-40">Vị trí</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-mono font-semibold text-indigo-600">
                            {{ $item->metadata['ma_vat_dung'] ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <div class="max-w-xs overflow-hidden text-ellipsis" title="{{ $item->tieu_de }}">{{ $item->tieu_de }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $item->location ? $item->location->ma_vi_tri : '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <div class="max-w-md overflow-hidden text-ellipsis" title="{{ $item->metadata['mo_ta_vi_tri'] ?? ($item->location->mo_ta ?? '-') }}">
                                {{ $item->metadata['mo_ta_vi_tri'] ?? ($item->location->mo_ta ?? '-') }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                            <button wire:click="openEditModal({{ $item->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Xóa vật dụng này?" class="text-red-600 hover:text-red-900">Xóa</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Chưa có vật dụng nào. Click "Thêm vật dụng" để bắt đầu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($items->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
