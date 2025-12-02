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

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Users
    Route::get('/users', UserList::class)->name('users.index');
    Route::get('/users/create', function() { return 'Create User'; })->name('users.create');
    Route::get('/users/{id}/edit', function() { return 'Edit User'; })->name('users.edit');
    
    // Agencies
    Route::get('/agencies', AgencyList::class)->name('agencies.index');
    Route::get('/agencies/create', function() { return 'Create Agency'; })->name('agencies.create');
    Route::get('/agencies/{id}/edit', function() { return 'Edit Agency'; })->name('agencies.edit');

    // Products
    Route::get('/products', ProductList::class)->name('products.index');
    Route::get('/products/create', function() { return 'Create Product'; })->name('products.create');
    Route::get('/products/{id}/edit', function() { return 'Edit Product'; })->name('products.edit');

    // Categories
    Route::get('/categories', CategoryList::class)->name('categories.index');
    Route::get('/categories/create', function() { return 'Create Category'; })->name('categories.create');
    Route::get('/categories/{id}/edit', function() { return 'Edit Category'; })->name('categories.edit');

    // Suppliers
    Route::get('/suppliers', SupplierList::class)->name('suppliers.index');
    Route::get('/suppliers/create', function() { return 'Create Supplier'; })->name('suppliers.create');
    Route::get('/suppliers/{id}/edit', function() { return 'Edit Supplier'; })->name('suppliers.edit');

    // Ingredients
    Route::get('/ingredients', IngredientList::class)->name('ingredients.index');
    Route::get('/ingredients/create', function() { return 'Create Ingredient'; })->name('ingredients.create');
    Route::get('/ingredients/{id}/edit', function() { return 'Edit Ingredient'; })->name('ingredients.edit');

    // Vietnamese Routes (Legacy/Duplicate)
    Route::get('/nguoi-dung', NguoiDungDanhSach::class)->name('nguoi-dung.index');
    Route::get('/diem-ban', DiemBanDanhSach::class)->name('diem-ban.index');
    
    // Shift Closing (New)
    Route::get('/shift/closing', ShiftClosing::class)->name('shift.closing');
});
