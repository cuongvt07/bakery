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

    <!-- ... (rest of file) ... -->
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let productionChartInstance = null;
        let distributionChartInstance = null;

        document.addEventListener('livewire:navigated', () => {
            initCharts();
        });

        document.addEventListener('DOMContentLoaded', () => {
            initCharts();
        });

        function initCharts() {
            // Production Chart
            const productionData = @json($this->productionChartData);
            const productionCtx = document.getElementById('productionChart');
            
            if (productionCtx) {
                if (productionChartInstance) {
                    productionChartInstance.destroy();
                }

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
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }
            }

            // Distribution Chart
            const distributionData = @json($this->distributionChartData);
            const distributionCtx = document.getElementById('distributionChart');
            
            if (distributionCtx) {
                if (distributionChartInstance) {
                    distributionChartInstance.destroy();
                }

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
