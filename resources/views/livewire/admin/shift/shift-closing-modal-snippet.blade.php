{{-- CHECKOUT WARNING MODAL --}}
@if ($showCheckoutWarning)
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
                    <button wire:click="$set('showCheckoutWarning', false)"
                        class="flex-1 bg-gray-100 text-gray-700 font-medium py-3 rounded-lg hover:bg-gray-200">
                        Hủy
                    </button>
                    <button wire:click="submit"
                        class="flex-1 {{ $checkoutWarningType === 'late' ? 'bg-red-600 hover:bg-red-700' : ($checkoutWarningType === 'early' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700') }} text-white font-bold py-3 rounded-lg">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
