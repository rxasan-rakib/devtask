<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications System
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/check-due', [NotificationController::class, 'checkDue'])->name('notifications.check-due');
    Route::get('/notifications/{notification}/go', [NotificationController::class, 'readAndRedirect'])->name('notifications.read-and-redirect');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.unread');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks & Projects
    Route::get('/tasks/kanban/view', [TaskController::class, 'kanban'])->name('tasks.kanban');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::patch('/tasks/{task}/assign', [TaskController::class, 'updateAssignee'])->name('tasks.assign');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
    Route::delete('/tasks/{task}/comments/{comment}', [TaskController::class, 'deleteComment'])->name('tasks.comments.destroy');
    Route::resource('tasks', TaskController::class);

    // Clients
    Route::resource('clients', ClientController::class);

    // Reminders
    Route::get('/reminders/check-due', [ReminderController::class, 'checkDue'])->name('reminders.check-due');
    Route::patch('/reminders/{reminder}/mark-done', [ReminderController::class, 'markDone'])->name('reminders.mark-done');
    Route::patch('/reminders/{reminder}/mark-pending', [ReminderController::class, 'markPending'])->name('reminders.mark-pending');
    Route::patch('/reminders/{reminder}/mark-sent', [ReminderController::class, 'markSent'])->name('reminders.mark-sent');
    Route::resource('reminders', ReminderController::class);

    // Team Members / Users
    Route::resource('users', UserController::class);

    // Dynamic Roles & Permissions
    Route::resource('roles', RoleController::class);

    // Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // System & SMTP Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [SettingController::class, 'sendTestEmail'])->name('settings.test-email');

});
