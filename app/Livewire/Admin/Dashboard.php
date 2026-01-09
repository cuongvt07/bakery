<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\CaLamViec;
use App\Models\ProductionBatch;
use App\Models\PhanBoHangDiemBan;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public $selectedDate;
    public $stats = [];
    public $recentShifts = [];
    public $recentBatches = [];
    public $distributionData = [];

    public function mount()
    {
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->loadData();
    }

    public function updatedSelectedDate()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $date = Carbon::parse($this->selectedDate);

        // Summary Statistics
        $this->stats = [
            'active_staff' => CaLamViec::whereDate('ngay_lam', $date)
                ->where('trang_thai', '!=', 'da_chot')
                ->distinct('nguoi_dung_id')
                ->count('nguoi_dung_id'),
            
            'active_shifts' => CaLamViec::whereDate('ngay_lam', $date)->count(),
            
            'batches_total' => ProductionBatch::whereDate('ngay_san_xuat', $date)->count(),
            
            'batches_completed' => ProductionBatch::whereDate('ngay_san_xuat', $date)
                ->where('trang_thai', 'hoan_thanh')
                ->count(),
            
            'total_distributed' => PhanBoHangDiemBan::whereDate('created_at', $date)
                ->sum('so_luong'),
        ];

        // Recent Shifts
        $this->recentShifts = CaLamViec::with(['nguoiDung', 'diemBan'])
            ->whereDate('ngay_lam', $date)
            ->latest()
            ->limit(5)
            ->get();

        // Recent Production Batches
        $this->recentBatches = ProductionBatch::with('details.product')
            ->whereDate('ngay_san_xuat', $date)
            ->latest()
            ->limit(5)
            ->get();

        // Distribution Overview
        $this->distributionData = PhanBoHangDiemBan::with(['diemBan', 'product'])
            ->whereDate('created_at', $date)
            ->latest()
            ->limit(5)
            ->get();
    }

    // Chart data for production
    public function getProductionChartDataProperty()
    {
        $date = Carbon::parse($this->selectedDate);
        
        return ProductionBatch::with('details.product')
            ->whereDate('ngay_san_xuat', $date)
            ->where('trang_thai', 'hoan_thanh')
            ->get()
            ->flatMap(function($batch) {
                return $batch->details;
            })
            ->groupBy('san_pham_id')
            ->map(function($details) {
                return [
                    'product' => $details->first()->product->ten_san_pham ?? 'N/A',
                    'quantity' => $details->sum('so_luong_thuc_te'),
                ];
            })
            ->values()
            ->toArray();
    }

    // Chart data for distribution
    public function getDistributionChartDataProperty()
    {
        $date = Carbon::parse($this->selectedDate);
        
        return PhanBoHangDiemBan::with('diemBan')
            ->whereDate('created_at', $date)
            ->get()
            ->groupBy('diem_ban_id')
            ->map(function($items) {
                return [
                    'agency' => $items->first()->diemBan->ten_diem_ban ?? 'N/A',
                    'quantity' => $items->sum('so_luong'),
                ];
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
