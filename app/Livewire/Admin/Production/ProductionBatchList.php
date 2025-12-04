<?php

namespace App\Livewire\Admin\Production;

use App\Models\ProductionBatch;
use App\Models\Recipe;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class ProductionBatchList extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFilter = '';
    public $buoiFilter = '';
    public $trangThaiFilter = '';
    public $perPage = 15;

    public function mount()
    {
        $this->dateFilter = Carbon::today()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $batch = ProductionBatch::find($id);
        
        if (!$batch) {
            return;
        }

        // Check if batch has been distributed
        if ($batch->distributions()->exists()) {
            session()->flash('error', 'Không thể xóa mẻ đã được phân bổ!');
            return;
        }

        $batch->delete();
        session()->flash('message', 'Đã xóa mẻ sản xuất thành công.');
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->dateFilter = Carbon::today()->format('Y-m-d');
        $this->buoiFilter = '';
        $this->trangThaiFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = ProductionBatch::with(['recipe.product', 'creator']);

        if ($this->search) {
            $query->where('ma_me', 'like', '%' . $this->search . '%');
        }

        if ($this->dateFilter) {
            $query->whereDate('ngay_san_xuat', $this->dateFilter);
        }

        if ($this->buoiFilter) {
            $query->where('buoi', $this->buoiFilter);
        }

        if ($this->trangThaiFilter) {
            $query->where('trang_thai', $this->trangThaiFilter);
        }

        $batches = $query->orderBy('ngay_san_xuat', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate($this->perPage);

        return view('livewire.admin.production.production-batch-list', [
            'batches' => $batches,
        ]);
    }
}
