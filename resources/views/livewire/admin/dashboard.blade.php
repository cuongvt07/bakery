<div>
    <!-- Header with Date Filter -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
        
        <div class="flex flex-col md:flex-row items-center gap-3 bg-white p-2 rounded-lg shadow-sm">
            <!-- Quick Filters -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button wire:click="$set('filterType', 'today')" 
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $filterType === 'today' ? 'bg-white shadow text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Hôm nay
                </button>
                <button wire:click="$set('filterType', 'this_week')" 
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $filterType === 'this_week' ? 'bg-white shadow text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Tuần này
                </button>
                <button wire:click="$set('filterType', 'this_month')" 
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $filterType === 'this_month' ? 'bg-white shadow text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Tháng này
                </button>
                <button wire:click="$set('filterType', 'this_year')" 
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $filterType === 'this_year' ? 'bg-white shadow text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Năm nay
                </button>
            </div>

            <!-- Custom Date Range -->
            <div class="flex items-center gap-2 border-l pl-3">
                <input type="date" 
                       wire:model.live="startDate" 
                       class="px-3 py-1 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <span class="text-gray-400">-</span>
                <input type="date" 
                       wire:model.live="endDate" 
                       class="px-3 py-1 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-sm text-gray-600">Doanh Thu</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($stats['total_revenue'] ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-1">VNĐ</div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-600">Ca Làm Việc</div>
            <div class="text-xl font-bold text-gray-900">{{ $stats['active_shifts'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">ca</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-sm text-gray-600">Mẻ Sản Xuất</div>
            <div class="text-xl font-bold text-gray-900">
                {{ $stats['batches_completed'] ?? 0 }}/{{ $stats['batches_total'] ?? 0 }}
            </div>
            <div class="text-xs text-gray-500 mt-1">hoàn thành</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <div class="text-sm text-gray-600">Phân Bổ</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($stats['total_distributed'] ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-1">sản phẩm</div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
             <div class="text-sm text-gray-600">Nhân Sự (Tham gia)</div>
             <div class="text-xl font-bold text-gray-900">{{ $stats['active_staff'] ?? 0 }}</div>
             <div class="text-xs text-gray-500 mt-1">nhân viên</div>
        </div>
    </div>

    <!-- Resource Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg shadow p-4 text-white">
            <div class="text-sm opacity-90">Đại Lý</div>
            <div class="text-2xl font-bold">{{ $stats['total_agencies'] ?? 0 }}</div>
            <div class="text-xs opacity-75 mt-1">hoạt động</div>
        </div>

        <div class="bg-gradient-to-r from-rose-500 to-pink-500 rounded-lg shadow p-4 text-white">
            <div class="text-sm opacity-90">Công Thức</div>
            <div class="text-2xl font-bold">{{ $stats['total_recipes'] ?? 0 }}</div>
            <div class="text-xs opacity-75 mt-1">đang sử dụng</div>
        </div>

        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg shadow p-4 text-white">
            <div class="text-sm opacity-90">Nguyên Liệu</div>
            <div class="text-2xl font-bold">{{ $stats['total_ingredients'] ?? 0 }}</div>
            <div class="text-xs opacity-75 mt-1">trong kho</div>
        </div>

        <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-lg shadow p-4 text-white">
            <div class="text-sm opacity-90">Sắp Hết Hàng</div>
            <div class="text-2xl font-bold">{{ $stats['low_stock_ingredients'] ?? 0 }}</div>
            <div class="text-xs opacity-75 mt-1">nguyên liệu cần nhập</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Doanh Số Theo Ngày</h3>
            <div class="relative h-80 w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Production Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 Sản Xuất (Sản Phẩm)</h3>
            <div class="relative h-80 w-full">
                <canvas id="productionChart"></canvas>
            </div>
        </div>
        
        <!-- Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Phân Bổ Theo Đại Lý</h3>
            <div class="relative h-80 w-full">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Tables Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Shifts -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Ca Làm Việc Gần Đây</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đại lý</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentShifts as $shift)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $shift->nguoiDung->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $shift->diemBan->ten_diem_ban ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $shift->trang_thai === 'dang_lam' ? 'bg-green-100 text-green-800' : 
                                           ($shift->trang_thai === 'da_chot' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $shift->trang_thai)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Batches -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Mẻ Sản Xuất Gần Đây</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã mẻ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày SX</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentBatches as $batch)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $batch->ma_me }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $batch->ngay_san_xuat->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $batch->trang_thai === 'hoan_thanh' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $batch->trang_thai)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Chưa có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let productionChartInstance = null;
        let distributionChartInstance = null;
        let salesChartInstance = null;

        document.addEventListener('livewire:navigated', () => {
            initCharts();
        });

        document.addEventListener('DOMContentLoaded', () => {
            initCharts();
        });

        // Re-init charts when data updates
        document.addEventListener('livewire:initialized', () => {
             Livewire.hook('morph.updated', ({ el, component }) => {
                 initCharts();
             });
        });

        function initCharts() {
            // Sales Chart
            const salesData = @json($this->salesChartData);
            const maxRevenue = {{ $this->maxRevenue }};
            const salesCtx = document.getElementById('salesChart');

            if (salesCtx) {
                if (salesChartInstance) salesChartInstance.destroy();

                if (salesData.length > 0) {
                    salesChartInstance = new Chart(salesCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: salesData.map(d => d.date),
                            datasets: [{
                                label: 'Doanh thu (VNĐ)',
                                data: salesData.map(d => d.total),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: maxRevenue + 50000000,
                                    ticks: {
                                        callback: function(value) {
                                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumSignificantDigits: 3 }).format(value);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Production Chart
            const productionData = @json($this->productionChartData);
            const productionCtx = document.getElementById('productionChart');
            
            if (productionCtx) {
                if (productionChartInstance) productionChartInstance.destroy();

                if (productionData.length > 0) {
                    productionChartInstance = new Chart(productionCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: productionData.map(d => d.product),
                            datasets: [{
                                label: 'Số lượng',
                                data: productionData.map(d => d.quantity),
                                backgroundColor: 'rgba(147, 51, 234, 0.7)',
                                borderColor: 'rgba(147, 51, 234, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y', // Horizontal bar for potential long product names
                            scales: {
                                x: { beginAtZero: true }
                            }
                        }
                    });
                }
            }

            // Distribution Chart
            const distributionData = @json($this->distributionChartData);
            const distributionCtx = document.getElementById('distributionChart');
            
            if (distributionCtx) {
                if (distributionChartInstance) distributionChartInstance.destroy();

                if (distributionData.length > 0) {
                    distributionChartInstance = new Chart(distributionCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: distributionData.map(d => d.agency),
                            datasets: [{
                                data: distributionData.map(d => d.quantity),
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                ],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            }
        }
    </script>
</div>
