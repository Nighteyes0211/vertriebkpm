<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\Organization\AppointmentController;
use App\Http\Controllers\Organization\BranchController;
use App\Http\Controllers\Organization\ContactController;
use App\Http\Controllers\Organization\DashboardController;
use App\Http\Controllers\Organization\FacilityStatusController;
use App\Http\Controllers\Organization\FacilityTypeController;
use App\Http\Controllers\Organization\ProductController;
use App\Http\Controllers\Organization\RoleController;
use App\Http\Controllers\Organization\StateController;
use App\Http\Controllers\Organization\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

/**
 * Redirects the user to their dashboard
 */
Route::get('/dashboard', function () {

    return redirect()->route('organization.dashboard');

})->middleware(['auth'])->name('dashboard');



Route::prefix('dashboard/o/')
    ->name('organization.')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::post('import-data', [DashboardController::class, 'importData'])->name('import.data');

        # Export data
        Route::get('export-data', [DashboardController::class, 'exportData'])->name('export.data');
        Route::get('export', [DashboardController::class, 'export'])->name('export');

        # Role
        Route::prefix('role')
            ->name('role.')
            ->group(function () {
                Route::get('/', [RoleController::class, 'index'])->middleware(['permission:role:index'])->name('index');
                Route::get('create', [RoleController::class, 'create'])->middleware(['permission:role:create'])->name('create');
                Route::get('{role:id}/edit', [RoleController::class, 'edit'])->middleware(['permission:role:edit'])->name('edit');
            });

        # User
        Route::prefix('users')
            ->name('user.')
            ->group(function () {

                Route::get('/', [UserController::class, 'index'])->middleware(['permission:user:index'])->name('index');
                Route::get('create', [UserController::class, 'create'])->middleware(['permission:user:create'])->name('create');
                Route::get('edit/{user}', [UserController::class, 'edit'])->middleware(['permission:user:edit'])->name('edit');
            });

        # Profile
        Route::view('profile', 'users.organization.profile')->name('profile');


        # Contact
        Route::prefix('contact')
            ->name('contact.')
            ->group(function () {
                Route::get('/', [ContactController::class, 'index'])->name('index');
                Route::get('create', [ContactController::class, 'create'])->name('create');
                Route::get('{contact:id}/edit', [ContactController::class, 'edit'])->name('edit');
            });

        # Facility
        Route::prefix('facility')
            ->name('facility.')
            ->group(function () {
                Route::get('/', [FacilityController::class, 'index'])->name('index');
                Route::get('create', [FacilityController::class, 'create'])->name('create');
                Route::get('{id}/edit', [FacilityController::class, 'edit'])->name('edit');
            });

        # FacilityStatus
        Route::prefix('facility/status')
            ->name('facility-status.')
            ->group(function () {
                Route::get('/', [FacilityStatusController::class, 'index'])->name('index');
                Route::get('create', [FacilityStatusController::class, 'create'])->name('create');
                Route::get('{facility_status:id}/edit', [FacilityStatusController::class, 'edit'])->name('edit');
            });

        # Branch
        Route::prefix('branch')
            ->name('branch.')
            ->group(function () {
                Route::get('/', [BranchController::class, 'index'])->name('index');
                Route::get('create', [BranchController::class, 'create'])->name('create');
                Route::get('{branch:id}/edit', [BranchController::class, 'edit'])->name('edit');
            });

        # Facility Type
        Route::prefix('facility-type')
            ->name('facility-type.')
            ->group(function () {
                Route::get('/', [FacilityTypeController::class, 'index'])->name('index');
                Route::get('create', [FacilityTypeController::class, 'create'])->name('create');
                Route::get('{facilityType:id}/edit', [FacilityTypeController::class, 'edit'])->name('edit');
            });

        # State
        Route::prefix('state')
            ->name('state.')
            ->group(function () {
                Route::get('/', [StateController::class, 'index'])->name('index');
                Route::get('create', [StateController::class, 'create'])->name('create');
                Route::get('{state:id}', [StateController::class, 'edit'])->name('edit');
            });

        # Calendar
        Route::get('calendar', [DashboardController::class, 'calendar'])->name('calendar');
        Route::get('appointment', [AppointmentController::class, 'index'])->name('appointment.index');
        Route::get('appointment/{appointment:id}', [AppointmentController::class, 'edit'])->name('appointment.edit');
        Route::post('appointment/{appointment:id}/delete', [AppointmentController::class, 'delete'])->name('appointment.delete');


        # Product
        Route::prefix('product')
            ->name('product.')
            ->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('index');
                Route::get('create', [ProductController::class, 'create'])->name('create');
                Route::get('{product:id}/edit', [ProductController::class, 'edit'])->name('edit');
            });
    });



/**
 * Auth routes
 */
require __DIR__ . '/auth.php';

?>
