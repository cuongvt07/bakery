<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Duyệt yêu cầu ca làm</h2>
    </div>

    @if(session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-amber-50 rounded-lg shadow-sm p-4 border-l-4 border-amber-500">
            <div class="text-3xl font-bold text-amber-600">{{ $stats['pending'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Chờ duyệt</div>
        </div>
        
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Đã duyệt hôm nay</div>
        </div>
        
        <div class="bg-red-50 rounded-lg shadow-sm p-4 border-l-4 border-red-500">
            <div class="text-3xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Từ chối hôm nay</div>
        </div>
        
        <div class="bg-blue-50 rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_today'] }}</div>
            <div class="text-sm text-gray-600 mt-1">Yêu cầu hôm nay</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="mb-4">
            <x-search-bar placeholder="Tìm theo tên, mã nhân viên..." />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="filterStatus" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Tất cả</option>
                    <option value="cho_duyet">Chờ duyệt</option>
                    <option value="da_duyet">Đã duyệt</option>
                    <option value="tu_choi">Từ chối</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Loại yêu cầu</label>
                <select wire:model.live="filterType" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Tất cả</option>
                    <option value="xin_ca">Xin ca</option>
                    <option value="doi_ca">Đổi ca</option>
                    <option value="xin_nghi">Xin nghỉ</option>
                </select>
            </div>

            <div class="flex items-end">
                <x-reset-button wire:click="setFilter('')" />
            </div>
        </div>
    </div>

    {{-- Requests Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại yêu cầu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày mong muốn</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lý do</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày tạo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requests as $request)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $request->user->ho_ten }}</div>
                        <div class="text-xs text-gray-500">{{ $request->user->ma_nhan_vien }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ match($request->loai_yeu_cau) {
                            'xin_ca' => 'bg-blue-100 text-blue-800',
                            'doi_ca' => 'bg-amber-100 text-amber-800',
                            'xin_nghi' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100'
                        } }}">
                            {{ match($request->loai_yeu_cau) {
                                'xin_ca' => '➕ Xin ca',
                                'doi_ca' => '🔄 Đổi ca',
                                'xin_nghi' => '❌ Xin nghỉ',
                                default => $request->loai_yeu_cau
                            } }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $request->ngay_mong_muon ? \Carbon\Carbon::parse($request->ngay_mong_muon)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 max-w-xs truncate">{{ $request->ly_do }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ match($request->trang_thai) {
                            'cho_duyet' => 'bg-yellow-100 text-yellow-800',
                            'da_duyet' => 'bg-green-100 text-green-800',
                            'tu_choi' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100'
                        } }}">
                            {{ match($request->trang_thai) {
                                'cho_duyet' => '⏳ Chờ duyệt',
                                'da_duyet' => '✅ Đã duyệt',
                                'tu_choi' => '❌ Từ chối',
                                default => $request->trang_thai
                            } }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $request->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="openDetailModal({{ $request->id }})" class="text-indigo-600 hover:text-indigo-900">
                            Chi tiết
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        Không có yêu cầu nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t">
            <x-pagination-controls :paginator="$requests" />
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedRequest)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Chi tiết yêu cầu</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                {{-- Employee Info --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">👤 Nhân viên</h4>
                    <div class="text-sm text-gray-700">
                        <p><strong>Họ tên:</strong> {{ $selectedRequest->user->ho_ten }}</p>
                        <p><strong>Mã NV:</strong> {{ $selectedRequest->user->ma_nhan_vien }}</p>
                        <p><strong>SĐT:</strong> {{ $selectedRequest->user->so_dien_thoai }}</p>
                    </div>
                </div>

                {{-- Request Info --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">📋 Thông tin yêu cầu</h4>
                    <div class="text-sm space-y-2">
                        <p><strong>Loại:</strong> 
                            <span class="px-2 py-1 rounded-full text-xs {{ match($selectedRequest->loai_yeu_cau) {
                                'xin_ca' => 'bg-blue-100 text-blue-800',
                                'doi_ca' => 'bg-amber-100 text-amber-800',
                                'xin_nghi' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100'
                            } }}">
                                {{ match($selectedRequest->loai_yeu_cau) {
                                    'xin_ca' => 'Xin ca',
                                    'doi_ca' => 'Đổi ca',
                                    'xin_nghi' => 'Xin nghỉ',
                                    default => $selectedRequest->loai_yeu_cau
                                } }}
                            </span>
                        </p>
                        @if($selectedRequest->ngay_mong_muon)
                        <p><strong>Ngày mong muốn:</strong> {{ \Carbon\Carbon::parse($selectedRequest->ngay_mong_muon)->format('d/m/Y') }}</p>
                        @endif
                        @if($selectedRequest->gio_bat_dau)
                        <p><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($selectedRequest->gio_bat_dau)->format('H:i') }} - {{ \Carbon\Carbon::parse($selectedRequest->gio_ket_thuc)->format('H:i') }}</p>
                        @endif
                        <p><strong>Lý do:</strong></p>
                        <div class="p-3 bg-gray-50 rounded">{{ $selectedRequest->ly_do }}</div>
                        <p><strong>Ngày tạo:</strong> {{ $selectedRequest->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($selectedRequest->trang_thai !== 'cho_duyet')
                    {{-- Already processed --}}
                    <div class="bg-{{ $selectedRequest->trang_thai === 'da_duyet' ? 'green' : 'red' }}-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">📝 Kết quả duyệt</h4>
                        <p class="text-sm"><strong>Người duyệt:</strong> {{ $selectedRequest->approver->ho_ten ?? 'N/A' }}</p>
                        <p class="text-sm"><strong>Ngày duyệt:</strong> {{ $selectedRequest->ngay_duyet?->format('d/m/Y H:i') }}</p>
                        <p class="text-sm"><strong>Ghi chú:</strong> {{ $selectedRequest->ghi_chu_duyet }}</p>
                    </div>
                @else
                    {{-- Approval/Rejection Form --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú duyệt (tuỳ chọn khi duyệt, bắt buộc khi từ chối)</label>
                        <textarea wire:model="approvalNote" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Nhập ghi chú..."></textarea>
                        @error('approvalNote') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="approveRequest" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            ✅ Duyệt
                        </button>
                        <button wire:click="rejectRequest" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            ❌ Từ chối
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
