<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Loại ghi chú</h2>
        <button wire:click="openAddModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm loại mới
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
            <h3 class="font-semibold mb-4">{{ $editingType ? 'Sửa loại ghi chú' : 'Thêm loại mới' }}</h3>
            
            <form wire:submit="save">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên hiển thị *</label>
                        <input type="text" wire:model.live="ten_hien_thi" class="w-full px-3 py-2 border rounded-lg" placeholder="VD: Hợp đồng">
                        @error('ten_hien_thi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã loại (tự động)</label>
                        <input type="text" wire:model="ma_loai" readonly class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                        @error('ma_loai') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon *</label>
                        <div class="grid grid-cols-6 gap-2">
                            @foreach($icons as $i)
                                <button type="button" wire:click="$set('icon', '{{ $i }}')" 
                                    class="text-2xl p-2 rounded border-2 hover:bg-gray-50 {{ $icon === $i ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                    {{ $i }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Màu sắc *</label>
                        <select wire:model="mau_sac" class="w-full px-3 py-2 border rounded-lg">
                            @foreach($colors as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <label class="block text-sm font-medium text-gray-700 mb-1 mt-3">Thứ tự</label>
                        <input type="number" wire:model="thu_tu" class="w-full px-3 py-2 border rounded-lg" min="0">
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        {{ $editingType ? 'Cập nhật' : 'Thêm mới' }}
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">Icon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên hiển thị</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã loại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Màu sắc</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Số ghi chú</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($types as $type)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-2xl">{{ $type->icon }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="font-medium text-gray-900">{{ $type->ten_hien_thi }}</span>
                            @if($type->la_mac_dinh)
                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Mặc định</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $type->ma_loai }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full bg-{{ $type->mau_sac }}-100 text-{{ $type->mau_sac }}-800 text-xs">
                                {{ $colors[$type->mau_sac] ?? $type->mau_sac }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                {{ $type->notes()->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            @if(!$type->la_mac_dinh)
                                <button wire:click="openEditModal({{ $type->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</button>
                                <button wire:click="delete({{ $type->id }})" wire:confirm="Xóa loại này?" class="text-red-600 hover:text-red-900">Xóa</button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có loại ghi chú nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
