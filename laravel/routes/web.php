<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ChildController;
use App\Http\Controllers\Admin\VaccineController;
use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboard;
use App\Http\Controllers\Parent\ChildController as ParentChildController;
use App\Http\Controllers\Parent\HospitalController as ParentHospitalController;
use App\Http\Controllers\Parent\AppointmentController as ParentAppointmentController;
use App\Http\Controllers\Parent\VaccinationController as ParentVaccinationController;
use App\Http\Controllers\Parent\ProfileController as ParentProfileController;
use App\Http\Controllers\Parent\NotificationController as ParentNotificationController;
use App\Http\Controllers\Parent\SettingsController as ParentSettingsController;
use App\Http\Controllers\Parent\RequestController as ParentRequestController;
use App\Http\Controllers\Hospital\DashboardController as HospitalDashboard;
use App\Http\Controllers\Hospital\AppointmentController as HospitalAppointmentController;
use App\Http\Controllers\Hospital\VaccineInventoryController as HospitalInventoryController;
use App\Http\Controllers\Hospital\VaccinationController as HospitalVaccinationController;
use App\Http\Controllers\Hospital\ProfileController as HospitalProfileController;
use App\Http\Controllers\Hospital\NotificationController as HospitalNotificationController;
use App\Http\Controllers\Hospital\SettingsController as HospitalSettingsController;

Route::get('/', function () {
    if (auth()->check()) return redirect()->route(auth()->user()->role . '.dashboard');
    return redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');
    Route::get('/register/parent', [AuthController::class, 'showRegisterParent'])->name('register.parent');
    Route::post('/register/parent', [AuthController::class, 'registerParent'])->name('register.parent.submit');
    Route::get('/register/hospital', [AuthController::class, 'showRegisterHospital'])->name('register.hospital');
    Route::post('/register/hospital', [AuthController::class, 'registerHospital'])->name('register.hospital.submit');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Children
    Route::get('/children', [ChildController::class, 'index'])->name('children.index');
    Route::get('/children/{child}', [ChildController::class, 'show'])->name('children.show');
    // Vaccines
    Route::get('/vaccines', [VaccineController::class, 'index'])->name('vaccines.index');
    Route::get('/vaccines/create', [VaccineController::class, 'create'])->name('vaccines.create');
    Route::post('/vaccines', [VaccineController::class, 'store'])->name('vaccines.store');
    Route::get('/vaccines/{vaccine}/edit', [VaccineController::class, 'edit'])->name('vaccines.edit');
    Route::put('/vaccines/{vaccine}', [VaccineController::class, 'update'])->name('vaccines.update');
    Route::delete('/vaccines/{vaccine}', [VaccineController::class, 'destroy'])->name('vaccines.destroy');
    // Hospitals
    Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals.index');
    Route::get('/hospitals/create', [HospitalController::class, 'create'])->name('hospitals.create');
    Route::post('/hospitals', [HospitalController::class, 'store'])->name('hospitals.store');
    Route::get('/hospitals/{hospital}', [HospitalController::class, 'show'])->name('hospitals.show');
    Route::get('/hospitals/{hospital}/edit', [HospitalController::class, 'edit'])->name('hospitals.edit');
    Route::put('/hospitals/{hospital}', [HospitalController::class, 'update'])->name('hospitals.update');
    Route::post('/hospitals/{hospital}/toggle-status', [HospitalController::class, 'toggleStatus'])->name('hospitals.toggle-status');
    Route::post('/hospitals/{hospital}/approve', [HospitalController::class, 'approve'])->name('hospitals.approve');
    Route::delete('/hospitals/{hospital}', [HospitalController::class, 'destroy'])->name('hospitals.destroy');
    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    // Requests
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{request_model}', [RequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{request_model}/review', [RequestController::class, 'review'])->name('requests.review');
    Route::post('/requests/{request_model}/resolve', [RequestController::class, 'resolve'])->name('requests.resolve');
    Route::post('/requests/{request_model}/close', [RequestController::class, 'close'])->name('requests.close');
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    // Settings
    Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
});

