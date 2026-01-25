<?php

namespace App\Livewire\Admin\Production;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDetail;
use App\Models\Recipe;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
class ProductionBatchForm extends Component
{
    use WithFileUploads;

    public ?ProductionBatch $batch = null;
    public $ma_me = '';
    public $ngay_san_xuat;
    public $buoi = 'sang';
    public $han_su_dung;
    public $trang_thai = 'ke_hoach';

    // Products in this batch
    public $products = []; // [[cong_thuc_id, so_luong_du_kien]]

    // QC fields
    public $qcData = []; // [detail_id => failed_qty]
    public $ghi_chu_qc = '';
    public $qcImages = [];
    public $existingImages = [];

    public function mount($id = null)
    {
        $this->ngay_san_xuat = Carbon::today()->format('Y-m-d');
        $this->han_su_dung = Carbon::today()->addDays(3)->format('Y-m-d');

        if ($id) {
            $this->batch = ProductionBatch::with(['details.product'])->findOrFail($id);
            $this->ma_me = $this->batch->ma_me;
            $this->ngay_san_xuat = Carbon::parse($this->batch->ngay_san_xuat)->format('Y-m-d');
            $this->buoi = $this->batch->buoi;
            $this->han_su_dung = $this->batch->han_su_dung ? Carbon::parse($this->batch->han_su_dung)->format('Y-m-d') : null;
            $this->trang_thai = $this->batch->trang_thai;
            $this->ghi_chu_qc = $this->batch->ghi_chu_qc ?? '';
            $this->existingImages = $this->batch->anh_qc ?? [];

            // Load products from details
            foreach ($this->batch->details as $detail) {
                $this->products[] = [
                    'detail_id' => $detail->id,
                    'cong_thuc_id' => $detail->cong_thuc_id,
                    'san_pham_id' => $detail->san_pham_id,
                    'so_luong_du_kien' => $detail->so_luong_du_kien,
                    'so_luong_that_bai' => $detail->so_luong_that_bai,
                    'so_luong_thuc_te' => $detail->so_luong_thuc_te,
                ];

                $this->qcData[$detail->id] = $detail->so_luong_that_bai;
            }
        } else {
            // Start with one empty product slot
            $this->products = [['cong_thuc_id' => '', 'san_pham_id' => '', 'so_luong_du_kien' => 100]];
            // Auto-generate batch code
            $this->generateBatchCode();
        }
    }

    public function generateBatchCode()
    {
        // Use the static method from ProductionBatch model
        $this->ma_me = ProductionBatch::generateBatchCode($this->buoi, $this->ngay_san_xuat);
    }

    public function updatedBuoi()
    {
        if (!$this->batch) {
            $this->generateBatchCode();
        }
    }

    public function updatedNgaySanXuat()
    {
        if (!$this->batch) {
            $this->generateBatchCode();
        }
    }

