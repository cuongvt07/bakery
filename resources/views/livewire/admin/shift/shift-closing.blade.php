<div class="max-w-4xl mx-auto p-4">
    @if(!$shift)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Bạn không có ca làm việc nào đang hoạt động. Vui lòng check-in trước.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-indigo-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Chốt Ca Làm Việc
                </h2>
                <p class="text-indigo-100 text-sm mt-1">
                    {{ $shift->ngay_lam }} | {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }} - Hiện tại
                </p>
            </div>

            <div class="p-6 space-y-8">
                <!-- 1. Tiền -->
                <section>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">1. Tổng Kết Tiền</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tiền mặt thực tế</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₫</span>
                                </div>
                                <input type="number" wire:model.live="tienMat" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-lg border-gray-300 rounded-md py-3" placeholder="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tiền chuyển khoản</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₫</span>
                                </div>
                                <input type="number" wire:model.live="tienChuyenKhoan" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-lg border-gray-300 rounded-md py-3" placeholder="0">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 2. Hàng hóa -->
                <section>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">2. Kiểm Kê Hàng Hóa</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tồn đầu</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Tồn cuối</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Đã bán</th>
                                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $p)
                                    <tr>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $p->ten_san_pham }}
                                            <div class="text-xs text-gray-500">{{ number_format($p->gia_ban) }} đ</div>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $p->ton_dau_ca }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-center">
                                            <input type="number" wire:model.live="closingStock.{{ $p->id }}" class="w-24 text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" min="0">
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-bold">
                                            {{ $soldQuantities[$p->id] ?? 0 }}
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            {{ number_format(($soldQuantities[$p->id] ?? 0) * $p->gia_ban) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- 3. Tổng kết -->
                <section class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">3. Đối Soát</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-500 mb-1">Doanh thu lý thuyết</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalTheoretical) }} đ</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-500 mb-1">Thực thu (Tiền mặt + CK)</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($totalActual) }} đ</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm border-2 {{ $discrepancy < 0 ? 'border-red-200 bg-red-50' : ($discrepancy > 0 ? 'border-green-200 bg-green-50' : 'border-gray-100') }}">
                            <p class="text-sm text-gray-500 mb-1">Chênh lệch</p>
                            <p class="text-2xl font-bold {{ $discrepancy < 0 ? 'text-red-600' : ($discrepancy > 0 ? 'text-green-600' : 'text-gray-900') }}">
                                {{ number_format($discrepancy) }} đ
                            </p>
                            @if($discrepancy != 0)
                                <p class="text-xs mt-1 {{ $discrepancy < 0 ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $discrepancy < 0 ? 'Thiếu tiền' : 'Thừa tiền' }}
                                </p>
                            @else
                                <p class="text-xs mt-1 text-gray-400">Khớp</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú / Giải trình lệch</label>
                        <textarea wire:model="ghiChu" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Nhập ghi chú nếu có..."></textarea>
                    </div>
                </section>
                
                <!-- Actions -->
                <div class="flex justify-end pt-4">
                    <button wire:click="submit" wire:confirm="Bạn có chắc chắn muốn chốt ca? Hành động này không thể hoàn tác." class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition hover:scale-105 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        XÁC NHẬN CHỐT CA
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
