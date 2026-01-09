<?php

namespace App\Livewire\Admin\Distribution;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\PhanBoHangDiemBan;
use App\Models\Agency;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class DistributionList extends Component
{
    // Filters
    public $dateFrom = '';
    public $dateTo = '';
    public $selectedAgency = '';
    
    // Expandable rows: ["date_agencyId" => true/false]
    public $expandedRows = [];

    public function mount()
    {
        $this->dateFrom = Carbon::today()->subDays(7)->format('Y-m-d');
        $this->dateTo = Carbon::today()->format('Y-m-d');
    }
    
    public function clearFilters()
    {
        $this->selectedAgency = '';
        $this->dateFrom = Carbon::today()->subDays(7)->format('Y-m-d');
        $this->dateTo = Carbon::today()->format('Y-m-d');
    }

    
    public function toggleDetails($date, $agencyId)
    {
        $key = $date . '_' . $agencyId;
        $this->expandedRows[$key] = !($this->expandedRows[$key] ?? false);
    }
    
    public function deleteDistribution($id)
    {
        $distribution = PhanBoHangDiemBan::find($id);
        
        if (!$distribution) {
            session()->flash('error', 'Không tìm thấy phân bổ!');
            return;
        }

        if ($distribution->trang_thai === 'da_nhan') {
            session()->flash('error', 'Không thể xóa phân bổ đã được nhận hàng!');
            return;
        }

        $distribution->delete();
        
        // Reload modal data
        $this->showDetails($this->modalDate, $this->modalAgencyId);
        session()->flash('success', 'Đã xóa phân bổ thành công!');
    }
    
    public function render()
    {
        // Get distributions grouped by date and agency
        $distributionsQuery = PhanBoHangDiemBan::with(['diemBan', 'product'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$this->dateFrom, $this->dateTo]);
        
        if ($this->selectedAgency) {
            $distributionsQuery->where('diem_ban_id', $this->selectedAgency);
        }
        
        $distributions = $distributionsQuery->get();
        
        // Group by date -> agency
        $groupedData = [];
        
        foreach ($distributions as $dist) {
            $date = Carbon::parse($dist->created_at)->format('Y-m-d');
            $agencyId = $dist->diem_ban_id;
            
            if (!isset($groupedData[$date])) {
                $groupedData[$date] = [];
            }
            
            if (!isset($groupedData[$date][$agencyId])) {
                $groupedData[$date][$agencyId] = [
                    'agency' => $dist->diemBan,  // Changed from agency to diemBan
                    'total_quantity' => 0,
                    'total_items' => 0,
                    'status_counts' => [
                        'da_nhan' => 0,
                        'chua_nhan' => 0,
                    ],
                ];
            }
            
            $groupedData[$date][$agencyId]['total_quantity'] += $dist->so_luong;
            $groupedData[$date][$agencyId]['total_items']++;
            $groupedData[$date][$agencyId]['status_counts'][$dist->trang_thai]++;
        }
        
        // Sort dates descending
        krsort($groupedData);
        
        $agencies = Agency::where('trang_thai', 'hoat_dong')
            ->where('ten_diem_ban', 'not like', '%Xưởng%')
            ->get();
        
        return view('livewire.admin.distribution.distribution-list', [
            'groupedData' => $groupedData,
            'agencies' => $agencies,
        ]);
    }
}
