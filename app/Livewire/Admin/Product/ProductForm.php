<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ProductForm extends Component
{
    public ?Product $product = null;
    public $ma_san_pham = '';
    public $ten_san_pham = '';
    public $danh_muc_id = '';
    public $mo_ta = '';
    public $gia_ban = 0;
    public $don_vi_tinh = 'cái';
    public $trang_thai = 'con_hang';

    public function mount($id = null)
    {
        if ($id) {
            $this->product = Product::findOrFail($id);
            $this->ma_san_pham = $this->product->ma_san_pham;
            $this->ten_san_pham = $this->product->ten_san_pham;
            $this->danh_muc_id = $this->product->danh_muc_id;
            $this->mo_ta = $this->product->mo_ta;
            $this->gia_ban = $this->product->gia_ban;
            $this->don_vi_tinh = $this->product->don_vi_tinh;
            $this->trang_thai = $this->product->trang_thai;
        }
    }

    public function save()
    {
        $this->validate([
            'ma_san_pham' => 'required|unique:san_pham,ma_san_pham,' . ($this->product->id ?? 'NULL'),
            'ten_san_pham' => 'required|min:2',
            'gia_ban' => 'required|numeric|min:0',
            'trang_thai' => 'required|in:con_hang,het_hang,ngung_ban',
        ]);

        $data = [
            'ma_san_pham' => $this->ma_san_pham,
            'ten_san_pham' => $this->ten_san_pham,
            'danh_muc_id' => $this->danh_muc_id ?: null,
            'mo_ta' => $this->mo_ta,
            'gia_ban' => $this->gia_ban,
            'don_vi_tinh' => $this->don_vi_tinh,
            'trang_thai' => $this->trang_thai,
        ];

        if ($this->product) {
            $this->product->update($data);
            session()->flash('message', 'Cập nhật sản phẩm thành công.');
        } else {
            Product::create($data);
            session()->flash('message', 'Thêm mới sản phẩm thành công.');
        }

        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        $categories = ProductCategory::orderBy('thu_tu')->get();
        return view('livewire.admin.product.product-form', [
            'categories' => $categories,
        ]);
    }
}
