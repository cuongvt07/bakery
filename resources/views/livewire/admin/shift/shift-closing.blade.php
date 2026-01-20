<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Mobile Header -->
    <div class="bg-indigo-600 px-4 py-4 shadow-md sticky top-0 z-10">
        <div class="flex items-center justify-between text-white">
            <h1 class="text-lg font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Chốt Ca Làm Việc
            </h1>
            <a href="{{ auth()->user()->vai_tro === 'nhan_vien' ? route('employee.pos') : route('admin.pos.quick-sale') }}"
                class="bg-white text-indigo-600 px-3 py-1.5 rounded-lg font-semibold text-sm hover:bg-indigo-50 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                POS
            </a>
        </div>
        @if ($shift)
            <div class="mt-2 text-indigo-100 text-xs flex justify-between">
                <span>Ca: {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }} - Hiện tại</span>
                <span>NV: {{ Auth::user()->ho_ten }}</span>
            </div>
        @endif
    </div>

    @if (!$shift)
        <div class="p-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Bạn chưa check-in ca làm việc nào.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="p-4 space-y-6">
            <!-- SECTION 1: TIỀN -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex items-center">
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded mr-2">1</span>
                    <h2 class="font-bold text-gray-800">Tổng Kết Tiền</h2>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tổng tiền mặt đang giữ</label>
                        <div class="relative">
                            <input type="number" inputmode="numeric" wire:model.live="tienMat"
                                class="block w-full pl-4 pr-12 py-3 text-lg border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                                placeholder="0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 1.5: THỐNG KÊ ĐƠN HÀNG -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">📊 Thống Kê Đơn Hàng</h3>
                </div>
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">💵 Đơn tiền mặt:</span>
                        <span class="font-semibold text-green-600">{{ $cashSalesCount }} đơn -
                            {{ number_format($cashSalesTotal) }}đ</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">💳 Đơn chuyển khoản:</span>
                        <span class="font-semibold text-blue-600">{{ $transferSalesCount }} đơn -
                            {{ number_format($transferSalesTotal) }}đ</span>
                    </div>
                    <div class="pt-2 mt-2 border-t flex justify-between items-center font-bold">
                        <span class="text-gray-800">Tổng:</span>
                        <span class="text-indigo-600">{{ $cashSalesCount + $transferSalesCount }} đơn -
                            {{ number_format($cashSalesTotal + $transferSalesTotal) }}đ</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: HÀNG HÓA -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded mr-2">2</span>
                        <h2 class="font-bold text-gray-800">Kiểm Kê Số Lượng</h2>
                    </div>
                    <button onclick="copyReport()"
                        class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Copy
                    </button>
                </div>

                <script>
                    function copyReport() {
                        const report = @js($this->generateReport());
                        navigator.clipboard.writeText(report).then(() => {
                            alert('✅ Đã copy báo cáo!');
                        }).catch(() => {
                            alert('❌ Không thể copy. Vui lòng thử lại!');
                        });
                    }
                </script>
                <div class="divide-y divide-gray-100">
                    @foreach ($products as $p)
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex-1 pr-4">
                                <h3 class="font-medium text-gray-900">{{ $p['ten_san_pham'] }}</h3>
                                <div class="flex text-xs text-gray-500 mt-1 space-x-3">
                                    <span>Đầu ca: <strong class="text-gray-700">{{ $p['ton_dau_ca'] }}</strong></span>
                                    <span>Giá: {{ number_format($p['gia_ban'] / 1000) }}k</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <label class="text-xs text-gray-500 mb-1">Tồn cuối</label>
                                <input type="number" inputmode="numeric" step="0.01"
                                    wire:model="closingStock.{{ $p['id'] }}"
                                    class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 text-lg font-bold"
                                    placeholder="0">
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Upload Ảnh Hàng Hóa -->
                <div class="p-4 border-t border-gray-100">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Ảnh khay bánh tồn</label>
                    <div class="flex items-center justify-center w-full">
                        <label
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-6 h-6 mb-2 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500">Chụp ảnh khay bánh</p>
                            </div>
                            <input type="file" wire:model="photosStock" multiple class="hidden"
                                accept="image/*" />
                        </label>
                    </div>
                    @if ($photosStock)
                        <div class="mt-2 flex gap-2 overflow-x-auto">
                            @foreach ($photosStock as $photo)
                                <img src="{{ $photo->temporaryUrl() }}"
                                    class="h-16 w-16 object-cover rounded border">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- SECTION 3: TỔNG KẾT -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded mr-2">3</span>
                        <h2 class="font-bold text-gray-800">Đối Soát</h2>
                    </div>
                    <button wire:click="generateZaloText"
                        class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                            </path>
                        </svg>
                        Copy Zalo
                    </button>
                </div>
                <div class="p-4">

                    <!-- Expected Cash Display -->
                    <div class="bg-yellow-50 p-3 rounded-lg mb-4 border border-yellow-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-yellow-600 font-medium mb-1">💰 TM lý thuyết phải có</p>
                                <p class="text-xs text-gray-500">
                                    Đầu ca: {{ number_format($shift->tien_mat_dau_ca ?? 0) }}đ +
                                    Đơn TM: {{ number_format($cashSalesTotal) }}đ
                                </p>
                            </div>
                            <p class="text-2xl font-bold text-yellow-700">{{ number_format($expectedCash) }}</p>
                        </div>
                    </div>

                    @php
                        $cashDiscrepancy = (float) ($tienMat ?: 0) - $expectedCash;
                    @endphp

                    @if (abs($cashDiscrepancy) > 0)
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-red-700">⚠️ Chênh lệch:</span>
                                <span
                                    class="text-xl font-bold text-red-600">{{ number_format($cashDiscrepancy) }}đ</span>
                            </div>
                            <p class="text-xs text-red-600 mt-1">
                                {{ $cashDiscrepancy < 0 ? 'Thiếu tiền' : 'Thừa tiền' }} - Vui lòng kiểm tra lại hoặc
                                ghi chú lý do
                            </p>
                        </div>
                    @else
                        <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-sm font-medium text-green-700 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                ✅ Đúng số tiền
                            </p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <textarea wire:model="ghiChu" rows="2"
                            class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ghi chú (nếu có)..."></textarea>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="sticky bottom-4">
                <button wire:click="initiateSubmit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg text-lg flex items-center justify-center transition transform active:scale-95">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    HOÀN TẤT CHỐT CA
                </button>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('copy-to-clipboard', ({
                text
            }) => {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Đã copy báo cáo! Bạn có thể dán vào Zalo.');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    alert('Lỗi copy. Vui lòng thử lại.');
                });
            });

            Livewire.on('show-alert', ({
                type,
                message
            }) => {
                alert((type === 'success' ? '✅ ' : '⚠️ ') + message);
            });
        });
    </script>

    @include('livewire.admin.shift.shift-closing-modal-snippet')
</div>
