<div>
    <!-- Header with Date Filter -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
        <div>
            <input type="date" 
                   wire:model.live="selectedDate" 
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-sm text-gray-600">Nhân Sự Đang Làm</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['active_staff'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">người</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-600">Ca Làm Việc</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['active_shifts'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">ca</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-sm text-gray-600">Mẻ Sản Xuất</div>
            <div class="text-2xl font-bold text-gray-900">
                {{ $stats['batches_completed'] ?? 0 }}/{{ $stats['batches_total'] ?? 0 }}
            </div>
            <div class="text-xs text-gray-500 mt-1">hoàn thành</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <div class="text-sm text-gray-600">Sản Phẩm Phân Bổ</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_distributed'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">cái</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Production Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sản Xuất Theo Sản Phẩm</h3>
            <canvas id="productionChart" height="200"></canvas>
        </div>
        
        <!-- Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Phân Bổ Theo Đại Lý</h3>
            <canvas id="distributionChart" height="200"></canvas>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Recent Shifts -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Ca Làm Việc Hôm Nay</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đại lý</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giờ vào</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentShifts as $shift)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $shift->nguoiDung->ho_ten ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $shift->diemBan->ten_dai_ly ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $shift->gio_bat_dau }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $shift->trang_thai === 'da_chot' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ match($shift->trang_thai) {
                                            'da_chot' => 'Đã chốt',
                                            default => 'Đang làm'
                                        } }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Không có ca làm việc</td></tr>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã mẻ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buổi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentBatches as $batch)
                            <tr>
                                <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $batch->ma_me }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($batch->buoi) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $batch->details->count() }} SP</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $batch->trang_thai === 'hoan_thanh' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ match($batch->trang_thai) {
                                            'hoan_thanh' => 'Hoàn thành',
                                            default => 'Kế hoạch'
                                        } }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Không có mẻ sản xuất</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Distribution Overview -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Phân Bổ Gần Đây</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đại lý</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($distributionData as $dist)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $dist->diemBan->ten_dai_ly ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $dist->product->ten_san_pham ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $dist->so_luong }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $dist->ngay_duyet ? $dist->ngay_duyet->format('d/m/Y') : ($dist->created_at ? $dist->created_at->format('d/m/Y') : 'N/A') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Không có phân bổ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', () => {
        initCharts();
    });

    document.addEventListener('DOMContentLoaded', () => {
        initCharts();
    });

    function initCharts() {
        // Production Chart
        const productionData = @json($this->productionChartData);
        const productionCtx = document.getElementById('productionChart')?.getContext('2d');
        
        if (productionCtx && productionData.length > 0) {
            new Chart(productionCtx, {
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
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Distribution Chart
        const distributionData = @json($this->distributionChartData);
        const distributionCtx = document.getElementById('distributionChart')?.getContext('2d');
        
        if (distributionCtx && distributionData.length > 0) {
            new Chart(distributionCtx, {
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
</script>
