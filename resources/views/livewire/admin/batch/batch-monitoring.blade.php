<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Giám sát Mẻ Hàng (Batch)</h1>
            <p class="mt-2 text-sm text-gray-700">Theo dõi hạn sử dụng và tình trạng các mẻ hàng đã hoàn thành.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-6 bg-white p-4 rounded-lg shadow flex items-center space-x-4">
        <div class="flex-1 max-w-md">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <div class="relative rounded-md shadow-sm">
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Nhập mã mẻ hoặc tên sản phẩm...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="mt-6 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Mã mẻ</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Ngày SX</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">Sản phẩm</th>
                    <th scope="col" class="px-4 py-3.5 text-center text-sm font-semibold text-gray-900">HSD</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-sm font-semibold text-gray-900">Dự kiến</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-sm font-semibold text-blue-600">Phân bổ</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-sm font-semibold text-red-600">Hỏng</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-sm font-semibold text-green-600">Thực tế</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-sm font-semibold text-orange-600">Đã bán</th>
                    <th scope="col"
                        class="px-4 py-3.5 text-right text-sm font-semibold text-purple-600 bg-purple-50">Còn lại</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($batches as $batch)
                    <tr class="hover:bg-gray-50 {{ $batch->total_distributed == 0 ? 'bg-yellow-50' : '' }}">
                        <td class="whitespace-nowrap px-4 py-4 text-sm font-bold text-indigo-600">
                            {{ $batch->ma_me }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900">
                            {{ $batch->ngay_san_xuat->format('d/m/Y') }}
                            <div class="text-xs text-gray-500">{{ $batch->buoi == 'sang' ? 'Sáng' : 'Chiều' }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 max-w-xs truncate"
                            title="{{ $batch->product_names }}">
                            {{ Str::limit($batch->product_names, 40) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-center">
                            @if ($batch->nearest_expiry)
                                {{ \Carbon\Carbon::parse($batch->nearest_expiry)->format('d/m/Y') }}
                                @if (\Carbon\Carbon::parse($batch->nearest_expiry)->isToday())
                                    <span
                                        class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Hết hạn!
                                    </span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-right font-medium text-gray-900">
                            {{ number_format($batch->total_expected) }}
                        </td>
                        <td
                            class="whitespace-nowrap px-4 py-4 text-sm text-right {{ $batch->total_distributed > 0 ? 'text-blue-600 font-medium' : 'text-gray-400' }}">
                            {{ $batch->total_distributed > 0 ? number_format($batch->total_distributed) : '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-right text-red-600 font-bold">
                            {{ $batch->total_failed > 0 ? number_format($batch->total_failed) : '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-right text-green-700 font-bold bg-green-50">
                            {{ number_format($batch->total_actual) }}
                        </td>
                        <td
                            class="whitespace-nowrap px-4 py-4 text-sm text-right {{ $batch->total_sold > 0 ? 'text-orange-600 font-medium' : 'text-gray-400' }}">
                            {{ $batch->total_sold > 0 ? number_format($batch->total_sold) : '-' }}
                        </td>
                        <td
                            class="whitespace-nowrap px-4 py-4 text-sm text-right text-purple-700 font-bold bg-purple-50">
                            {{ number_format($batch->total_remaining) }}
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <button wire:click="openBatchDetail('{{ $batch->id }}')"
                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md border border-indigo-200 shadow-sm transition">
                                Chi tiết
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-base font-medium">Không có mẻ nào</p>
                            <p class="text-sm">Thử thay đổi bộ lọc hoặc kiểm tra lại</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $batches->links() }}
    </div>

    <!-- Detail Modal -->
    @if ($showModal && $selectedBatch)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Modal Header -->
                    <div class="bg-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Mẻ: {{ $selectedBatch['code'] }}
                                </h3>
                                <p class="text-indigo-100 text-sm">
                                    {{ $selectedBatch['date'] }} ({{ $selectedBatch['shift'] }}) •
                                    Người tạo: {{ $selectedBatch['creator'] }}
                                </p>
                            </div>
                            <button wire:click="closeModal" class="text-white hover:text-indigo-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto" x-data="{ activeTab: 'products' }">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-4 gap-4 mb-6">
                            <div class="bg-gray-50 p-3 rounded-lg text-center border">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Dự kiến</dt>
                                <dd class="mt-1 text-xl font-semibold text-gray-900">
                                    {{ number_format($selectedBatch['total_expected']) }}</dd>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg text-center border border-blue-100">
                                <dt class="text-xs font-medium text-blue-500 uppercase">Đã phân bổ</dt>
                                <dd class="mt-1 text-xl font-semibold text-blue-600">
                                    {{ number_format($selectedBatch['total_distributed']) }}</dd>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg text-center border border-red-100">
                                <dt class="text-xs font-medium text-red-500 uppercase">Hỏng</dt>
                                <dd class="mt-1 text-xl font-semibold text-red-600">
                                    {{ number_format($selectedBatch['total_failed']) }}</dd>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg text-center border border-green-100">
                                <dt class="text-xs font-medium text-green-500 uppercase">Thực tế</dt>
                                <dd class="mt-1 text-xl font-semibold text-green-600">
                                    {{ number_format($selectedBatch['total_actual']) }}</dd>
                            </div>
                        </div>

                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200 mb-4">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'products'"
                                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'products', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'products' }"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Chi tiết sản phẩm & Phân bổ
                                </button>
                                <button @click="activeTab = 'history'"
                                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'history' }"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Lịch sử thay đổi
                                </button>
                            </nav>
                        </div>

                        <!-- Tab 1: Products -->
                        <div x-show="activeTab === 'products'">
                            @foreach ($selectedBatch['products'] as $product)
                                <div class="bg-white rounded-lg border mb-4 overflow-hidden shadow-sm">
                                    <!-- Product Header -->
                                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                        <div>
                                            <span
                                                class="font-bold text-lg text-gray-900">{{ $product['name'] }}</span>
                                            <span
                                                class="text-xs text-gray-500 ml-2 bg-white border px-2 py-0.5 rounded">HSD:
                                                {{ $product['expiry'] }}</span>
                                        </div>
                                        <div class="flex gap-4 text-sm">
                                            <span class="text-gray-600">Ban đầu:
                                                <b>{{ number_format($product['expected']) }}</b></span>
                                            <span class="text-red-600">Hỏng:
                                                <b>{{ number_format($product['failed']) }}</b></span>
                                            <span class="text-green-600">Thực tế:
                                                <b>{{ number_format($product['actual']) }}</b></span>
                                        </div>
                                    </div>

                                    <!-- Distribution Details Table -->
                                    <div class="p-0">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Điểm bán</th>
                                                    <th scope="col"
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Số lượng</th>
                                                    <th scope="col"
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Thời gian</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @forelse ($product['distributions'] as $dist)
                                                    <tr>
                                                        <td
                                                            class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $dist['shop'] }}</td>
                                                        <td
                                                            class="px-4 py-2 whitespace-nowrap text-sm text-blue-600 font-bold">
                                                            {{ number_format($dist['qty']) }}</td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $dist['date'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3"
                                                            class="px-4 py-4 text-center text-sm text-gray-500 italic">
                                                            Chưa
                                                            có phân bổ nào</td>
                                                    </tr>
                                                @endforelse
                                                <!-- Summary Row -->
                                                <tr class="bg-gray-50">
                                                    <td
                                                        class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                                        Tổng cộng</td>
                                                    <td
                                                        class="px-4 py-2 whitespace-nowrap text-sm font-bold text-blue-600">
                                                        {{ number_format($product['distributed']) }}</td>
                                                    <td
                                                        class="px-4 py-2 whitespace-nowrap text-sm text-orange-600 font-bold">
                                                        Còn ở xưởng: {{ number_format($product['remaining']) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Section 3: Value Adjustment (Moved inside Products tab) -->
                            <div class="mt-6 bg-white border rounded-lg p-4 shadow-sm">
                                <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Báo cáo Hỏng / Điều chỉnh
                                </h4>

                                @if (session()->has('message'))
                                    <div
                                        class="mb-3 p-2 bg-green-100 text-green-700 rounded text-sm flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ session('message') }}
                                    </div>
                                @endif

                                <div class="flex gap-3 items-end">
                                    <div class="w-1/3">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Sản phẩm</label>
                                        <select wire:model="adjustProductId"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <option value="">-- Chọn --</option>
                                            @foreach ($selectedBatch['products'] as $p)
                                                <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('adjustProductId')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-24">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Số lượng</label>
                                        <input type="number" wire:model="adjustQty" min="1"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            placeholder="5">
                                        @error('adjustQty')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Ghi chú</label>
                                        <input type="text" wire:model="adjustNote"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                            placeholder="Lý do...">
                                    </div>
                                    <button wire:click="saveAdjustment"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Cập nhật
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: History -->
                        <div x-show="activeTab === 'history'" style="display: none;">
                            <div class="bg-white rounded-lg border overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Thời gian</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Người thực hiện</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Loại</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Sản phẩm</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Thay đổi</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($selectedBatch['history'] as $log)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $log->created_at->format('H:i d/m/Y') }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $log->nguoiCapNhat->ho_ten ?? 'Hệ thống' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @switch($log->loai)
                                                        @case('ban')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                Bán hàng
                                                            </span>
                                                        @break

                                                        @case('hong')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                Hỏng/Lỗi
                                                            </span>
                                                        @break

                                                        @case('dieu_chinh')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                Điều chỉnh
                                                            </span>
                                                        @break

                                                        @case('phan_bo')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                Phân bổ
                                                            </span>
                                                        @break

                                                        @case('hoan')
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                                Hoàn về
                                                            </span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                                {{ $log->loai }}
                                                            </span>
                                                    @endswitch
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $log->product->ten_san_pham ?? '-' }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $log->so_luong_doi > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $log->so_luong_doi > 0 ? '+' : '' }}{{ $log->so_luong_doi }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 italic truncate max-w-xs"
                                                    title="{{ $log->ghi_chu }}">
                                                    {{ $log->ghi_chu ?? '-' }}
                                                </td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6"
                                                        class="px-6 py-4 text-center text-sm text-gray-500">Chưa
                                                        có lịch sử thay đổi</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-6 py-3 flex justify-end">
                            <button wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                                Đóng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
