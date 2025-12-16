<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Quản Lý Chốt Ca</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" wire:model.live="dateFrom" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" wire:model.live="dateTo" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Điểm bán</label>
                <select wire:model.live="agencyId" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Tất cả</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Điểm bán</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thực tế / Lý thuyết</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Chênh lệch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $report->ngay_chot->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($report->gio_chot)->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $report->diemBan->ten_diem_ban ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $report->nguoiChot->ho_ten ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <div class="font-medium text-gray-900">{{ number_format($report->tong_tien_thuc_te ?? 0) }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($report->tong_tien_ly_thuyet ?? 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold">
                            @if($report->tien_lech == 0)
                                <span class="text-green-600">0</span>
                            @else
                                <span class="{{ $report->tien_lech > 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    {{ $report->tien_lech > 0 ? '+' : '' }}{{ number_format($report->tien_lech) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $report->trang_thai === 'da_duyet' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $report->trang_thai }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="viewDetail({{ $report->id }})" class="text-indigo-600 hover:text-indigo-900">Chi tiết</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Không có dữ liệu chốt ca nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
    </div>

    <!-- DETAIL MODAL -->
    @if($showDetailModal && $selectedReport)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeDetail"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
                            Chi Tiết Chốt Ca - {{ $selectedReport->ma_phieu }}
                        </h3>
                        <button wire:click="closeDetail" class="text-white hover:text-gray-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Thông Tin Chung</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between"><span>Điểm bán:</span> <span class="font-medium">{{ $selectedReport->diemBan->ten_diem_ban ?? 'N/A' }}</span></div>
                                    <div class="flex justify-between"><span>Nhân viên:</span> <span class="font-medium">{{ $selectedReport->nguoiChot->ho_ten ?? 'N/A' }}</span></div>
                                    <div class="flex justify-between"><span>Thời gian:</span> <span class="font-medium">{{ $selectedReport->ngay_chot->format('d/m/Y') }} {{ \Carbon\Carbon::parse($selectedReport->gio_chot)->format('H:i') }}</span></div>
                                    <div class="flex justify-between"><span>Ghi chú:</span> <span class="italic text-gray-600">{{ $selectedReport->ghi_chu ?? '(Không có)' }}</span></div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Tài Chính</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between text-green-700"><span>Tiền mặt thực tế:</span> <span class="font-bold">{{ number_format($selectedReport->tien_mat) }}</span></div>
                                    <div class="flex justify-between text-blue-700"><span>Chuyển khoản:</span> <span class="font-bold">{{ number_format($selectedReport->tien_chuyen_khoan) }}</span></div>
                                    <div class="border-t pt-2 mt-2 flex justify-between font-bold">
                                        <span>Tổng thực tế:</span>
                                        <span>{{ number_format($selectedReport->tong_tien_thuc_te) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-500"><span>Lý thuyết:</span> <span>{{ number_format($selectedReport->tong_tien_ly_thuyet) }}</span></div>
                                    <div class="flex justify-between items-center mt-2 p-2 rounded {{ $selectedReport->tien_lech != 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        <span class="font-bold">Chênh lệch:</span>
                                        <span class="font-bold text-lg">{{ number_format($selectedReport->tien_lech) }} đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Gallery -->
                        <div class="mb-6">
                            <h4 class="font-bold text-gray-700 mb-3">Hình Ảnh Chốt Ca</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Cash Images -->
                                <div>
                                    <h5 class="text-sm font-semibold text-gray-600 mb-2">Ảnh Tiền Mặt</h5>
                                    <div class="flex gap-2 overflow-x-auto pb-2">
                                        @forelse($selectedReport->anh_tien_mat ?? [] as $img)
                                            <a href="{{ Storage::url($img) }}" target="_blank" class="block border rounded-lg hover:opacity-75 transition">
                                                <img src="{{ Storage::url($img) }}" class="h-32 w-auto object-cover rounded-lg">
                                            </a>
                                        @empty
                                            <p class="text-xs text-gray-400 italic">Không có ảnh tiền mặt</p>
                                        @endforelse
                                    </div>
                                </div>
                                
                                <!-- Stock Images -->
                                <div>
                                    <h5 class="text-sm font-semibold text-gray-600 mb-2">Ảnh Hàng Hóa</h5>
                                    <div class="flex gap-2 overflow-x-auto pb-2">
                                        @forelse($selectedReport->anh_hang_hoa ?? [] as $img)
                                            <a href="{{ Storage::url($img) }}" target="_blank" class="block border rounded-lg hover:opacity-75 transition">
                                                <img src="{{ Storage::url($img) }}" class="h-32 w-auto object-cover rounded-lg">
                                            </a>
                                        @empty
                                            <p class="text-xs text-gray-400 italic">Không có ảnh hàng hóa</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Detail Table -->
                        <!-- If needed, can query chiTietCaLam -->
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeDetail" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
