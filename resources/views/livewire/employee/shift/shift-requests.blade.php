<div class="p-4 space-y-4">
    {{-- Header --}}
    {{-- Header --}}
    <div class="flex items-center gap-3 pb-2">
         <a href="{{ route('employee.shifts.schedule') }}" class="p-2 bg-white rounded-lg shadow-sm border border-gray-100 text-gray-500 hover:text-indigo-600 active:scale-95 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Yêu cầu của tôi</h1>
            <p class="text-gray-500 text-xs">Quản lý các yêu cầu thay đổi ca</p>
        </div>
    </div>

    @if(session('message'))
    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl">
        {{ session('message') }}
    </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
        <button wire:click="setFilter('')" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap {{ $filterStatus === '' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-700' }}">
            Tất cả
        </button>
        <button wire:click="setFilter('cho_duyet')" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap {{ $filterStatus === 'cho_duyet' ? 'bg-amber-500 text-white' : 'bg-white border border-gray-200 text-gray-700' }}">
            Chờ duyệt
        </button>
        <button wire:click="setFilter('da_duyet')" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap {{ $filterStatus === 'da_duyet' ? 'bg-green-600 text-white' : 'bg-white border border-gray-200 text-gray-700' }}">
            Đã duyệt
        </button>
        <button wire:click="setFilter('tu_choi')" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap {{ $filterStatus === 'tu_choi' ? 'bg-red-600 text-white' : 'bg-white border border-gray-200 text-gray-700' }}">
            Từ chối
        </button>
    </div>

    {{-- Create New Request Button --}}
    <button wire:click="openRequestModal('xin_ca')" class="w-full btn-mobile bg-indigo-600 text-white shadow-md hover:bg-indigo-700 active:scale-95 transition-transform">
        ➕ Tạo yêu cầu mới
    </button>

    {{-- Requests List --}}
    <div class="space-y-3">
        @forelse($requests as $request)
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ match($request->loai_yeu_cau) {
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
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ match($request->trang_thai) {
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
            </div>

            @if($request->ngay_mong_muon)
            <div class="text-sm text-gray-600 mb-2">
                📅 {{ \Carbon\Carbon::parse($request->ngay_mong_muon)->format('d/m/Y') }}
            </div>
            @endif

            <div class="text-sm text-gray-700 mb-3">
                <strong>Lý do:</strong> {{ $request->ly_do }}
            </div>

            @if($request->ghi_chu_duyet)
            <div class="p-3 bg-gray-50 rounded-lg text-sm">
<strong>Phản hồi:</strong> {{ $request->ghi_chu_duyet }}
            </div>
            @endif

            <div class="flex items-center justify-between mt-3 pt-3 border-t">
                <div class="text-xs text-gray-500">
                    {{ $request->created_at->diffForHumans() }}
                </div>
                @if($request->trang_thai === 'cho_duyet')
                <button wire:click="cancelRequest({{ $request->id }})" wire:confirm="Hủy yêu cầu này?" class="text-red-600 text-sm font-medium">
                    Hủy yêu cầu
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-gray-50 rounded-2xl p-8 text-center">
            <div class="text-4xl mb-2">📋</div>
            <p class="text-gray-600 mb-4">Chưa có yêu cầu nào</p>
            <button wire:click="openRequestModal('xin_ca')" class="btn-mobile bg-indigo-600 text-white">
                Tạo yêu cầu đầu tiên
            </button>
        </div>
        @endforelse
    </div>

    {{-- Request Modal --}}
    @if($showRequestModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end md:items-center justify-center p-4">
        <div class="bg-white rounded-t-2xl md:rounded-2xl w-full md:max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Tạo yêu cầu mới</h3>
                <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                {{-- Request Type Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại yêu cầu</label>
                    <select wire:model="requestType" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="xin_ca">➕ Xin ca làm việc</option>
                        <option value="doi_ca">🔄 Đổi ca</option>
                        <option value="xin_nghi">❌ Xin nghỉ</option>
                    </select>
                </div>

                @if($requestType === 'xin_ca')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày mong muốn</label>
                    <input type="date" wire:model="requestDate" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lý do *</label>
                    <textarea 
                        wire:model="requestNote" 
                        rows="4" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                        placeholder="Nhập lý do chi tiết (ít nhất 10 ký tự)..."></textarea>
                    @error('requestNote') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <button wire:click="submitRequest" class="w-full btn-mobile bg-indigo-600 text-white">
                    Gửi yêu cầu
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
