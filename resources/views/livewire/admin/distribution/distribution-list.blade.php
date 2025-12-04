<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Danh Sách Phân Bổ Hàng Hóa</h2>
            <a href="{{ route('admin.distribution.daily') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tạo Phân Bổ Hôm Nay
            </a>
        </div>

        <!-- Filters -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <label class="text-sm font-medium text-gray-700 mr-2">Lọc theo ngày:</label>
                <input type="date" wire:model.live="dateFilter" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            @if($dateFilter)
                <button wire:click="$set('dateFilter', null)" class="text-sm text-red-600 hover:text-red-800">
                    Xóa lọc
                </button>
            @endif
        </div>
    </div>

    <div class="space-y-4">
        @forelse($dates as $dateRecord)
            @php
                $date = $dateRecord->ngay_xuat;
                $batches = $groupedBatches[$date] ?? collect();
                $totalQty = $batches->sum('tong_so_luong');
                $batchCount = $batches->count();
            @endphp
            
            <div x-data="{ open: false }" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Date Header (Clickable) -->
                <div @click="open = !open" class="px-6 py-4 bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <span class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="hidden sm:flex space-x-2">
                            <span class="px-2 py-1 text-xs font-semibold bg-indigo-100 text-indigo-800 rounded-full">
                                {{ $batchCount }} Mẻ hàng
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                Tổng: {{ number_format($totalQty) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center text-gray-500">
                        <span class="text-sm mr-2" x-show="!open">Xem chi tiết</span>
                        <span class="text-sm mr-2" x-show="open">Thu gọn</span>
                        <svg class="w-5 h-5 transform transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Batches List (Dropdown) -->
                <div x-show="open" class="border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mẻ Hàng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số Lượng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người Tạo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($batches as $dist)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-medium">
                                        {{ $dist->ten_me_hang ?? 'Mặc định' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        {{ number_format($dist->tong_so_luong) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $dist->nguoiXuat->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $dist->trang_thai === 'hoan_thanh' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $dist->trang_thai === 'hoan_thanh' ? 'Hoàn thành' : 'Đang xử lý' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-3">
                                            <a href="{{ route('admin.distribution.daily', ['date' => $dist->ngay_xuat, 'batch_id' => $dist->id]) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md">
                                                Xem / Sửa
                                            </a>
                                            
                                            @if($dist->phan_bo_count == 0)
                                                <button wire:click="delete({{ $dist->id }})" 
                                                        wire:confirm="Bạn có chắc chắn muốn xóa mẻ hàng này không? Hành động này không thể hoàn tác."
                                                        class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md"
                                                        title="Xóa mẻ hàng">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-gray-400 cursor-not-allowed" title="Đã phân bổ, không thể xóa">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-lg p-12 text-center text-gray-500">
                Chưa có dữ liệu phân bổ nào.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $dates->links() }}
    </div>
</div>
