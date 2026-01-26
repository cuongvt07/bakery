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
            // Redirect employee to employee dashboard
            return redirect()->route('employee.dashboard');
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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/notifications', App\Livewire\Admin\Notification\NotificationManager::class)->name('notifications.index');

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
    Route::get('/agencies/{agencyId}/locations', App\Livewire\Admin\Agency\LocationList::class)->name('agencies.locations');
    Route::get('/agencies/{agencyId}/note-types', App\Livewire\Admin\Agency\NoteTypeList::class)->name('agencies.note-types');

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

    // Departments
    Route::get('/departments', App\Livewire\Admin\Department\DepartmentList::class)->name('departments.index');
    Route::get('/departments/create', App\Livewire\Admin\Department\DepartmentForm::class)->name('departments.create');
    Route::get('/departments/{id}/edit', App\Livewire\Admin\Department\DepartmentForm::class)->name('departments.edit');

    // Materials Management (centralized)
    Route::get('/materials', App\Livewire\Admin\Material\MaterialList::class)->name('materials.index');
    Route::get('/materials/create', App\Livewire\Admin\Material\MaterialForm::class)->name('materials.create');
    Route::get('/materials/{id}/edit', App\Livewire\Admin\Material\MaterialForm::class)->name('materials.edit');

    // Production Management
    Route::get('/recipes', App\Livewire\Admin\Production\RecipeList::class)->name('recipes.index');
    Route::get('/recipes/create', App\Livewire\Admin\Production\RecipeForm::class)->name('recipes.create');
    Route::get('/recipes/{id}/edit', App\Livewire\Admin\Production\RecipeForm::class)->name('recipes.edit');

    Route::get('/production-batches', App\Livewire\Admin\Production\ProductionBatchList::class)->name('production-batches.index');
    Route::get('/production-batches/create', App\Livewire\Admin\Production\ProductionBatchForm::class)->name('production-batches.create');
    Route::get('/production-batches/{id}/edit', App\Livewire\Admin\Production\ProductionBatchForm::class)->name('production-batches.edit');
    Route::get('/batches/monitoring', App\Livewire\Admin\Batch\BatchMonitoring::class)->name('batches.monitoring');
    // Route::get('/expiry-report', App\Livewire\Admin\Production\ExpiryReport::class)->name('expiry-report.index');
    Route::get('/attendance', App\Livewire\Admin\Shift\AttendanceManager::class)->name('attendance.index');

    Route::get('/distribution', \App\Livewire\Admin\Distribution\DistributionList::class)->name('distribution.index');
    Route::get('/distribution/daily', DailyDistribution::class)->name('distribution.daily');

    // Shift Management & Operations
    Route::get('/shift/management', \App\Livewire\Admin\Shift\ShiftManagement::class)->name('shift.management'); // Admin view
    Route::get('/shift/new', \App\Livewire\Admin\Shift\ShiftManagementNew::class)->name('shift.new'); // New tab-based UI
    Route::get('/shift/closing', ShiftClosing::class)->name('shift.closing'); // Employee closing
    Route::get('/shift/requests', \App\Livewire\Admin\Shift\AdminShiftRequests::class)->name('shift.requests');
    Route::get('/shift/check-in', \App\Livewire\Admin\Shift\ShiftCheckIn::class)->name('shift.check-in'); // Employee check-in
    Route::get('/pos', QuickSale::class)->name('admin.pos.quick-sale');
    Route::get('/pos/pending', App\Livewire\Admin\Shift\PendingSalesList::class)->name('admin.pos.pending');
    Route::get('/pos/confirmed', App\Livewire\Admin\Shift\ConfirmedSalesList::class)->name('admin.pos.confirmed');
});

// Employee Routes (Mobile-First)
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', App\Livewire\Employee\Dashboard::class)->name('dashboard');

    // Shift Management
    Route::get('/shifts/schedule', App\Livewire\Employee\Shift\ShiftSchedule::class)->name('shifts.schedule');
    Route::get('/shifts/register', App\Livewire\Employee\Shift\ShiftRegistration::class)->name('shifts.register');
    Route::get('/shifts/requests', App\Livewire\Employee\Shift\ShiftRequests::class)->name('shifts.requests');
    Route::get('/shifts/check-in', App\Livewire\Employee\Shift\CheckIn::class)->name('shifts.check-in');
    Route::get('/shifts/closing', App\Livewire\Admin\Shift\ShiftClosing::class)->name('shifts.closing');
    Route::get('/shifts/system-schedule', App\Livewire\Employee\Shift\EmployeeSystemSchedule::class)->name('shifts.system-schedule');


    // POS (Sales)
    Route::get('/pos', App\Livewire\Admin\Shift\QuickSale::class)->name('pos');
    Route::get('/pos/pending', App\Livewire\Admin\Shift\PendingSalesList::class)->name('pos.pending');
    Route::get('/pos/confirmed', App\Livewire\Admin\Shift\ConfirmedSalesList::class)->name('pos.confirmed');

    // Material Check
    Route::get('/materials', App\Livewire\Employee\Material\MaterialCheck::class)->name('materials');

    // Support
    Route::get('/support/ticket', App\Livewire\Employee\SupportTicket::class)->name('support.ticket');
});





Route::get('/admin/shift-reports', App\Livewire\Admin\Shift\ShiftReportList::class)->name('admin.shift.reports');
