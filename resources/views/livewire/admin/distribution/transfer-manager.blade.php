<div class="max-w-[1600px] mx-auto pb-20 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-gray-200 pb-5 mb-6 pt-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Luân Chuyển Hàng Hóa</h1>
            <p class="mt-1 text-sm text-gray-500">Điều điều chuyển kho giữa các điểm bán</p>
        </div>
        <a href="{{ route('admin.distribution.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <i class="las la-arrow-left mr-2"></i>Quay lại
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        
        <!-- LEFT COLUMN: Config (1/2) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-2 text-sm font-bold">1</span>
                        Thiết lập
                    </h3>
                </div>
                
                <div class="p-5 space-y-5">
                    <!-- Source -->
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Từ kho (Nguồn)</label>
                        <div class="relative">
                            <i class="las la-warehouse absolute left-3 top-3 text-xl text-gray-400"></i>
                            <select wire:model.live="sourceAgencyId" 
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 text-sm {{ $errors->has('sourceAgencyId') ? 'border-red-300' : '' }}">
                                <option value="">-- Chọn điểm đi --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" @disabled($agency->id == $destinationAgencyId)>
                                        {{ $agency->ten_diem_ban }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('sourceAgencyId') 
                            <p class="mt-1.5 text-xs text-red-600 flex items-center font-medium">
                                <i class="las la-exclamation-circle mr-1"></i> {{ $message }}
                            </p> 
                        @enderror
                        @if(!$errors->has('sourceAgencyId'))
                            <p class="mt-1.5 text-xs text-indigo-600 bg-indigo-50 p-2 rounded flex items-start">
                                <i class="las la-info-circle mr-1 mt-0.5"></i>
                                Điểm đi phải đang đóng ca.
                            </p>
                        @endif
                    </div>

                    <!-- Arrow Indicator -->
                    <div class="flex justify-center">
                        <div class="bg-gray-100 p-1.5 rounded-full text-gray-400">
                            <i class="las la-arrow-down text-xl"></i>
                        </div>
                    </div>

                    <!-- Destination -->
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Đến kho (Đích)</label>
                        <div class="relative">
                            <i class="las la-store-alt absolute left-3 top-3 text-xl text-gray-400"></i>
                            <select wire:model.live="destinationAgencyId" 
                                class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2.5 text-sm">
                                <option value="">-- Chọn điểm đến --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" @disabled($agency->id == $sourceAgencyId)>
                                        {{ $agency->ten_diem_ban }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('destinationAgencyId') 
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Note -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                        <textarea wire:model="note" rows="3" 
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            placeholder="Nhập lý do chuyển..."></textarea>
                    </div>

                    <!-- Setup Summary -->
                    @if(count($stockData) > 0)
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Sản phẩm khả dụng:</span>
                            <span class="font-bold text-gray-900">{{ count($stockData) }} mã</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                             <span class="text-gray-500">Mặt hàng chọn:</span>
                             <span class="font-bold text-indigo-600">{{ count(array_filter($transferItems, fn($q) => $q > 0)) }} mã</span>
                        </div>
                    </div>
                    @endif
                    
                    <button type="button" 
                        wire:click="validateTransfer"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all {{ count(array_filter($transferItems, fn($q) => $q > 0)) == 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                        Xác nhận chuyển
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Products (1/2) -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden min-h-[600px] flex flex-col">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-2 text-sm font-bold">2</span>
                        Chọn hàng hóa
                    </h3>
                    @if($sourceAgencyId)
                        <span class="text-xs font-medium px-2.5 py-1 rounded bg-green-100 text-green-800 border border-green-200">
                             Kho: {{ $agencies->find($sourceAgencyId)?->ten_diem_ban }}
                        </span>
                    @endif
                </div>

                <div class="flex-1 relative">
                    @if(!$sourceAgencyId)
                        <!-- Empty State -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="las la-arrow-left text-2xl text-gray-400"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900">Chưa chọn kho nguồn</h4>
                            <p class="mt-2 text-sm">Vui lòng chọn "Điểm chuyển đi" ở cột bên trái để xem danh sách hàng hóa.</p>
                        </div>
                    @else
                        @if(count($stockData) == 0)
                             <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center text-gray-500">
                                <i class="las la-box-open text-4xl mb-3 text-gray-300"></i>
                                <p>Kho này hiện không có sản phẩm nào khả dụng để chuyển.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Sản phẩm</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tồn kho</th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng chuyển</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($stockData as $productId => $qty)
                                            @php $product = $products->find($productId); @endphp
                                            @if($product)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="h-10 w-10 flex-shrink-0">
                                                                @if($product->anh_san_pham)
                                                                    <img class="h-10 w-10 rounded-lg object-cover border border-gray-200" src="{{ Storage::url($product->anh_san_pham) }}" alt="">
                                                                @else
                                                                    <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                                                        <i class="las la-image"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">{{ $product->ten_san_pham }}</div>
                                                                <div class="text-xs text-gray-500">{{ $product->ma_san_pham }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ number_format($qty) }} {{ $product->don_vi_tinh }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <input type="number" 
                                                            wire:model.live="transferItems.{{ $productId }}"
                                                            min="0" max="{{ $qty }}"
                                                            class="w-24 text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-bold text-indigo-700"
                                                            placeholder="0">
                                                        @error("transferItems.$productId")
                                                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
     @if($showConfirmation)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" wire:click="$set('showConfirmation', false)"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-indigo-600 px-4 py-6 sm:px-6 text-white">
                        <h3 class="text-lg leading-6 font-bold flex items-center">
                            <i class="las la-check-circle text-2xl mr-2"></i>
                            Xác nhận chuyển hàng
                        </h3>
                        <p class="mt-1 text-indigo-100 text-sm opacity-90">
                            Vui lòng kiểm tra kỹ thông tin trước khi thực hiện.
                        </p>
                    </div>

                    <div class="px-6 py-4">
                        <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-center w-1/3">
                                    <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Từ</div>
                                    <div class="font-bold text-gray-900 mt-1">{{ $agencies->find($sourceAgencyId)?->ten_diem_ban }}</div>
                                </div>
                                <div class="w-1/3 flex justify-center">
                                    <i class="las la-long-arrow-alt-right text-3xl text-indigo-400"></i>
                                </div>
                                <div class="text-center w-1/3">
                                    <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Đến</div>
                                    <div class="font-bold text-gray-900 mt-1">{{ $agencies->find($destinationAgencyId)?->ten_diem_ban }}</div>
                                </div>
                            </div>
                            
                            @if($note)
                                <div class="text-sm text-gray-600 bg-white p-2 rounded border border-gray-200 italic">
                                    "<i class="las la-comment"></i> {{ $note }}"
                                </div>
                            @endif
                        </div>

                        <h4 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Chi tiết hàng hóa</h4>
                        <div class="max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                            <table class="min-w-full">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Sản phẩm</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($transferItems as $pid => $qty)
                                        @if($qty > 0)
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-800">{{ $products->find($pid)->ten_san_pham }}</td>
                                                <td class="px-3 py-2 text-right font-bold text-indigo-600">{{ number_format($qty) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4 p-3 bg-blue-50 text-blue-800 text-xs rounded-lg flex items-start">
                            <i class="las la-info-circle text-lg mr-2 flex-shrink-0"></i>
                            <div>
                                Hệ thống sẽ tự động trừ kho từ các mẻ cũ nhất (FIFO) tại điểm nguồn và tạo phiếu chờ nhận tại điểm đích.
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="button" wire:click="confirmTransfer" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Đồng ý chuyển
                        </button>
                        <button type="button" wire:click="$set('showConfirmation', false)" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Hủy bỏ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
