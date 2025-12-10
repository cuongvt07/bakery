<!-- Filters & Search -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full px-3 py-2 text-sm border rounded-lg" placeholder="Tìm theo mã, tên...">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Đại lý</label>
            <select wire:model.live="filterAgency" class="w-full px-3 py-2 text-sm border rounded-lg">
                <option value="">-- Tất cả --</option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button wire:click="clearFilters" class="w-full px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg">Xóa bộ lọc</button>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-lg border overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32"><button wire:click="sortBy('ma_vi_tri')" class="flex items-center gap-1 hover:text-gray-700">Mã @if($sortField === 'ma_vi_tri')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"><button wire:click="sortBy('ten_vi_tri')" class="flex items-center gap-1 hover:text-gray-700">Tên vị trí @if($sortField === 'ten_vi_tri')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48"><button wire:click="sortBy('dai_ly')" class="flex items-center gap-1 hover:text-gray-700">Đại lý @if($sortField === 'dai_ly')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Địa chỉ</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($locations as $location)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-mono font-semibold text-indigo-600">{{ $location->ma_vi_tri }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $location->ten_vi_tri }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $location->agency ? $location->agency->ten_diem_ban : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $location->mo_ta ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $location->dia_chi ?? '-' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                        <button wire:click="openEditLocationModal({{ $location->id }})" class="text-yellow-600 hover:text-yellow-900 mr-2">Sửa</button>
                        <button wire:click="deleteLocation({{ $location->id }})" wire:confirm="Xóa vị trí này?" class="text-red-600 hover:text-red-900">Xóa</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        @if($search || $filterAgency)
                            Không tìm thấy vị trí nào phù hợp với bộ lọc
                        @else
                            Chưa có vị trí nào
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($locations && $locations->hasPages())
        <div class="px-4 py-3 border-t bg-gray-50">{{ $locations->links() }}</div>
    @endif
</div>
@if($locations)
    <div class="mt-4 text-sm text-gray-600">Hiển thị {{ $locations->count() }} / {{ $locations->total() }} vị trí</div>
@endif
