<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-indigo-600 px-4 py-4 shadow-md sticky top-0 z-10">
        <div class="flex items-center justify-between text-white">
            <h1 class="text-lg font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Check-in Đầu Ca
            </h1>
            <span class="text-xs bg-indigo-500 px-2 py-1 rounded-full">{{ now()->format('d/m') }}</span>
        </div>
    </div>

    <div class="p-4 space-y-6">
        {{-- UNCLOSED SHIFT PROMPT --}}
        @if ($showCheckoutPrompt && $unclosedShift)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                    <div class="{{ $isLateCheckout ? 'bg-red-600' : 'bg-orange-600' }} px-6 py-4">
                        <h3 class="text-white font-bold text-lg flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            {{ $isLateCheckout ? 'Chốt ca muộn' : 'Cần chốt ca' }}
                        </h3>
                        <p class="{{ $isLateCheckout ? 'text-red-100' : 'text-orange-100' }} text-sm mt-1">
                            Bạn cần chốt ca trước đó trước khi bắt đầu ca mới
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="text-sm text-gray-600 mb-2">Ca chưa chốt:</div>
                            <div class="font-bold text-gray-900">{{ $unclosedShift->ngay_lam->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($unclosedShift->gio_bat_dau)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($unclosedShift->gio_ket_thuc)->format('H:i') }}
                            </div>
                        </div>

                        @if ($isLateCheckout)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-sm text-red-800">
                                        <div class="font-semibold">Chốt ca muộn (quá 30 phút)</div>
                                        <div>Phiếu chốt ca sẽ được ghi chú: "Quên chốt ca"</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <button wire:click="forceCheckout"
                            class="w-full {{ $isLateCheckout ? 'bg-red-600 hover:bg-red-700' : 'bg-orange-600 hover:bg-orange-700' }} text-white font-bold py-3 rounded-lg mb-2">
                            Xác nhận chốt ca
                        </button>
                        <button wire:click="$set('showCheckoutPrompt', false)"
                            class="w-full bg-gray-100 text-gray-700 font-medium py-3 rounded-lg hover:bg-gray-200">
                            Hủy
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- STATE 1: NO ACTIVE SHIFT -->
        @if (!$hasActiveShift)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                @if ($hasRegisteredShifts)
                    {{-- Has registered shifts - show start button --}}
                    <div class="mb-4">
                        <div class="mx-auto h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Bắt đầu ca làm việc</h2>
                    <p class="text-gray-500 mb-6">Bạn chưa có ca làm việc nào đang hoạt động. Hãy bắt đầu ca mới ngay.
                    </p>

                    @if (session()->has('error'))
                        <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <button wire:click="startShift" wire:loading.attr="disabled"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg flex items-center justify-center">
                        <span wire:loading.remove wire:target="startShift">BẮT ĐẦU CA MỚI</span>
                        <span wire:loading wire:target="startShift">Đang xử lý...</span>
                    </button>
                @else
                    {{-- No registered shifts - show rest message --}}
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl">💤</span>
                    </div>
                    <p class="text-gray-900 font-medium">Hôm nay nghỉ ngơi</p>
                    <p class="text-sm text-gray-500 mt-1">Không có ca làm việc nào được xếp</p>
                    <a href="{{ route('employee.shifts.register') }}" wire:navigate
                        class="mt-3 inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
                        Đăng ký ca
                    </a>
                @endif
            </div>

            <!-- STATE 2: ACTIVE SHIFT BUT NOT CHECKED IN -->
        @elseif(!$isCheckedIn)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-100 flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="font-bold text-yellow-800">Xác nhận nhận ca</h2>
                </div>

                <div class="p-4 space-y-6">
                    {{-- Check-in type --}}
                    @php
                        $checkinType = Auth::user()->getCheckinType();
                    @endphp

                    {{-- 1. Tiền mặt đầu ca - CHỈ cho Sales --}}
                    @if ($checkinType === 'sales')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiền mặt đầu ca <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" inputmode="numeric" wire:model="openingCash"
                                    class="block w-full pl-4 pr-12 py-3 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-lg font-bold"
                                    placeholder="0">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">đ</span>
                                </div>
                            </div>
                            @error('openingCash')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- 2. Hình ảnh check-in -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh check-in (Có thể chọn
                            nhiều) <span class="text-red-500">*</span></label>
                        <div class="flex items-center justify-center w-full">
                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="text-xs text-center text-gray-500">Chạm để chụp/tải ảnh</p>
                                </div>
                                <input type="file" wire:model="checkinImages" class="hidden" accept="image/*"
                                    multiple capture="environment">
                            </label>
                        </div>

                        @if ($checkinImages)
                            <div class="mt-3 flex gap-2 overflow-x-auto py-2">
                                @foreach ($checkinImages as $index => $photo)
                                    <div class="relative flex-shrink-0 group">
                                        <img src="{{ $photo->temporaryUrl() }}"
                                            class="h-20 w-20 object-cover rounded-lg border border-gray-200 shadow-sm">
                                        <button type="button" wire:click="deleteImage({{ $index }})"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @error('checkinImages')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                        @error('checkinImages.*')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- 3. Xác nhận hàng hóa - CHỈ cho Sales --}}
                    @if ($checkinType === 'sales')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Xác nhận số lượng bánh nhận
                                <span class="text-red-500">*</span></label>
                            <div class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200">
                                @forelse($products as $p)
                                    <div class="p-3 flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="font-medium text-gray-900 block truncate">{{ $p->ten_san_pham }}</span>
                                            <div class="text-xs text-gray-500">{{ number_format($p->gia_ban) }}đ</div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="relative w-24">
                                                <input type="number" inputmode="numeric"
                                                    wire:model="receivedStock.{{ $p->id }}"
                                                    class="block w-full text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold"
                                                    placeholder="0">
                                            </div>
                                            <button wire:click="fillMaxStock({{ $p->id }})"
                                                class="px-2 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold hover:bg-indigo-200 active:scale-95 transition-transform whitespace-nowrap">
                                                Max ({{ $maxStock[$p->id] ?? 0 }})
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-gray-500 text-sm">
                                        Không có hàng phân bổ trong ca này
                                    </div>
                                @endforelse
                            </div>
                            @error('receivedStock.*')
                                <span class="text-red-500 text-xs">Vui lòng nhập số lượng hợp lệ</span>
                            @enderror
                        </div>
                    @endif

                    {{-- 4. Ghi chú ca làm việc --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú ca làm việc</label>
                        <textarea wire:model="ghi_chu" rows="3"
                            class="block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                            placeholder="Nhập ghi chú cho ca làm việc (nếu có)..."></textarea>
                    </div>

                    <button wire:click="confirmCheckIn" wire:confirm="Xác nhận các thông tin trên là chính xác?"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg text-lg flex items-center justify-center">
                        XÁC NHẬN NHẬN CA
                    </button>
                </div>
            </div>

            <!-- STATE 3: ALREADY CHECKED IN -->
        @else
            @php
                $checkinType = Auth::user()->getCheckinType();
            @endphp

            {{-- Auto-redirect sales staff to POS immediately --}}
            @if ($checkinType === 'sales')
                <script>
                    window.location.href = "{{ route('employee.pos') }}";
                </script>
            @endif

            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <div class="mx-auto h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-green-800 mb-2">Đã Check-in thành công!</h2>
                <p class="text-green-600 mb-6">Bạn đang trong ca làm việc. Chúc bạn làm việc hiệu quả.</p>

                <div class="flex flex-col gap-3">
                    @if ($checkinType === 'sales')
                        {{-- Sales: Go to POS --}}
                        <a href="{{ route('employee.pos') }}"
                            class="inline-block w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:bg-indigo-700">
                            Vào màn hình Bán Hàng
                        </a>
                    @else
                        {{-- Production & Office: Checkout button --}}
                        <button wire:click="initiateCheckout"
                            class="w-full bg-orange-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:bg-orange-700">
                            Chốt ca
                        </button>
                    @endif

                    <a href="{{ route('employee.dashboard') }}"
                        class="inline-block w-full bg-white border border-green-300 text-green-700 font-medium py-3 px-4 rounded-xl hover:bg-green-50">
                        Về Trang Chủ
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- SHIFT SELECTION MODAL -->
    @if ($showShiftSelection)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="bg-indigo-600 px-6 py-4">
                    <h3 class="text-white font-bold text-lg">Chọn Ca Làm Việc</h3>
                    <p class="text-indigo-100 text-sm">Bạn có nhiều ca trong ngày hôm nay</p>
                </div>
                <div class="p-6 space-y-3">
                    @foreach ($todayShifts as $s)
                        @php
                            // Check if shift has checkout record (phieu chot ca)
                            $isCompleted = $s->phieuChotCa !== null;
                        @endphp

                        <button wire:click="selectShift({{ $s->id }})"
                            class="w-full text-left rounded-lg p-4 transition-all {{ $isCompleted ? 'bg-gray-100 opacity-50 cursor-not-allowed' : 'bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300' }}"
                            {{ $isCompleted ? 'disabled' : '' }}>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold {{ $isCompleted ? 'text-gray-400' : 'text-gray-900' }}">
                                    {{ $s->agency->ten_diem_ban ?? 'Unknown Agency' }}
                                </span>
                                <span
                                    class="text-xs font-bold px-2 py-0.5 rounded {{ $isCompleted ? 'bg-gray-200 text-gray-500' : 'bg-blue-100 text-blue-700' }}">
                                    {{ \Carbon\Carbon::parse($s->gio_bat_dau)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($s->gio_ket_thuc)->format('H:i') }}
                                </span>
                            </div>
                            <div class="text-sm {{ $isCompleted ? 'text-gray-400' : 'text-gray-600' }}">
                                {{ $s->shiftTemplate->name ?? 'Ca tùy chỉnh' }}
                                @if ($isCompleted)
                                    <span class="ml-2 text-xs font-semibold text-red-500">✓ Đã chốt ca</span>
                                @endif
                            </div>
                        </button>
                    @endforeach

                    {{-- Ghi chú check-in --}}
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú (tùy chọn)</label>
                        <textarea wire:model="ghi_chu" rows="3" placeholder="Nhập ghi chú về tình trạng đầu ca..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none text-sm"></textarea>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- CHECKOUT CONFIRMATION MODAL --}}
    @if ($showCheckoutConfirm)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div
                    class="{{ $checkoutWarningType === 'late' ? 'bg-red-600' : ($checkoutWarningType === 'early' ? 'bg-yellow-600' : 'bg-green-600') }} px-6 py-4">
                    <h3 class="text-white font-bold text-lg flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if ($checkoutWarningType === 'late' || $checkoutWarningType === 'early')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @endif
                        </svg>
                        {{ $checkoutWarningType === 'late' ? 'Chốt ca muộn' : ($checkoutWarningType === 'early' ? 'Chốt ca sớm' : 'Xác nhận chốt ca') }}
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-4">{{ $checkoutWarningMessage }}</p>

                    {{-- OT Checkbox --}}
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="isOvertime"
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                <span class="font-bold">Tăng ca (OT)</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Đánh dấu nếu ca này là tăng ca</span>
                            </span>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="$set('showCheckoutConfirm', false)"
                            class="flex-1 bg-gray-100 text-gray-700 font-medium py-3 rounded-lg hover:bg-gray-200">
                            Hủy
                        </button>
                        <button wire:click="confirmCheckout"
                            class="flex-1 {{ $checkoutWarningType === 'late' ? 'bg-red-600 hover:bg-red-700' : ($checkoutWarningType === 'early' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700') }} text-white font-bold py-3 rounded-lg">
                            Xác nhận
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
