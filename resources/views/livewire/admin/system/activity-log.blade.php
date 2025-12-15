<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Nhật Ký Hoạt Động</h2>
            <p class="text-sm text-gray-500 mt-1">Theo dõi lịch sử thao tác của nhân viên</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Tìm kiếm mô tả...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hành động</label>
                <select wire:model.live="actionFilter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Tất cả hành động</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}">{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Người dùng</label>
                <select wire:model.live="userFilter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Tất cả người dùng</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->ho_ten }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người thực hiện</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->nguoiDung->ho_ten ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $log->hanh_dong }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $log->mo_ta }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($log->du_lieu_moi || $log->du_lieu_cu)
                                    <pre class="text-xs bg-gray-50 p-2 rounded">{{ json_encode($log->du_lieu_moi ?? $log->du_lieu_cu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Không có dữ liệu nhật ký
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
