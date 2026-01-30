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

    // Chart data for Sales (Aggregated by Date)
    public function getSalesChartDataProperty()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $sales = BatchBanHang::whereBetween('ngay_chot', [$start, $end])
            ->selectRaw('DATE(ngay_chot) as date, SUM(tong_tien) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with 0
        $data = [];
        $current = $start->copy();

        $maxVal = 0;

        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $sale = $sales->firstWhere('date', $dateStr);
            $total = $sale ? (float) $sale->total : 0;

            if ($total > $maxVal) {
                $maxVal = $total;
            }

            $data[] = [
                'date' => $current->format('d/m/Y'),
                'total' => $total,
            ];

            $current->addDay();
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
