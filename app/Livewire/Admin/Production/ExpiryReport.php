<?php

namespace App\Livewire\Admin\Production;

use App\Models\ProductionBatchDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ExpiryReport extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, expired, near_expiry, ok
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ProductionBatchDetail::with(['product'])
            ->whereNotNull('han_su_dung');

        // Apply filters
        if ($this->filter === 'expired') {
            $query->whereDate('han_su_dung', '<', now());
        } elseif ($this->filter === 'near_expiry') {
            $query->whereDate('han_su_dung', '>=', now())
                  ->whereDate('han_su_dung', '<=', now()->addDay());
        } elseif ($this->filter === 'ok') {
            $query->whereDate('han_su_dung', '>', now()->addDay());
        }

        // Search
        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('ten_san_pham', 'like', '%' . $this->search . '%');
            });
        }

        $details = $query->orderBy('han_su_dung', 'asc')
                        ->paginate(20);

        // Statistics
        $stats = [
            'total' => ProductionBatchDetail::whereNotNull('han_su_dung')->count(),
            'expired' => ProductionBatchDetail::whereNotNull('han_su_dung')
                ->whereDate('han_su_dung', '<', now())->count(),
            'near_expiry' => ProductionBatchDetail::whereNotNull('han_su_dung')
                ->whereDate('han_su_dung', '>=', now())
                ->whereDate('han_su_dung', '<=', now()->addDay())->count(),
            'ok' => ProductionBatchDetail::whereNotNull('han_su_dung')
                ->whereDate('han_su_dung', '>', now()->addDay())->count(),
        ];

        return view('livewire.admin.production.expiry-report', [
            'details' => $details,
            'stats' => $stats,
        ]);
    }
}
