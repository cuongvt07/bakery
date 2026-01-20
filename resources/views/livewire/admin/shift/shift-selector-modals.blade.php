{{-- SHIFT SELECTOR MODAL - Add this after the header section in shift-closing.blade.php --}}
@if ($showShiftSelector && count($unclosedShifts) > 0)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4">
                <h3 class="text-white font-bold text-lg">Chọn Ca Cần Chốt</h3>
                <p class="text-indigo-100 text-sm">Bạn có {{ count($unclosedShifts) }} ca hôm nay chưa chốt</p>
            </div>
            <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                @foreach ($unclosedShifts as $shift)
                    <button wire:click="selectShiftToClose({{ $shift->id }})"
                        class="w-full text-left rounded-lg p-4 border-2 border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition-all">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-gray-900">
                                Ca {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($shift->gio_ket_thuc)->format('H:i') }}
                            </span>
                            @php
                                $expectedCheckout = $shift->expected_checkout_time;
                                $isLate = now()->gt($expectedCheckout->copy()->addMinutes(15));
                            @endphp
                            @if ($isLate)
                                <span class="text-xs font-bold px-2 py-1 rounded bg-red-100 text-red-700">
                                    ⚠️ Muộn
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">
                            Check-in: {{ $shift->thoi_gian_checkin->format('H:i') }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Dự kiến chốt: {{ $expectedCheckout->format('H:i') }}
                            @if ($isLate)
                                <span class="text-red-600 font-medium">
                                    (Quá {{ $expectedCheckout->diffForHumans(null, true) }})
                                </span>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- OLDER SHIFT WARNING MODAL - Add this after shift selector modal --}}
@if ($hasOlderUnclosedShift && !$showCheckoutWarning && !$showShiftSelector)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-orange-600 px-6 py-4">
                <h3 class="text-white font-bold text-lg flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Cảnh báo thứ tự chốt ca
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">{{ $olderShiftWarning }}</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showShiftSelector', true)"
                        class="flex-1 bg-gray-100 text-gray-700 font-medium py-3 rounded-lg hover:bg-gray-200">
                        Chọn ca khác
                    </button>
                    <button wire:click="$set('hasOlderUnclosedShift', false)"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-lg">
                        Tiếp tục chốt ca này
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
