<?php

use App\Http\Controllers\Admin\AuditLogExportController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/history/groups-and-societies', [HomeController::class, 'societies'])->name('history.societies');
Route::get('/history/friars', [HomeController::class, 'friars'])->name('history.friars');
Route::get('/history/pastoral-council-chairpersons', [HomeController::class, 'pastoralCouncil'])->name('history.pastoral-council');
Route::get('/ministries', [HomeController::class, 'ministries'])->name('ministries');
Route::get('/ministries/{ministry:slug}', [HomeController::class, 'ministry'])->name('ministries.show');
Route::get('/events', [HomeController::class, 'events'])->name('events');
Route::get('/sermons', [HomeController::class, 'sermons'])->name('sermons');
Route::get('/sermons/{sermon}', [HomeController::class, 'sermon'])->name('sermons.show');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin/audit-logs/export', AuditLogExportController::class)->name('admin.audit-logs.export');
});

require __DIR__.'/auth.php';

