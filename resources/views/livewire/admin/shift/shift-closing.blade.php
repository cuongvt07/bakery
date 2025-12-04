<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Mobile Header -->
    <div class="bg-indigo-600 px-4 py-4 shadow-md sticky top-0 z-10">
        <div class="flex items-center justify-between text-white">
            <h1 class="text-lg font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Chốt Ca Làm Việc
            </h1>
            <span class="text-xs bg-indigo-500 px-2 py-1 rounded-full">{{ now()->format('d/m') }}</span>
        </div>
        @if($shift)
            <div class="mt-2 text-indigo-100 text-xs flex justify-between">
                <span>Ca: {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }} - Hiện tại</span>
                <span>NV: {{ Auth::user()->ho_ten }}</span>
            </div>
        @endif
    </div>

    @if(!$shift)
        <div class="p-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
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
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tiền mặt thực tế</label>
                        <div class="relative">
                            <input type="number" inputmode="numeric" wire:model.live="tienMat" class="block w-full pl-4 pr-12 py-3 text-lg border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">đ</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Tiền chuyển khoản</label>
                        <div class="relative">
                            <input type="number" inputmode="numeric" wire:model.live="tienChuyenKhoan" class="block w-full pl-4 pr-12 py-3 text-lg border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" placeholder="0">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold">đ</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upload Ảnh Két Tiền -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Ảnh két tiền / Bill CK</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="text-xs text-gray-500"><span class="font-semibold">Chạm để tải ảnh</span></p>
                                    <p class="text-xs text-gray-500">Tối đa 5 ảnh</p>
                                </div>
                                <input type="file" wire:model="photosCash" multiple class="hidden" accept="image/*" />
                            </label>
                        </div>
                        @if($photosCash)
                            <div class="mt-2 flex gap-2 overflow-x-auto">
                                @foreach($photosCash as $photo)
                                    <img src="{{ $photo->temporaryUrl() }}" class="h-16 w-16 object-cover rounded border">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SECTION 2: HÀNG HÓA -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded mr-2">2</span>
                        <h2 class="font-bold text-gray-800">Kiểm Kê Kho</h2>
                    </div>
                    <span class="text-xs text-gray-500">{{ count($products) }} sản phẩm</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($products as $p)
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex-1 pr-4">
                                <h3 class="font-medium text-gray-900">{{ $p->ten_san_pham }}</h3>
                                <div class="flex text-xs text-gray-500 mt-1 space-x-3">
                                    <span>Đầu ca: <strong class="text-gray-700">{{ $p->ton_dau_ca }}</strong></span>
                                    <span>Giá: {{ number_format($p->gia_ban/1000) }}k</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <label class="text-xs text-gray-500 mb-1">Tồn cuối</label>
                                <input type="number" inputmode="numeric" wire:model.live="closingStock.{{ $p->id }}" class="w-20 text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 text-lg font-bold" placeholder="0">
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Upload Ảnh Hàng Hóa -->
                <div class="p-4 border-t border-gray-100">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Ảnh khay bánh tồn</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-6 h-6 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500">Chụp ảnh khay bánh</p>
                            </div>
                            <input type="file" wire:model="photosStock" multiple class="hidden" accept="image/*" />
                        </label>
                    </div>
                    @if($photosStock)
                        <div class="mt-2 flex gap-2 overflow-x-auto">
                            @foreach($photosStock as $photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="h-16 w-16 object-cover rounded border">
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
                    <button wire:click="generateZaloText" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        Copy Zalo
                    </button>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Lý thuyết</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($totalTheoretical) }}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="text-xs text-blue-500 mb-1">Thực tế</p>
                            <p class="text-lg font-bold text-blue-700">{{ number_format($totalActual) }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Chênh lệch:</span>
                            <span class="text-xl font-bold {{ $discrepancy < 0 ? 'text-red-600' : ($discrepancy > 0 ? 'text-green-600' : 'text-gray-900') }}">
                                {{ number_format($discrepancy) }} đ
                            </span>
                        </div>
                        @if($discrepancy != 0)
                            <p class="text-right text-xs mt-1 {{ $discrepancy < 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ $discrepancy < 0 ? 'Thiếu tiền' : 'Thừa tiền' }}
                            </p>
                        @else
                            <p class="text-right text-xs mt-1 text-green-600 flex items-center justify-end">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Khớp số liệu
                            </p>
                        @endif
                    </div>

                    <div class="mt-4">
                        <textarea wire:model="ghiChu" rows="2" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ghi chú (nếu có)..."></textarea>
                    </div>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="sticky bottom-4">
                <button wire:click="submit" wire:confirm="Xác nhận chốt ca? Không thể sửa đổi sau khi chốt." class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg text-lg flex items-center justify-center transition transform active:scale-95">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    HOÀN TẤT CHỐT CA
                </button>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('copy-to-clipboard', ({ text }) => {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Đã copy báo cáo! Bạn có thể dán vào Zalo.');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    alert('Lỗi copy. Vui lòng thử lại.');
                });
            });
        });
    </script>
</div>
