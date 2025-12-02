<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Danh sách Người dùng</h2>
        <div class="flex gap-3">
            <button wire:click="exportExcel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Xuất Excel
            </button>
            <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm người dùng
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="mb-4 max-w-lg">
            <x-search-bar placeholder="Tìm theo tên, email, SĐT..." />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                <select wire:model.live="vaiTro" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Tất cả</option>
                    <option value="admin">Admin</option>
                    <option value="nhan_vien">Nhân viên</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="trangThai" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Tất cả</option>
                    <option value="hoat_dong">Hoạt động</option>
                    <option value="khoa">Khóa</option>
                </select>
            </div>
            <div class="flex items-end">
                <x-reset-button wire:click="resetFilters" />
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
                        <x-sort-icon field="ho_ten" :currentField="$sortField" :direction="$sortDirection">
                            <span class="text-xs font-medium text-gray-500 uppercase">Họ tên</span>
                        </x-sort-icon>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->ho_ten }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->vai_tro === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->vai_tro === 'admin' ? 'Admin' : 'Nhân viên' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->trang_thai === 'hoat_dong' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Khóa' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</a>
                            <button wire:click="delete({{ $user->id }})" wire:confirm="Xóa người dùng?" class="text-red-600 hover:text-red-900">Xóa</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Không có người dùng nào</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            <x-pagination-controls :paginator="$users" />
        </div>
    </div>
</div>
