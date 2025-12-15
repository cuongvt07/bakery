<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Quản lý Nhân viên</h2>
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
                Thêm nhân viên
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="mb-4 max-w-lg">
            <x-search-bar placeholder="Tìm theo mã NV, tên, email, SĐT..." />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Loại hợp đồng</label>
                <select wire:model.live="loaiHopDong" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Tất cả</option>
                    <option value="thu_viec">Thử việc</option>
                    <option value="chinh_thuc">Chính thức</option>
                    <option value="hop_tac">Hợp tác</option>
                </select>
            </div>
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
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã NV</th>
                        <th class="px-4 py-3 text-left">
                            <x-sort-icon field="ho_ten" :currentField="$sortField" :direction="$sortDirection">
                                <span class="text-xs font-medium text-gray-500 uppercase">Họ tên</span>
                            </x-sort-icon>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liên hệ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại HĐ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lương TV</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lương CT</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại lương</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày ký HĐ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hết hạn HĐ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-mono text-gray-900">
                                {{ $user->vai_tro === 'admin' ? '-' : ($user->ma_nhan_vien ?? '-') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $user->ho_ten }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <div>📞 {{ $user->so_dien_thoai ?? '-' }}</div>
                                @if($user->facebook && $user->vai_tro !== 'admin')
                                    <a href="{{ $user->facebook }}" target="_blank" class="text-blue-600 hover:underline text-xs">
                                        👤 Facebook
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user->loai_hop_dong && $user->vai_tro !== 'admin')
                                    <span class="px-2 py-1 text-xs rounded-full {{ match($user->loai_hop_dong) {
                                        'chinh_thuc' => 'bg-green-100 text-green-800',
                                        'thu_viec' => 'bg-yellow-100 text-yellow-800',
                                        'hop_tac' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    } }}">
                                        {{ match($user->loai_hop_dong) {
                                            'chinh_thuc' => 'Chính thức',
                                            'thu_viec' => 'Thử việc',
                                            'hop_tac' => 'Hợp tác',
                                            default => '-',
                                        } }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $user->vai_tro === 'admin' ? '-' : ($user->luong_thu_viec ? number_format($user->luong_thu_viec, 0, ',', '.') . 'đ' : '-') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-green-600">
                                {{ $user->vai_tro === 'admin' ? '-' : ($user->luong_chinh_thuc ? number_format($user->luong_chinh_thuc, 0, ',', '.') . 'đ' : '-') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($user->vai_tro === 'admin')
                                    -
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-50 text-indigo-700">
                                        {{ match($user->loai_luong) {
                                            'theo_ngay' => 'Theo ngày',
                                            'theo_gio' => 'Theo giờ',
                                            default => '-',
                                        } }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $user->vai_tro === 'admin' ? '-' : ($user->ngay_ky_hop_dong?->format('d/m/Y') ?? '-') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($user->vai_tro === 'admin')
                                    -
                                @elseif($user->ngay_het_han_hop_dong)
                                    <div class="flex flex-col">
                                        <span class="text-gray-900">{{ $user->ngay_het_han_hop_dong->format('d/m/Y') }}</span>
                                        @if($user->isContractExpired())
                                            <span class="text-xs text-red-600 font-medium">⚠️ Hết hạn</span>
                                        @elseif($user->ngay_het_han_hop_dong->diffInDays(now()) <= 30)
                                            <span class="text-xs text-orange-600">Sắp hết</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->vai_tro === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $user->vai_tro === 'admin' ? 'Admin' : 'NV' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Sửa</a>
                                <button wire:click="delete({{ $user->id }})" wire:confirm="Xóa nhân viên này?" class="text-red-600 hover:text-red-900">Xóa</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="px-6 py-12 text-center text-gray-500">Không có nhân viên nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            <x-pagination-controls :paginator="$users" />
        </div>
    </div>
</div>
