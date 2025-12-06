<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Điểm bán</h2>
        <div class="flex gap-3">
            <button wire:click="exportExcel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Xuất Excel
            </button>
            <a href="{{ route('admin.agencies.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm điểm bán
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="mb-4 max-w-lg"><x-search-bar placeholder="Tìm theo tên, mã, địa chỉ..." /></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="trangThai" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Tất cả</option>
                    <option value="hoat_dong">Hoạt động</option>
                    <option value="dong_cua">Đóng cửa</option>
                </select>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('message') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden relative">
        <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
            <svg class="w-6 h-6 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <x-sort-icon field="ma_diem_ban" :currentField="$sortField" :direction="$sortDirection">
                            <span class="text-xs font-medium text-gray-500 uppercase">Mã ĐB</span>
                        </x-sort-icon>
                    </th>
                    <th class="px-6 py-3 text-left">
                        <x-sort-icon field="ten_diem_ban" :currentField="$sortField" :direction="$sortDirection">
                            <span class="text-xs font-medium text-gray-500 uppercase">Tên điểm bán</span>
                        </x-sort-icon>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Địa chỉ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại ĐL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($agencies as $agency)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-indigo-600">{{ $agency->ma_diem_ban }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $agency->ten_diem_ban }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $agency->dia_chi }}</td>
                        <td class="px-6 py-4">
                            @if($agency->loai_dai_ly === 'rieng_tu')
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    🏠 Riêng tư
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                                    📍 Vỉa hè
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $agency->trang_thai === 'hoat_dong' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $agency->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Đóng cửa' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.agencies.edit', $agency->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</a>
                            <button wire:click="delete({{ $agency->id }})" wire:confirm="Xóa điểm bán?" class="text-red-600 hover:text-red-900">Xóa</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Không có điểm bán nào</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            <x-pagination-controls :paginator="$agencies" />
        </div>
    </div>
</div>
