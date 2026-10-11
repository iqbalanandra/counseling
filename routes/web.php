<?php

use App\Models\SchoolSetting;
use App\Presentation\Http\Controllers\AchievementController;
use App\Presentation\Http\Controllers\AuditLogController;
use App\Presentation\Http\Controllers\CounselingController;
use App\Presentation\Http\Controllers\DashboardController;
use App\Presentation\Http\Controllers\NotificationController;
use App\Presentation\Http\Controllers\ReportController;
use App\Presentation\Http\Controllers\RoleController;
use App\Presentation\Http\Controllers\SchoolClassController;
use App\Presentation\Http\Controllers\SchoolSettingController;
use App\Presentation\Http\Controllers\StudentController;
use App\Presentation\Http\Controllers\UserManagementController;
use App\Presentation\Http\Controllers\ViolationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'school' => SchoolSetting::getSettings(),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students & Classes
    Route::resource('students', StudentController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('classes', SchoolClassController::class)->only(['index', 'store', 'update', 'destroy']);

    // Counseling
    Route::redirect('counseling', '/counseling/requests');

    // Counseling – Requests
    Route::get('counseling/requests', [CounselingController::class, 'requestsIndex'])->name('counseling.requests.index');
    Route::get('counseling/requests/{counselingRequest}', [CounselingController::class, 'showRequest'])->name('counseling.requests.show');
    Route::post('counseling/requests', [CounselingController::class, 'submitRequest'])->name('counseling.requests.store');
    Route::patch('counseling/requests/{counselingRequest}/approve', [CounselingController::class, 'approveRequest'])->name('counseling.requests.approve');
    Route::patch('counseling/requests/{counselingRequest}/reschedule', [CounselingController::class, 'rescheduleRequest'])->name('counseling.requests.reschedule');

    // Counseling – Sessions
    Route::get('counseling/sessions', [CounselingController::class, 'sessionsIndex'])->name('counseling.sessions.index');
    Route::post('counseling/sessions/{counselingSession}/record', [CounselingController::class, 'recordSession'])->name('counseling.sessions.record');

    // Violations
    Route::get('violations', [ViolationController::class, 'index'])->name('violations.index');
    Route::post('violations', [ViolationController::class, 'store'])->name('violations.store');
    Route::patch('violations/{violation}', [ViolationController::class, 'update'])->name('violations.update');
    Route::delete('violations/{violation}', [ViolationController::class, 'destroy'])->name('violations.destroy');

    // Achievements
    Route::get('achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::post('achievements', [AchievementController::class, 'store'])->name('achievements.store');
    Route::patch('achievements/{achievement}', [AchievementController::class, 'update'])->name('achievements.update');
    Route::delete('achievements/{achievement}', [AchievementController::class, 'destroy'])->name('achievements.destroy');

    // Administration
    Route::get('admin', function () {
        if (request()->user()?->can('school.manage')) {
            return redirect()->route('admin.school-profile.edit');
        }
        if (request()->user()?->can('role.view')) {
            return redirect()->route('roles.index');
        }
        if (request()->user()?->can('user.view')) {
            return redirect()->route('users.index');
        }
        if (request()->user()?->can('audit_log.view')) {
            return redirect()->route('audit-logs.index');
        }

        return redirect()->route('dashboard');
    })->name('admin');

    Route::resource('users', UserManagementController::class)->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'users.index',
            'show' => 'users.show',
            'store' => 'users.store',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]);

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::patch('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Notifications
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('admin/school-profile', [SchoolSettingController::class, 'edit'])->name('admin.school-profile.edit');
    Route::post('admin/school-profile', [SchoolSettingController::class, 'update'])->name('admin.school-profile.update');
});

require __DIR__.'/settings.php';
