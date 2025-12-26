<?php

namespace App\Livewire\Admin\Production;

use App\Models\Recipe;
use App\Models\RecipeDetail;
use App\Models\Product;
use App\Models\Ingredient;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class RecipeForm extends Component
{
    public ?Recipe $recipe = null;
    public $ma_cong_thuc = '';
    public $ten_cong_thuc = '';
    public $san_pham_id = '';
    public $so_luong_san_xuat = 100;
    public $don_vi_san_xuat = 'cái';
    public $mo_ta = '';
    public $trang_thai = 'hoat_dong';

    // Nguyên liệu
    public $ingredients = [];
    
    public function mount($id = null)
    {
        if ($id) {
            $this->recipe = Recipe::with('details')->findOrFail($id);
            $this->ma_cong_thuc = $this->recipe->ma_cong_thuc;
            $this->ten_cong_thuc = $this->recipe->ten_cong_thuc;
            $this->san_pham_id = $this->recipe->san_pham_id;
            $this->so_luong_san_xuat = $this->recipe->so_luong_san_xuat;
            $this->don_vi_san_xuat = $this->recipe->don_vi_san_xuat;
            $this->mo_ta = $this->recipe->mo_ta;
            $this->trang_thai = $this->recipe->trang_thai;

            // Load existing ingredients
            foreach ($this->recipe->details as $detail) {
                $this->ingredients[] = [
                    'nguyen_lieu_id' => $detail->nguyen_lieu_id,
                    'so_luong' => $detail->so_luong,
                    'don_vi' => $detail->don_vi,
                    'don_gia' => $detail->don_gia,
                ];
            }
        } else {
            // Auto-generate code for new recipe
            $this->ma_cong_thuc = Recipe::generateUniqueCode('ma_cong_thuc', 'CT');
        }

        // Initialize with empty row if no ingredients
        if (empty($this->ingredients)) {
            $this->addIngredient();
        }
    }

    public function addIngredient()
    {
        $this->ingredients[] = [
            'nguyen_lieu_id' => '',
            'so_luong' => 0,
            'don_vi' => 'kg',
            'don_gia' => 0,
        ];
    }

    public function removeIngredient($index)
    {
        unset($this->ingredients[$index]);
        $this->ingredients = array_values($this->ingredients);
    }

    public function updatedIngredients($value, $key)
    {
        // Parse key to get index and field name
        // Format: "0.nguyen_lieu_id" or "1.so_luong"
        $parts = explode('.', $key);
        if (count($parts) === 2 && $parts[1] === 'nguyen_lieu_id') {
            $index = $parts[0];
            $ingredientId = $value;
            
            if ($ingredientId) {
                $ingredient = Ingredient::find($ingredientId);
                if ($ingredient) {
                    // Auto-fill unit and price
                    $this->ingredients[$index]['don_vi'] = $ingredient->don_vi_tinh;
                    $this->ingredients[$index]['don_gia'] = $ingredient->gia_nhap ?? 0;
                }
            }
        }
    }

    public function updated($propertyName, $value)
    {
        // Sanitize quantity inputs
        if (str_contains($propertyName, 'ingredients.') && str_ends_with($propertyName, '.so_luong')) {
            if ($value === '' || $value === null) {
                // Default to 1 if empty
                data_set($this, $propertyName, 1);
            } else {
                // Normalize decimal separator
                $newValue = str_replace(',', '.', $value);
                if ($newValue !== $value) {
                    data_set($this, $propertyName, $newValue);
                }
            }
        }
    }

    // Computed properties for cost calculation
    public function getTotalCostProperty()
    {
        return collect($this->ingredients)->sum(function($ing) {
            $qty = (float) ($ing['so_luong'] ?? 0);
            $price = (float) ($ing['don_gia'] ?? 0);
            return $qty * $price;
        });
    }

    public function getCostPerUnitProperty()
    {
        if ($this->so_luong_san_xuat > 0) {
            return $this->totalCost / $this->so_luong_san_xuat;
        }
        return 0;
    }

    public function save()
    {
        $this->validate([
            'ma_cong_thuc' => 'required|unique:cong_thuc_san_xuat,ma_cong_thuc,' . ($this->recipe->id ?? 'NULL'),
            'ten_cong_thuc' => 'required|min:2',
            'san_pham_id' => 'required|exists:san_pham,id',
            'so_luong_san_xuat' => 'required|integer|min:1',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.nguyen_lieu_id' => 'required|exists:nguyen_lieu,id',
            'ingredients.*.so_luong' => 'required|numeric|min:0',
            'ingredients.*.don_gia' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {
            $data = [
                'ma_cong_thuc' => $this->ma_cong_thuc,
                'ten_cong_thuc' => $this->ten_cong_thuc,
                'san_pham_id' => $this->san_pham_id,
                'so_luong_san_xuat' => $this->so_luong_san_xuat,
                'don_vi_san_xuat' => $this->don_vi_san_xuat,
                'mo_ta' => $this->mo_ta,
                'trang_thai' => $this->trang_thai,
            ];

            if ($this->recipe) {
                $this->recipe->update($data);
                $this->recipe->details()->delete(); // Delete old details
            } else {
                $this->recipe = Recipe::create($data);
            }

            // Save details
            foreach ($this->ingredients as $ing) {
                if (!empty($ing['nguyen_lieu_id'])) {
                    RecipeDetail::create([
                        'cong_thuc_id' => $this->recipe->id,
                        'nguyen_lieu_id' => $ing['nguyen_lieu_id'],
                        'so_luong' => $ing['so_luong'],
                        'don_vi' => $ing['don_vi'],
                        'don_gia' => $ing['don_gia'],
                    ]);
                }
            }

            // Calculate cost
            $this->recipe->calculateCost();
        });

        session()->flash('message', 'Lưu công thức thành công.');
        return redirect()->route('admin.recipes.index');
    }

    public function render()
    {
        $products = Product::where('trang_thai', 'con_hang')->orderBy('ten_san_pham')->get();
        $allIngredients = Ingredient::orderBy('ten_nguyen_lieu')->get();

        return view('livewire.admin.production.recipe-form', [
            'products' => $products,
            'allIngredients' => $allIngredients,
        ]);
    }
}
