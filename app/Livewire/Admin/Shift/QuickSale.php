<?php

namespace App\Livewire\Admin\Shift;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\CaLamViec;
use App\Models\ChiTietCaLam;
use App\Models\PendingSale;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class QuickSale extends Component
{
    public $shift;
    public $shiftDetails = [];
    public $cart = [];
    public $total = 0;
    public $pendingCount = 0;
    public $paymentMethod = 'tien_mat'; // tien_mat or chuyen_khoan

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

        if (!$this->shift->trang_thai_checkin) {
            session()->flash('error', 'Vui lòng check-in trước khi bán hàng!');
            return redirect()->route('admin.shift.check-in');
        }

        $this->loadShiftProducts();
        $this->updatePendingCount();
    }

    public function loadShiftProducts()
    {
        // Load products with their quantities from shift details
        $this->shiftDetails = ChiTietCaLam::with('sanPham')
            ->where('ca_lam_viec_id', $this->shift->id)
            ->get()
            ->map(function ($detail) {
                return [
                    'id' => $detail->san_pham_id,
                    'product' => $detail->sanPham,
                    'so_luong_nhan_ca' => $detail->so_luong_nhan_ca,
                    'so_luong_ban' => $detail->so_luong_ban,
                    'con_lai' => $detail->so_luong_con_lai, // Use the actual column from DB
                ];
            })
            ->toArray();

        // Initialize cart with 0 for all products
        foreach ($this->shiftDetails as $detail) {
            $this->cart[$detail['id']] = 0;
        }
    }

    public function increment($productId)
    {
        $detail = collect($this->shiftDetails)->firstWhere('id', $productId);
        
        if (!$detail) {
            return;
        }

        $available = $detail['con_lai'];
        
        if ($this->cart[$productId] < $available) {
            $this->cart[$productId]++;
            $this->calculateTotal();
        } else {
            $this->dispatch('show-alert', [
                'type' => 'warning',
                'message' => 'Không đủ hàng!'
            ]);
        }
    }

    public function decrement($productId)
    {
        if ($this->cart[$productId] > 0) {
            $this->cart[$productId]--;
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->total = 0;

        foreach ($this->cart as $productId => $qty) {
            if ($qty > 0) {
                $detail = collect($this->shiftDetails)->firstWhere('id', $productId);
                if ($detail && $detail['product']) {
                    $this->total += $detail['product']->gia_ban * $qty;
                }
            }
        }
    }

    public function checkout()
    {
        // Validate cart is not empty
        $hasItems = false;
        foreach ($this->cart as $qty) {
            if ($qty > 0) {
                $hasItems = true;
                break;
            }
        }

        if (!$hasItems) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Giỏ hàng trống!'
            ]);
            return;
        }

        try {
            DB::transaction(function () {
                // Prepare sale details
                $chiTiet = [];
                foreach ($this->cart as $productId => $qty) {
                    if ($qty > 0) {
                        $detail = collect($this->shiftDetails)->firstWhere('id', $productId);
                        if ($detail && $detail['product']) {
                            $product = $detail['product'];
                            $chiTiet[] = [
                                'product_id' => $productId,
                                'ten_sp' => $product->ten_san_pham,
                                'so_luong' => $qty,
                                'gia' => $product->gia_ban,
                                'thanh_tien' => $product->gia_ban * $qty,
                            ];
                            
                            // IMPORTANT: Deduct inventory immediately when creating pending sale
                            // This reserves the products for this order
                            $chiTietCaLam = ChiTietCaLam::where('ca_lam_viec_id', $this->shift->id)
                                ->where('san_pham_id', $productId)
                                ->first();
                            
                            if ($chiTietCaLam) {
                                $chiTietCaLam->decrement('so_luong_con_lai', $qty);
                            }
                        }
                    }
                }
                
                // Validate we have items to save
                if (empty($chiTiet)) {
                    throw new \Exception('Không thể tạo đơn hàng - không có sản phẩm hợp lệ!');
                }

                // Create pending sale
                PendingSale::create([
                    'diem_ban_id' => $this->shift->diem_ban_id,
                    'ca_lam_viec_id' => $this->shift->id,
                    'nguoi_ban_id' => Auth::id(),
                    'thoi_gian' => now()->format('H:i:s'),
                    'chi_tiet' => $chiTiet,
                    'tong_tien' => $this->total,
                    'phuong_thuc_thanh_toan' => $this->paymentMethod,
                    'trang_thai' => 'pending',
                ]);
            });

            // Success feedback
            session()->flash('success', 'Đã lưu đơn hàng!');

            // Reset cart
            $this->clearCart();
            
            // Reload shift data to update available quantities
            $this->loadShiftProducts();
            $this->updatePendingCount();

        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        foreach ($this->cart as $key => $value) {
            $this->cart[$key] = 0;
        }
        $this->total = 0;
    }

    public function updatePendingCount()
    {
        $this->pendingCount = PendingSale::where('ca_lam_viec_id', $this->shift->id)
            ->where('trang_thai', 'pending')
            ->count();
    }

    public function render()
    {
        return view('livewire.admin.shift.quick-sale');
    }
}
