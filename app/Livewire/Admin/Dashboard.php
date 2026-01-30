<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\CaLamViec;
use App\Models\ProductionBatch;
use App\Models\PhanBoHangDiemBan;
use App\Models\BatchBanHang;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public $filterType = 'this_year'; // today, this_week, this_month, this_year, custom
    public $startDate;
    public $endDate;

    public $stats = [];
    public $recentShifts = [];
    public $recentBatches = [];
    public $distributionData = [];

    public function mount()
    {
        $this->setDatesByFilter();
        $this->loadData();
    }

    public function setDatesByFilter()
    {
        $now = Carbon::now();

        switch ($this->filterType) {
            case 'today':
                $this->startDate = $now->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = $now->startOfWeek()->format('Y-m-d');
                $this->endDate = $now->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = $now->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = $now->startOfYear()->format('Y-m-d');
                $this->endDate = $now->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Keep existing values or default to today if null
                if (!$this->startDate)
                    $this->startDate = $now->format('Y-m-d');
                if (!$this->endDate)
                    $this->endDate = $now->format('Y-m-d');
                break;
        }
    }

    public function updatedFilterType()
    {
        if ($this->filterType !== 'custom') {
            $this->setDatesByFilter();
            $this->loadData();
        }
    }

    public function updatedStartDate()
    {
        $this->filterType = 'custom';
        $this->loadData();
    }

    public function updatedEndDate()
    {
        $this->filterType = 'custom';
        $this->loadData();
    }

    public function loadData()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // Summary Statistics
        $this->stats = [
            'active_staff' => CaLamViec::whereBetween('ngay_lam', [$start, $end])
                ->distinct('nguoi_dung_id')
                ->count('nguoi_dung_id'),

            'active_shifts' => CaLamViec::whereBetween('ngay_lam', [$start, $end])->count(),

            'batches_total' => ProductionBatch::whereBetween('ngay_san_xuat', [$start, $end])->count(),

            'batches_completed' => ProductionBatch::whereBetween('ngay_san_xuat', [$start, $end])
                ->where('trang_thai', 'hoan_thanh')
                ->count(),

            'total_distributed' => PhanBoHangDiemBan::whereBetween('created_at', [$start, $end])
                ->sum('so_luong'),

            'total_revenue' => BatchBanHang::whereBetween('ngay_chot', [$start, $end])
                ->sum('tong_tien'),

            // Resource Stats
            'total_agencies' => \App\Models\Agency::count(),
            'total_recipes' => \App\Models\Recipe::count(),
            'total_ingredients' => \App\Models\Ingredient::count(),
            'low_stock_ingredients' => \App\Models\Ingredient::whereColumn('ton_kho_hien_tai', '<=', 'ton_kho_toi_thieu')->count(),
        ];

        // Recent Shifts (Latest 5 within range)
        $this->recentShifts = CaLamViec::with(['nguoiDung', 'diemBan'])
            ->whereBetween('ngay_lam', [$start, $end])
            ->latest('ngay_lam')
            ->limit(5)
            ->get();

        // Recent Production Batches (Latest 5 within range)
        $this->recentBatches = ProductionBatch::with('details.product')
            ->whereBetween('ngay_san_xuat', [$start, $end])
            ->latest('ngay_san_xuat')
            ->limit(5)
            ->get();

        // Distribution Overview (Top 5 Agencies by volume within range)
        $this->distributionData = PhanBoHangDiemBan::with(['diemBan', 'product'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->limit(5)
            ->get();
    }

    // Calculate max revenue for chart scaling
    public $maxRevenue = 0;

    public $salesChartLabel = 'Doanh Số Theo Ngày';

    // Chart data for Sales (Aggregated by Date/Month)
    public function getSalesChartDataProperty()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $diffInDays = $start->diffInDays($end);

        // Determine grouping and label
        // User requested: Today, Week, Month primarily.
        // Today -> Day (Single point)
        // Week -> Day
        // Month -> Day
        // Year -> Month

        $groupBy = 'day';

        if ($this->filterType === 'today') {
            $groupBy = 'day';
            $this->salesChartLabel = 'Doanh Số Hôm Nay (' . $start->format('d/m/Y') . ')';
        } elseif ($this->filterType === 'this_week') {
            $groupBy = 'day';
            $this->salesChartLabel = 'Doanh Số Tuần Này';
        } elseif ($this->filterType === 'this_month') {
            $groupBy = 'day';
            $this->salesChartLabel = 'Doanh Số Tháng Này';
        } elseif ($this->filterType === 'this_year' || $diffInDays > 31) {
            $groupBy = 'month';
            $this->salesChartLabel = 'Doanh Số Năm Này';
        } else {
            $groupBy = 'day';
            $this->salesChartLabel = 'Doanh Số Theo Ngày';
        }

        $query = BatchBanHang::whereBetween('ngay_chot', [$start, $end]);
        $data = [];
        $maxVal = 0;

        if ($groupBy === 'month') {
            $sales = $query->selectRaw('MONTH(ngay_chot) as month, YEAR(ngay_chot) as year, SUM(tong_tien) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // Fill months in range
            $current = $start->copy()->startOfMonth();
            $endMonth = $end->copy()->startOfMonth();

            while ($current <= $endMonth) {
                $sale = $sales->filter(function ($item) use ($current) {
                    return $item->month == $current->month && $item->year == $current->year;
                })->first();

                $total = $sale ? (float) $sale->total : 0;
                if ($total > $maxVal)
                    $maxVal = $total;

                $data[] = [
                    'date' => 'Tháng ' . $current->month,
                    'total' => $total,
                ];
                $current->addMonth();
            }
        } else { // Day (Default)
            $sales = $query->selectRaw('DATE(ngay_chot) as date, SUM(tong_tien) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $current = $start->copy();
            // If start equals end (Today), loop runs once
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $sale = $sales->firstWhere('date', $dateStr);
                $total = $sale ? (float) $sale->total : 0;
                if ($total > $maxVal)
                    $maxVal = $total;

                $data[] = [
                    'date' => $current->format('d/m'), // Short format for better fit
                    'total' => $total,
                ];
                $current->addDay();
            }
        }

        $this->maxRevenue = $maxVal;

        return $data;
    }
    // Chart data for Production (Aggregated by Product)
    public function getProductionChartDataProperty()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        return ProductionBatch::with('details.product')
            ->whereBetween('ngay_san_xuat', [$start, $end])
            ->where('trang_thai', 'hoan_thanh')
            ->get()
            ->flatMap(function ($batch) {
                return $batch->details;
            })
            ->groupBy('san_pham_id')
            ->map(function ($details) {
                return [
                    'product' => $details->first()->product->ten_san_pham ?? 'N/A',
                    'quantity' => $details->sum('so_luong_thuc_te'),
                ];
            })
            ->values()
            ->sortByDesc('quantity')
            ->take(10) // Top 10 products
            ->toArray();
    }

    // Chart data for Distribution (Aggregated by Agency)
    public function getDistributionChartDataProperty()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        return PhanBoHangDiemBan::with('diemBan')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy('diem_ban_id')
            ->map(function ($items) {
                return [
                    'agency' => $items->first()->diemBan->ten_diem_ban ?? 'N/A',
                    'quantity' => $items->sum('so_luong'),
                ];
            })
            ->values()
            ->toArray();
    }

    // Chart data for Sales (Aggregated by Date)


    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