// Parent routes
Route::prefix('parent')->name('parent.')->middleware(['auth', 'parent'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [ParentDashboard::class, 'index'])->name('dashboard');
    // Children
    Route::get('/children', [ParentChildController::class, 'index'])->name('children.index');
    Route::get('/children/create', [ParentChildController::class, 'create'])->name('children.create');
    Route::post('/children', [ParentChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}', [ParentChildController::class, 'show'])->name('children.show');
    Route::get('/children/{child}/edit', [ParentChildController::class, 'edit'])->name('children.edit');
    Route::put('/children/{child}', [ParentChildController::class, 'update'])->name('children.update');
    Route::delete('/children/{child}', [ParentChildController::class, 'destroy'])->name('children.destroy');
    // Hospitals
    Route::get('/hospitals', [ParentHospitalController::class, 'index'])->name('hospitals.index');
    Route::get('/hospitals/{hospital}', [ParentHospitalController::class, 'show'])->name('hospitals.show');

    // Requests
    Route::get('/requests/create', [ParentRequestController::class, 'create'])->name('requests.create');
    Route::get('/requests/{request_model}', [ParentRequestController::class, 'show'])->name('requests.show');
    Route::get('/requests', [ParentRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [ParentRequestController::class, 'store'])->name('requests.store');

    // Appointments
    Route::get('/appointments', [ParentAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [ParentAppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [ParentAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [ParentAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [ParentAppointmentController::class, 'cancel'])->name('appointments.cancel');
    // Vaccinations
    Route::get('/vaccinations/schedule', [ParentVaccinationController::class, 'schedule'])->name('vaccinations.schedule');
    Route::get('/vaccinations/history', [ParentVaccinationController::class, 'history'])->name('vaccinations.history');
    Route::get('/vaccinations/{record}', [ParentVaccinationController::class, 'show'])->name('vaccinations.show');
    // Profile
    Route::get('/profile', [ParentProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ParentProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ParentProfileController::class, 'updatePassword'])->name('profile.password');
    // Notifications
    Route::get('/notifications', [ParentNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read', [ParentNotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [ParentNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    // Settings
    Route::get('/settings', [ParentSettingsController::class, 'show'])->name('settings');
    Route::put('/settings/password', [ParentSettingsController::class, 'updatePassword'])->name('settings.password');
});

// Hospital routes
Route::prefix('hospital')->name('hospital.')->middleware(['auth', 'hospital'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [HospitalDashboard::class, 'index'])->name('dashboard');
    // Appointments
    Route::get('/appointments', [HospitalAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [HospitalAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/confirm', [HospitalAppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/complete', [HospitalAppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('/appointments/{appointment}/no-show', [HospitalAppointmentController::class, 'noShow'])->name('appointments.no-show');
    // Vaccine Inventory
    Route::get('/vaccines', [HospitalInventoryController::class, 'index'])->name('vaccines.index');
    Route::put('/vaccines/{inventory}', [HospitalInventoryController::class, 'update'])->name('vaccines.update');
    // Vaccination Records
    Route::get('/vaccinations', [HospitalVaccinationController::class, 'index'])->name('vaccinations.index');
    Route::get('/vaccinations/{record}', [HospitalVaccinationController::class, 'show'])->name('vaccinations.show');
    // Profile
    Route::get('/profile', [HospitalProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [HospitalProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [HospitalProfileController::class, 'updatePassword'])->name('profile.password');
    // Notifications
    Route::get('/notifications', [HospitalNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read', [HospitalNotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [HospitalNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    // Settings
    Route::get('/settings', [HospitalSettingsController::class, 'show'])->name('settings');
    Route::put('/settings/password', [HospitalSettingsController::class, 'updatePassword'])->name('settings.password');
});