    public function addProduct()
    {
        $this->products[] = ['cong_thuc_id' => '', 'san_pham_id' => '', 'so_luong_du_kien' => 100];
    }

    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products); // Re-index
    }

    public function save()
    {
        $this->validate([
            'ma_me' => 'required|unique:me_san_xuat,ma_me,' . ($this->batch->id ?? 'NULL'),
            'ngay_san_xuat' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.cong_thuc_id' => 'required|exists:cong_thuc_san_xuat,id',
            'products.*.san_pham_id' => 'required|exists:san_pham,id',
            'products.*.so_luong_du_kien' => 'required|integer|min:1',
        ]);

        DB::transaction(function () {
            $data = [
                'ma_me' => $this->ma_me,
                'nguoi_tao_id' => Auth::id(),
                'ngay_san_xuat' => $this->ngay_san_xuat,
                'buoi' => $this->buoi,
                'han_su_dung' => $this->han_su_dung,
                'trang_thai' => $this->trang_thai,
            ];

            // If completing, set QC user
            if ($this->trang_thai === 'hoan_thanh') {
                $data['nguoi_qc_id'] = Auth::id();
            }

            if ($this->batch) {
                $this->batch->update($data);
                // Delete old details
                $this->batch->details()->delete();
            } else {
                $this->batch = ProductionBatch::create($data);
            }

            // Create details for each product
            foreach ($this->products as $product) {
                // $recipe = Recipe::find($product['cong_thuc_id']);

                $detailData = [
                    'me_san_xuat_id' => $this->batch->id,
                    'cong_thuc_id' => $product['cong_thuc_id'],
                    'san_pham_id' => $product['san_pham_id'],
                    'so_luong_du_kien' => $product['so_luong_du_kien'],
                ];

                // If status is hoan_thanh, save QC data
                if ($this->trang_thai === 'hoan_thanh') {
                    $failedQty = $product['so_luong_that_bai'] ?? 0;
                    $detailData['so_luong_that_bai'] = $failedQty;
                    $detailData['so_luong_thuc_te'] = $product['so_luong_du_kien'] - $failedQty;
                    $detailData['ti_le_hong'] = $product['so_luong_du_kien'] > 0
                        ? ($failedQty / $product['so_luong_du_kien']) * 100
                        : 0;
                }

                ProductionBatchDetail::create($detailData);
            }

            // If completing, deduct ingredients
            if ($this->trang_thai === 'hoan_thanh') {
                $this->batch->deductIngredientsFromInventory();
            }
        });

        session()->flash('message', 'Lưu mẻ sản xuất thành công.');
        return redirect()->route('admin.production-batches.index');
    }

    public function messages()
    {
        return [
            'ma_me.required' => 'Mã mẻ là bắt buộc.',
            'ma_me.unique' => 'Mã mẻ đã tồn tại.',
            'ngay_san_xuat.required' => 'Ngày sản xuất là bắt buộc.',
            'products.required' => 'Cần ít nhất một sản phẩm.',
            'products.*.cong_thuc_id.required' => 'Vui lòng chọn công thức.',
            'products.*.san_pham_id.required' => 'Vui lòng chọn sản phẩm.',
            'products.*.so_luong_du_kien.required' => 'Nhập số lượng dự kiến.',
            'products.*.so_luong_du_kien.min' => 'Số lượng phải lớn hơn 0.',
        ];
    }

    public function confirmQC()
    {
        $this->validate([
            'qcData.*' => 'required|integer|min:0',
            'qcImages.*' => 'nullable|image|max:2048',
        ]);

        // Upload QC images
        $imagePaths = $this->existingImages;
        if (!empty($this->qcImages)) {
            foreach ($this->qcImages as $image) {
                $path = $image->store('production-qc', 'public');
                $imagePaths[] = $path;
            }
        }

        $this->batch->confirmQC($this->qcData, $imagePaths, $this->ghi_chu_qc);
        $this->batch->nguoi_qc_id = Auth::id();
        $this->batch->save();

        session()->flash('message', 'Xác nhận QC thành công. Mẻ sản xuất đã hoàn thành.');
        return redirect()->route('admin.production-batches.index');
    }

    public function updated($propertyName, $value)
    {
        if (str_contains($propertyName, 'products.') && str_ends_with($propertyName, '.cong_thuc_id')) {
            $parts = explode('.', $propertyName); // products.INDEX.cong_thuc_id
            if (count($parts) === 3) {
                $index = $parts[1];
                $recipe = Recipe::find($value);
                if ($recipe) {
                    // Default to recipe's product if it has one, otherwise keep current selection
                    if ($recipe->san_pham_id) {
                        $this->products[$index]['san_pham_id'] = $recipe->san_pham_id;
                    }
                }
            }
        }

        // Also call parent or existing updated hooks logic if any (none seen in previous view)
        // Wait, there was updatedBuoi and updatedNgaySanXuat, those are specific methods.
        // There was no generic updated method in the file I viewed.
    }

    public function render()
    {
        $recipes = Recipe::where('trang_thai', 'hoat_dong')
            ->with('product')
            ->orderBy('ten_cong_thuc')
            ->get();

        $allProducts = Product::where('trang_thai', 'con_hang')->orderBy('ten_san_pham')->get();

        return view('livewire.admin.production.production-batch-form', [
            'recipes' => $recipes,
            'allProducts' => $allProducts,
        ]);
    }
}
