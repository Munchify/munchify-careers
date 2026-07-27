<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicJobController;
use App\Http\Controllers\ApplicationWizardController;
use App\Http\Controllers\StatusTrackerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobManagementController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\PipelineTemplateController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\WhatsAppWebhookController;

// ==========================================
// 1. PUBLIC CAREERS PORTAL
// ==========================================
Route::get('/', [PublicJobController::class, 'index'])->name('careers.home');
Route::get('/jobs', [PublicJobController::class, 'jobs'])->name('careers.jobs');
Route::get('/jobs/{ulid}', [PublicJobController::class, 'show'])->name('careers.jobs.show');

// Application Wizard (Multi-step form)
Route::get('/apply/{ulid}/step/{step}', [ApplicationWizardController::class, 'showStep'])->name('apply.step');
Route::post('/apply/{ulid}/step/{step}', [ApplicationWizardController::class, 'saveStep'])->name('apply.step.save');
Route::post('/apply/{ulid}/submit', [ApplicationWizardController::class, 'submit'])->name('apply.submit');
Route::get('/apply/{ulid}/success', [ApplicationWizardController::class, 'success'])->name('apply.success');

// Candidate Status Tracker
Route::get('/application/{ulid}/status', [StatusTrackerController::class, 'show'])->name('application.status');


// ==========================================
// 2. AUTHENTICATION
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ==========================================
// 3. INTERNAL RECRUITMENT DASHBOARD (AUTH REQUIRED)
// ==========================================
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    
    // Overview (Accessible to all internal roles)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.overview');

    // Jobs Management
    Route::prefix('jobs')->group(function () {
        // Admin / HR only: create, edit, save jobs
        Route::middleware(['role:admin,hr_manager'])->group(function () {
            Route::get('/create', [JobManagementController::class, 'create'])->name('jobs.create');
            Route::post('/create', [JobManagementController::class, 'store']);
            Route::post('/ai-generate', [JobManagementController::class, 'aiGenerate'])->name('jobs.ai-generate');
            Route::post('/ai-generate-questions', [JobManagementController::class, 'aiGenerateQuestions'])->name('jobs.ai-generate-questions');
            Route::get('/{id}/edit', [JobManagementController::class, 'edit'])->name('jobs.edit');
            Route::post('/{id}/edit', [JobManagementController::class, 'update']);
            Route::post('/{id}/status', [JobManagementController::class, 'updateStatus'])->name('jobs.status');
            Route::post('/{id}/duplicate', [JobManagementController::class, 'duplicate'])->name('jobs.duplicate');
            Route::post('/{id}/delete', [JobManagementController::class, 'destroy'])->name('jobs.delete');
        });

        // Hiring / Interviewer / Viewer with job access: Kanban, detail view
        Route::middleware(['job.access'])->group(function () {
            Route::get('/', [JobManagementController::class, 'index'])->name('jobs.manage');
            Route::get('/{job}', [JobManagementController::class, 'show'])->name('jobs.show'); // Kanban board view
        });
    });

    // Applications Management
    Route::prefix('applications')->group(function () {
        // List all applications (Filterable)
        Route::get('/', [ApplicationController::class, 'index'])->name('applications.index');
        
        // Export CSV/Excel
        Route::get('/export', [ApplicationController::class, 'export'])->name('applications.export');
        
        // Bulk actions
        Route::post('/bulk', [ApplicationController::class, 'bulkAction'])->name('applications.bulk');

        // Specific Application routes (Job assignment/access protected)
        Route::middleware(['job.access'])->group(function () {
            Route::get('/{application}', [ApplicationController::class, 'show'])->name('applications.show');
            Route::post('/{application}/move-stage', [ApplicationController::class, 'moveStage'])->name('applications.move-stage');
            Route::post('/{application}/score', [ApplicationController::class, 'submitScore'])->name('applications.score');
            Route::post('/{application}/note', [ApplicationController::class, 'submitNote'])->name('applications.note');
            Route::post('/{application}/star', [ApplicationController::class, 'toggleStar'])->name('applications.star');
            Route::post('/{application}/hire', [ApplicationController::class, 'hire'])->name('applications.hire');
            Route::post('/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
        });
    });

    // Interviews Management
    Route::prefix('interviews')->group(function () {
        Route::get('/', [InterviewController::class, 'index'])->name('interviews.index');
        Route::post('/schedule', [InterviewController::class, 'schedule'])->name('interviews.schedule');
        Route::post('/{interview}/status', [InterviewController::class, 'updateStatus'])->name('interviews.status.update');
        Route::post('/{interview}/feedback', [InterviewController::class, 'submitFeedback'])->name('interviews.feedback');
    });

    // Pipeline Templates (Admin/HR Manager only)
    Route::middleware(['role:admin,hr_manager'])->prefix('pipelines')->group(function () {
        Route::get('/', [PipelineTemplateController::class, 'index'])->name('pipelines.index');
        Route::get('/create', [PipelineTemplateController::class, 'create'])->name('pipelines.create');
        Route::post('/create', [PipelineTemplateController::class, 'store']);
        Route::get('/{id}/edit', [PipelineTemplateController::class, 'edit'])->name('pipelines.edit');
        Route::post('/{id}/edit', [PipelineTemplateController::class, 'update']);
        Route::delete('/{id}', [PipelineTemplateController::class, 'destroy'])->name('pipelines.destroy');
    });

    // Analytics Dashboard (Admin, HR, Hiring Manager)
    Route::middleware(['role:admin,hr_manager,hiring_manager'])->get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Settings (Admin, HR Manager)
    Route::middleware(['role:admin,hr_manager'])->prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        
        // Team Management CRUD
        Route::post('/team', [SettingsController::class, 'saveTeamUser'])->name('settings.team.save');
        Route::post('/team/{id}/toggle', [SettingsController::class, 'toggleTeamUser'])->name('settings.team.toggle');
        
        // Departments CRUD
        Route::post('/departments', [SettingsController::class, 'saveDepartment'])->name('settings.department.save');
        Route::post('/departments/{id}/toggle', [SettingsController::class, 'toggleDepartment'])->name('settings.department.toggle');

        // Notification Templates Edit
        Route::post('/notifications/{id}', [SettingsController::class, 'saveNotificationTemplate'])->name('settings.notifications.save');

        // Gateway Configurations (SMTP & Hostpinnacle SMS)
        Route::post('/gateways', [SettingsController::class, 'saveGateways'])->name('settings.gateways.save');
        Route::post('/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
        Route::post('/test-sms', [SettingsController::class, 'testSms'])->name('settings.test-sms');
        Route::post('/test-template', [SettingsController::class, 'testTemplate'])->name('settings.test-template');
    });

    // Audit Log (Admin only)
    Route::middleware(['role:admin'])->get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
});


// ==========================================
// 4. WHATSAPP METRICS / WEBHOOKS
// ==========================================
Route::get('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'handle']);
