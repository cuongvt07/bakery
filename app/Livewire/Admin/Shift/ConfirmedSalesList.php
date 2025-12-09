<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CaLamViec;
use App\Models\BatchBanHang;
use App\Models\PendingSale;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class ConfirmedSalesList extends Component
{
    public $shift;
    public $sales = []; // All sales (pending + confirmed)
    
    // Edit modal
    public $showEditModal = false;
    public $editingBatch = null;
    public $editingBatchItems = []; // Editable items
    public $editPaymentMethod = 'tien_mat';
    public $editNote = '';

    public function mount()
    {
        // Check if user has active shift
        $this->shift = CaLamViec::where('nguoi_dung_id', Auth::id())
            ->where('trang_thai', 'dang_lam')
            ->first();

        if (!$this->shift) {
            session()->flash('error', 'Bạn chưa có ca làm việc nào!');
            return redirect()->route('admin.shift.check-in');
        }

        $this->loadSales();
    }

    public function loadSales()
    {
        // Load all confirmed batches for this shift
        $confirmedBatches = BatchBanHang::where('ca_lam_viec_id', $this->shift->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        
        // Load all pending sales
        $pendingSales = PendingSale::where('ca_lam_viec_id', $this->shift->id)
            ->where('trang_thai', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($sale) {
                return array_merge($sale->toArray(), ['is_confirmed' => false]);
            })
            ->toArray();
        
        // Format confirmed batches
        $confirmedSales = array_map(function($batch) {
            return array_merge($batch, ['is_confirmed' => true]);
        }, $confirmedBatches);
        
        // Merge and sort by time
        $this->sales = array_merge($confirmedSales, $pendingSales);
        
        // Sort by created_at descending
        usort($this->sales, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
    }

    public function openEdit($saleId, $isConfirmed)
    {
        if ($isConfirmed) {
            // Load batch
            $this->editingBatch = BatchBanHang::find($saleId);
            
            // Flatten items for editing
            $this->editingBatchItems = [];
            foreach ($this->editingBatch->chi_tiet_don as $sale) {
                foreach ($sale['chi_tiet'] as $item) {
                    $key = $item['product_id'];
                    if (!isset($this->editingBatchItems[$key])) {
                        $this->editingBatchItems[$key] = [
                            'product_id' => $item['product_id'],
                            'ten_sp' => $item['ten_sp'],
                            'gia' => $item['gia'],
                            'so_luong' => 0,
                        ];
                    }
                    $this->editingBatchItems[$key]['so_luong'] += $item['so_luong'];
                }
            }
            
            // Get payment method from first sale
            $firstSaleId = $this->editingBatch->chi_tiet_don[0]['id'] ?? null;
            if ($firstSaleId) {
                $pendingSale = PendingSale::find($firstSaleId);
                $this->editPaymentMethod = $pendingSale->phuong_thuc_thanh_toan ?? 'tien_mat';
            }
            
            $this->editNote = '';
            $this->showEditModal = true;
        } else {
            // Redirect to pending list for editing
            return redirect()->route('admin.pos.pending');
        }
    }

    public function closeEdit()
    {
        $this->showEditModal = false;
        $this->editingBatch = null;
        $this->editingBatchItems = [];
        $this->editPaymentMethod = 'tien_mat';
        $this->editNote = '';
    }

    public function updateBatch()
    {
        if (!$this->editingBatch) return;
        
        // Validate note is required
        $this->validate([
            'editNote' => 'required|min:5',
        ], [
            'editNote.required' => 'Vui lòng nhập lý do điều chỉnh!',
            'editNote.min' => 'Lý do phải ít nhất 5 ký tự!',
        ]);

        DB::transaction(function() {
            $batch = BatchBanHang::find($this->editingBatch->id);
            if (!$batch) return;
            
            // 1. Restore old inventory (reverse the original deduction)
            foreach ($batch->chi_tiet_don as $sale) {
                foreach ($sale['chi_tiet'] as $item) {
                    $chiTietCaLam = ChiTietCaLam::where('ca_lam_viec_id', $this->shift->id)
                        ->where('san_pham_id', $item['product_id'])
                        ->first();
                    
                    if ($chiTietCaLam) {
                        $chiTietCaLam->decrement('so_luong_ban', $item['so_luong']);
                    }
                }
            }
            
            // 2. Apply new quantities
            $newTotal = 0;
            foreach ($this->editingBatchItems as $item) {
                if ($item['so_luong'] > 0) {
                    $chiTietCaLam = ChiTietCaLam::where('ca_lam_viec_id', $this->shift->id)
                        ->where('san_pham_id', $item['product_id'])
                        ->first();
                    
                    if ($chiTietCaLam) {
                        $chiTietCaLam->increment('so_luong_ban', $item['so_luong']);
                    }
                    
                    $newTotal += $item['gia'] * $item['so_luong'];
                }
            }
            
            // 3. Update batch with new data
            $newChiTietDon = [[
                'id' => $batch->chi_tiet_don[0]['id'] ?? null,
                'thoi_gian' => now()->format('H:i:s'),
                'chi_tiet' => array_values(array_map(function($item) {
                    return [
                        'product_id' => $item['product_id'],
                        'ten_sp' => $item['ten_sp'],
                        'so_luong' => $item['so_luong'],
                        'gia' => $item['gia'],
                        'thanh_tien' => $item['gia'] * $item['so_luong'],
                    ];
                }, array_filter($this->editingBatchItems, fn($i) => $i['so_luong'] > 0))),
                'tong_tien' => $newTotal,
            ]];
            
            // 4. Update payment method in PendingSale
            $firstSaleId = $batch->chi_tiet_don[0]['id'] ?? null;
            if ($firstSaleId) {
                PendingSale::where('id', $firstSaleId)
                    ->update(['phuong_thuc_thanh_toan' => $this->editPaymentMethod]);
            }
            
            // 5. Save note and update batch
            $currentNote = $batch->ghi_chu ?? '';
            $newNote = "[" . now()->format('d/m H:i') . "] " . Auth::user()->ho_ten . ": " . $this->editNote;
            
            $batch->chi_tiet_don = $newChiTietDon;
            $batch->tong_tien = $newTotal;
            $batch->ghi_chu = $currentNote ? $currentNote . "\n" . $newNote : $newNote;
            $batch->save();
        });

        session()->flash('success', 'Đã cập nhật đơn hàng!');
        $this->closeEdit();
        $this->loadSales();
    }

    public function render()
    {
        return view('livewire.admin.shift.confirmed-sales-list');
    }
}
