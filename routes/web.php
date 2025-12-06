<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\User\UserList;
use App\Livewire\Admin\Agency\AgencyList;
use App\Livewire\Admin\Product\ProductList;
use App\Livewire\Admin\Supplier\SupplierList;
use App\Livewire\Admin\Ingredient\IngredientList;
use App\Livewire\Admin\ProductCategory\CategoryList;
use App\Livewire\Admin\NguoiDung\DanhSach as NguoiDungDanhSach;
use App\Livewire\Admin\DiemBan\DanhSach as DiemBanDanhSach;
use App\Livewire\Admin\Shift\ShiftClosing;
use App\Livewire\Admin\Shift\QuickSale;
use App\Livewire\Admin\Distribution\DailyDistribution;

use App\Livewire\Auth\Login;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->vai_tro === 'nhan_vien') {
            // Check if employee has checked in
            $shift = \App\Models\CaLamViec::where('nguoi_dung_id', auth()->id())
                ->where('trang_thai', 'dang_lam')
                ->first();
            
            if ($shift && $shift->trang_thai_checkin) {
                return redirect('/admin/pos');
            }
            return redirect()->route('admin.shift.check-in');
        }
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Users
    Route::get('/users', UserList::class)->name('users.index');
    Route::get('/users/create', App\Livewire\Admin\User\UserForm::class)->name('users.create');
    Route::get('/users/{id}/edit', App\Livewire\Admin\User\UserForm::class)->name('users.edit');
    
    // Agencies
    Route::get('/agencies', AgencyList::class)->name('agencies.index');
    Route::get('/agencies/create', App\Livewire\Admin\Agency\AgencyForm::class)->name('agencies.create');
    Route::get('/agencies/{id}/edit', App\Livewire\Admin\Agency\AgencyForm::class)->name('agencies.edit');
    Route::get('/agencies/dashboard', App\Livewire\Admin\Agency\AgencyDashboard::class)->name('agencies.dashboard');
    Route::get('/agencies/{id}/detail', App\Livewire\Admin\Agency\AgencyDetail::class)->name('agencies.detail');
    Route::get('/agencies/{agencyId}/notes/create', App\Livewire\Admin\Agency\NoteForm::class)->name('agencies.notes.create');
    Route::get('/agencies/{agencyId}/notes/{noteId}/edit', App\Livewire\Admin\Agency\NoteForm::class)->name('agencies.notes.edit');

    // Products
    Route::get('/products', ProductList::class)->name('products.index');
    Route::get('/products/create', App\Livewire\Admin\Product\ProductForm::class)->name('products.create');
    Route::get('/products/{id}/edit', App\Livewire\Admin\Product\ProductForm::class)->name('products.edit');

    // Categories
    Route::get('/categories', CategoryList::class)->name('categories.index');
    Route::get('/categories/create', App\Livewire\Admin\ProductCategory\CategoryForm::class)->name('categories.create');
    Route::get('/categories/{id}/edit', App\Livewire\Admin\ProductCategory\CategoryForm::class)->name('categories.edit');

    // Suppliers
    Route::get('/suppliers', SupplierList::class)->name('suppliers.index');
    Route::get('/suppliers/create', App\Livewire\Admin\Supplier\SupplierForm::class)->name('suppliers.create');
    Route::get('/suppliers/{id}/edit', App\Livewire\Admin\Supplier\SupplierForm::class)->name('suppliers.edit');

    // Ingredients
    Route::get('/ingredients', IngredientList::class)->name('ingredients.index');
    Route::get('/ingredients/create', App\Livewire\Admin\Ingredient\IngredientForm::class)->name('ingredients.create');
    Route::get('/ingredients/{id}/edit', App\Livewire\Admin\Ingredient\IngredientForm::class)->name('ingredients.edit');
    
    // Production Management
    Route::get('/recipes', App\Livewire\Admin\Production\RecipeList::class)->name('recipes.index');
    Route::get('/recipes/create', App\Livewire\Admin\Production\RecipeForm::class)->name('recipes.create');
    Route::get('/recipes/{id}/edit', App\Livewire\Admin\Production\RecipeForm::class)->name('recipes.edit');
    
    Route::get('/production-batches', App\Livewire\Admin\Production\ProductionBatchList::class)->name('production-batches.index');
    Route::get('/production-batches/create', App\Livewire\Admin\Production\ProductionBatchForm::class)->name('production-batches.create');
    Route::get('/production-batches/{id}/edit', App\Livewire\Admin\Production\ProductionBatchForm::class)->name('production-batches.edit');
    Route::get('/expiry-report', App\Livewire\Admin\Production\ExpiryReport::class)->name('expiry-report.index');
    
    // Core Flow
    Route::get('/distribution', \App\Livewire\Admin\Distribution\DistributionList::class)->name('distribution.index');
    Route::get('/distribution/daily', DailyDistribution::class)->name('distribution.daily');
    Route::get('/shift/check-in', App\Livewire\Admin\Shift\ShiftCheckIn::class)->name('shift.check-in');
    Route::get('/shift/closing', ShiftClosing::class)->name('shift.closing');
    
    // POS Routes (components handle their own check-in validation)
    Route::get('/pos', QuickSale::class)->name('admin.pos.quick-sale');
    Route::get('/pos/pending', App\Livewire\Admin\Shift\PendingSalesList::class)->name('admin.pos.pending');
});
