<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-indigo-600 px-4 py-4 shadow-md sticky top-0 z-10">
        <div class="flex items-center justify-between text-white">
            <h1 class="text-lg font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Check-in Đầu Ca
            </h1>
            <span class="text-xs bg-indigo-500 px-2 py-1 rounded-full">{{ now()->format('d/m') }}</span>
        </div>
    </div>

    <div class="p-4 space-y-6">
        <!-- STATE 1: NO ACTIVE SHIFT -->
        @if(!$hasActiveShift)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="mb-4">
                    <div class="mx-auto h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Bắt đầu ca làm việc</h2>
                <p class="text-gray-500 mb-6">Bạn chưa có ca làm việc nào đang hoạt động. Hãy bắt đầu ca mới ngay.</p>
                
                @if(session()->has('error'))
                    <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <button wire:click="startShift" wire:loading.attr="disabled" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg flex items-center justify-center">
                    <span wire:loading.remove wire:target="startShift">BẮT ĐẦU CA MỚI</span>
                    <span wire:loading wire:target="startShift">Đang xử lý...</span>
                </button>
            </div>
        
        <!-- STATE 2: ACTIVE SHIFT BUT NOT CHECKED IN -->
        @elseif(!$isCheckedIn)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-100 flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="font-bold text-yellow-800">Xác nhận nhận ca</h2>
                </div>
                
                <div class="p-4 space-y-6">
                    <!-- 1. Tiền mặt đầu ca -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiền mặt đầu ca <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" inputmode="numeric" wire:model="openingCash" class="block w-full pl-4 pr-12 py-3 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-lg font-bold" placeholder="0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">đ</span>
                            </div>
                        </div>
                        @error('openingCash') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Xác nhận hàng hóa -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Xác nhận số lượng bánh nhận <span class="text-red-500">*</span></label>
                        <div class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200">
                            @forelse($products as $p)
                                <div class="p-3 flex items-center justify-between">
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900">{{ $p->ten_san_pham }}</span>
                                        <div class="text-xs text-gray-500">{{ number_format($p->gia_ban) }}đ</div>
                                    </div>
                                    <div class="w-24">
                                        <input type="number" inputmode="numeric" wire:model="receivedStock.{{ $p->id }}" class="block w-full text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold" placeholder="0">
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    Không có hàng phân bổ trong ca này
                                </div>
                            @endforelse
                        </div>
                        @error('receivedStock.*') <span class="text-red-500 text-xs">Vui lòng nhập số lượng hợp lệ</span> @enderror
                    </div>

                    <button wire:click="confirmCheckIn" wire:confirm="Xác nhận các thông tin trên là chính xác?" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg text-lg flex items-center justify-center">
                        XÁC NHẬN NHẬN CA
                    </button>
                </div>
            </div>

        <!-- STATE 3: ALREADY CHECKED IN -->
        @else
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <div class="mx-auto h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-green-800 mb-2">Đã Check-in thành công!</h2>
                <p class="text-green-600 mb-6">Bạn đang trong ca làm việc. Chúc bạn làm việc hiệu quả.</p>
                
                <a href="{{ route('employee.dashboard') }}" class="inline-block bg-white border border-green-300 text-green-700 font-medium py-2 px-4 rounded-lg hover:bg-green-50">
                    Về Trang Chủ
                </a>
            </div>
        @endif
    </div>

    <!-- SHIFT SELECTION MODAL -->
    @if($showShiftSelection)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4">
                <h3 class="text-white font-bold text-lg">Chọn Ca Làm Việc</h3>
                <p class="text-indigo-100 text-sm">Bạn có nhiều ca trong ngày hôm nay</p>
            </div>
            <div class="p-6 space-y-3">
                @foreach($todayShifts as $s)
                    <button wire:click="selectShift({{ $s->id }})" class="w-full text-left bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-lg p-4 transition-all">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-gray-900">{{ $s->agency->ten_diem_ban ?? 'Unknown Agency' }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-700">
                                {{ \Carbon\Carbon::parse($s->gio_bat_dau)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->gio_ket_thuc)->format('H:i') }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $s->shiftTemplate->name ?? 'Ca tùy chỉnh' }}
                        </div>
                    </button>
                @endforeach
                
                <button wire:click="$set('showShiftSelection', false)" class="w-full mt-4 py-3 text-gray-500 font-medium hover:text-gray-700">
                    Hủy bỏ
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
