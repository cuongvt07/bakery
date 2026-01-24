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
                            @foreach($products as $p)
                                <div class="p-3 bg-white">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-bold text-gray-900">{{ $p->ten_san_pham }}</span>
                                        <span class="text-xs text-gray-500">{{ number_format($p->gia_ban/1000) }}k</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-12 gap-2 items-center text-sm">
                                        <!-- Breakdown -->
                                        <div class="col-span-8 flex space-x-3 text-xs text-gray-600 bg-gray-50 p-2 rounded">
                                            <div>
                                                <span class="block text-gray-400">Từ xưởng</span>
                                                <span class="font-semibold text-blue-600">{{ $stockSources[$p->id]['distribution'] ?? 0 }}</span>
                                            </div>
                                            <div class="border-l border-gray-300 pl-3">
                                                <span class="block text-gray-400">Ca trước</span>
                                                <span class="font-semibold text-orange-600">{{ $stockSources[$p->id]['handover'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Total Input -->
                                        <div class="col-span-4">
                                            <label class="block text-[10px] text-center text-gray-400 mb-0.5">TỔNG NHẬN</label>
                                            <input type="number" inputmode="numeric" wire:model="receivedStock.{{ $p->id }}" class="block w-full text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold text-gray-900" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
                
                <a href="{{ route('admin.dashboard') }}" class="inline-block bg-white border border-green-300 text-green-700 font-medium py-2 px-4 rounded-lg hover:bg-green-50">
                    Về Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
