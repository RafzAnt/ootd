<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OotdController;
use App\Http\Controllers\OutfitPlanController;
use App\Http\Controllers\PickController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WearController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->middleware('guest')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/pick', PickController::class)->name('pick');
    Route::get('/history', HistoryController::class)->name('history');
    Route::get('/planner', [OutfitPlanController::class, 'index'])->name('planner.index');
    Route::post('/planner', [OutfitPlanController::class, 'store'])->name('planner.store');
    Route::delete('/planner/{outfitPlan}', [OutfitPlanController::class, 'destroy'])->name('planner.destroy');

    Route::resource('ootds', OotdController::class)->except('show');
    Route::post('/ootds/{ootd}/favorite', FavoriteController::class)->name('ootds.favorite');
    Route::post('/ootds/{ootd}/wear', [WearController::class, 'store'])->name('ootds.wear');
    Route::delete('/ootds/{ootd}/wear', [WearController::class, 'destroy'])->name('ootds.unwear');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/summary', [NotificationController::class, 'summary'])->name('notifications.summary');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::delete('/notifications', [NotificationController::class, 'clear'])->name('notifications.clear');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::post('/settings/notifications/test', [SettingsController::class, 'testNotification'])->name('settings.notifications.test');
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])->name('settings.destroy');
});
