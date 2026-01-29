<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\RoomsController;

use App\Http\Controllers\Reception\DashboardController;
use App\Http\Controllers\Reception\ReceptionAuthController;

use App\Http\Controllers\Reception\Admin\RoomAdminController;
use App\Http\Controllers\Reception\Admin\StaffAdminController;
use App\Http\Controllers\Reception\Admin\AuditLogController;

use App\Http\Controllers\Reception\AccountController;

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

Route::get('/rooms', [RoomsController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{roomType:slug}', [RoomsController::class, 'show'])->name('rooms.show');

Route::view('/contact', 'contact')->name('contact');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/book/{roomType}', [BookingController::class, 'show'])->name('book');
Route::post('/book/{roomType}', [BookingController::class, 'store'])->name('book.store');

Route::get('/booking/{code}', [BookingController::class, 'confirmed'])->name('booking.confirmed');
Route::get('/booking/{code}/pdf', [BookingController::class, 'pdf'])->name('booking.pdf');

/*
|--------------------------------------------------------------------------
| Reception / Admin (Staff Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('reception')->name('reception.')->group(function () {

    // ✅ Login
    Route::get('/login', [ReceptionAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ReceptionAuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');

    // ✅ Logout (GET + POST)
    Route::get('/logout', [ReceptionAuthController::class, 'logoutGet'])->name('logout.get');
    Route::post('/logout', [ReceptionAuthController::class, 'logout'])->name('logout');

    // ✅ Forgot password (page only for now)
    Route::view('/forgot-password', 'reception.auth.forgot-password')
        ->name('forgot-password');

    /*
    |--------------------------------------------------------------------------
    | Reception Area (Admin + Reception)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:reception', 'reception.role:admin,reception'])->group(function () {

        // Landing (Reception home)
        Route::get('/', [DashboardController::class, 'index'])->name('bookings.index');

        // Bookings
        Route::get('/bookings', [DashboardController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create', [DashboardController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [DashboardController::class, 'store'])->name('bookings.store');

        Route::get('/bookings/{booking}', [DashboardController::class, 'show'])->name('bookings.show');

        Route::post('/bookings/{booking}/check-in', [DashboardController::class, 'checkIn'])->name('bookings.checkin');
        Route::post('/bookings/{booking}/check-out', [DashboardController::class, 'checkOut'])->name('bookings.checkout');
        Route::post('/bookings/{booking}/cancel', [DashboardController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{booking}/override-room', [DashboardController::class, 'overrideRoom'])->name('bookings.overrideRoom');

        // Rooms Board
        Route::get('/rooms', [DashboardController::class, 'rooms'])->name('rooms.index');

        // API
        Route::get('/api/available-rooms', [DashboardController::class, 'availableRoomsApi'])->name('api.availableRooms');

        // ✅ Staff self-service: change password
        Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password');
        Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:reception', 'reception.role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/', function () {
                return redirect()->route('reception.admin.rooms.index');
            })->name('index');

            // Staff Accounts
            Route::get('/staff', [StaffAdminController::class, 'index'])->name('staff.index');
            Route::get('/staff/create', [StaffAdminController::class, 'create'])->name('staff.create');
            Route::post('/staff', [StaffAdminController::class, 'store'])->name('staff.store');

            Route::get('/staff/{staff}/edit', [StaffAdminController::class, 'edit'])->name('staff.edit');
            Route::put('/staff/{staff}', [StaffAdminController::class, 'update'])->name('staff.update');

            Route::post('/staff/{staff}/toggle', [StaffAdminController::class, 'toggleActive'])->name('staff.toggle');
            Route::post('/staff/{staff}/reset-password', [StaffAdminController::class, 'resetPassword'])->name('staff.resetPassword');

            // Rooms Management
            Route::get('/rooms', [RoomAdminController::class, 'index'])->name('rooms.index');
            Route::get('/rooms/{physicalRoom}/edit', [RoomAdminController::class, 'edit'])->name('rooms.edit');
            Route::put('/rooms/{physicalRoom}', [RoomAdminController::class, 'update'])->name('rooms.update');
            Route::post('/rooms/bulk-assign', [RoomAdminController::class, 'bulkAssign'])->name('rooms.bulkAssign');

            // ✅ Audit Logs (THIS is what your navbar needs)
            Route::get('/logs', [AuditLogController::class, 'index'])->name('logs.index');
        });
});

/*
|--------------------------------------------------------------------------
| Default Laravel Auth Routes (Breeze/etc)
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}
