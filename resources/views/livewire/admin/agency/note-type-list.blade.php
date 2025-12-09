<div class="p-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Loại ghi chú</h2>
        <button wire:click="openAddModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 text-sm rounded-lg flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm loại
        </button>
    </div>

    <!-- Messages -->
    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-3 text-sm">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-3 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Add/Edit Form (Inline) -->
    @if($showModal)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <h3 class="font-semibold text-sm mb-2">{{ $editingType ? 'Sửa loại ghi chú' : 'Thêm loại mới' }}</h3>
            
            <form wire:submit="save">
                <!-- Horizontal compact form -->
                <div class="grid grid-cols-4 gap-2 items-start mb-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tên hiển thị *</label>
                        <input type="text" wire:model.live="ten_hien_thi" class="w-full px-2 py-1.5 text-sm border rounded-lg" placeholder="Hợp đồng">
                        @error('ten_hien_thi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Mã (tự động)</label>
                        <input type="text" wire:model="ma_loai" readonly class="w-full px-2 py-1.5 text-sm border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                        @error('ma_loai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Màu sắc *</label>
                        <select wire:model="mau_sac" class="w-full px-2 py-1.5 text-sm border rounded-lg">
                            @foreach($colors as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Thứ tự</label>
                        <input type="number" wire:model="thu_tu" class="w-full px-2 py-1.5 text-sm border rounded-lg" min="0" placeholder="0">
                    </div>
                </div>

                <!-- Icon selection - compact -->
                <div class="mb-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Icon *</label>
                    <div class="flex flex-wrap gap-1">
                        @foreach($icons as $i)
                            <button type="button" wire:click="$set('icon', '{{ $i }}')" 
                                class="text-xl p-1.5 rounded border {{ $icon === $i ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                {{ $i }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showModal', false)" class="px-3 py-1 text-sm border rounded-lg hover:bg-gray-50">Hủy</button>
                    <button type="submit" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-12">Icon</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tên</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mã</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Màu</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16">Số GC</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-24">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($types as $type)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-xl">{{ $type->icon }}</td>
                        <td class="px-3 py-2 text-sm">
                            <span class="font-medium text-gray-900">{{ $type->ten_hien_thi }}</span>
                            @if($type->la_mac_dinh)
                                <span class="ml-1 px-1.5 py-0.5 bg-green-100 text-green-800 rounded text-xs">Mặc định</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs font-mono text-gray-600">{{ $type->ma_loai }}</td>
                        <td class="px-3 py-2 text-xs">
                            <span class="px-2 py-0.5 rounded-full bg-{{ $type->mau_sac }}-100 text-{{ $type->mau_sac }}-800">
                                {{ $colors[$type->mau_sac] ?? $type->mau_sac }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-xs">
                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded-full">
                                {{ $type->notes()->count() }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-right text-xs">
                            @if(!$type->la_mac_dinh)
                                <button wire:click="openEditModal({{ $type->id }})" class="text-yellow-600 hover:text-yellow-900 mr-2">Sửa</button>
                                <button wire:click="delete({{ $type->id }})" wire:confirm="Xóa loại này?" class="text-red-600 hover:text-red-900">Xóa</button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-sm">Chưa có loại ghi chú nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
