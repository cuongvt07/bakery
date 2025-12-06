<?php

namespace App\Livewire\Admin\Agency;

use App\Models\Agency;
use App\Models\AgencyNote;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AgencyDashboard extends Component
{
    public $statusFilter = 'all'; // all, ok, warning, critical

    public function render()
    {
        $agencies = Agency::with(['notes' => function($q) {
            $q->where('da_xu_ly', false)
              ->orderBy('muc_do_quan_trong', 'desc')
              ->orderBy('ngay_nhac_nho');
        }])->where('trang_thai', 'hoat_dong')
          ->get();

        // Calculate status for each agency
        $agencies = $agencies->map(function($agency) {
            $overdueCount = $agency->notes->filter(fn($note) => $note->isOverdue())->count();
            $urgentCount = $agency->notes->where('muc_do_quan_trong', 'khan_cap')->count();
            $highCount = $agency->notes->where('muc_do_quan_trong', 'cao')->count();
            
            // Determine overall status
            if ($overdueCount > 0 || $urgentCount > 0) {
                $agency->status = 'critical'; // Red
                $agency->statusColor = 'red';
                $agency->statusLabel = '🔴 Cần xử lý';
            } elseif ($highCount > 0) {
                $agency->status = 'warning'; // Yellow
                $agency->statusColor = 'yellow';
                $agency->statusLabel = '🟡 Cảnh báo';
            } else {
                $agency->status = 'ok'; // Green
                $agency->statusColor = 'green';
                $agency->statusLabel = '🟢 Ổn định';
            }
            
            $agency->overdueCount = $overdueCount;
            $agency->pendingCount = $agency->notes->count();
            
            return $agency;
        });

        // Apply filter
        if ($this->statusFilter !== 'all') {
            $agencies = $agencies->where('status', $this->statusFilter);
        }

        return view('livewire.admin.agency.agency-dashboard', [
            'agencies' => $agencies,
        ]);
    }
}
