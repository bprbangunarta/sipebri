<?php

use App\Enums\RoleName;
use App\Http\Controllers\AnalysisAdministrationController;
use App\Http\Controllers\AnalysisAssessmentController;
use App\Http\Controllers\AnalysisBusinessController;
use App\Http\Controllers\AnalysisCollateralController;
use App\Http\Controllers\AnalysisCorrectionController;
use App\Http\Controllers\AnalysisFinanceController;
use App\Http\Controllers\AnalysisMemorandumController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\BmpkController;
use App\Http\Controllers\CollateralController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\CommitteeMemberController;
use App\Http\Controllers\CreditAnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductParameterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyLocationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// The dashboard is the root page; people without access to it are sent to the first page they may open.
Route::get('/', DashboardController::class)->name('dashboard');
Route::redirect('dashboard', '/'); // the dashboard used to live here

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('two-factor-challenge', [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
    Route::post('two-factor-challenge', [TwoFactorChallengeController::class, 'store'])->name('two-factor.verify');
    Route::post('two-factor-challenge/resend', [TwoFactorChallengeController::class, 'resend'])->name('two-factor.resend');
    Route::post('two-factor-challenge/cancel', [TwoFactorChallengeController::class, 'cancel'])->name('two-factor.cancel');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::prefix('profile/two-factor')->name('profile.two-factor.')->group(function () {
        Route::post('email/send', [ProfileController::class, 'sendEmailCode'])->name('email.send');
        Route::post('email', [ProfileController::class, 'enableEmail'])->name('email.enable');
        Route::post('totp/start', [ProfileController::class, 'startTotp'])->name('totp.start');
        Route::post('totp/cancel', [ProfileController::class, 'cancelTotp'])->name('totp.cancel');
        Route::post('totp', [ProfileController::class, 'enableTotp'])->name('totp.enable');
        Route::delete('/', [ProfileController::class, 'disable'])->name('disable');
    });

    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('collaterals', [CollateralController::class, 'index'])->middleware('permission:collaterals.view')->name('collaterals.index');
    Route::middleware('permission:collaterals.manage')->group(function () {
        Route::get('collaterals/create', [CollateralController::class, 'create'])->name('collaterals.create');
        Route::post('collaterals', [CollateralController::class, 'store'])->name('collaterals.store');
        Route::get('collaterals/{collateral}/edit', [CollateralController::class, 'edit'])->name('collaterals.edit');
        Route::put('collaterals/{collateral}', [CollateralController::class, 'update'])->name('collaterals.update');
        Route::delete('collaterals/{collateral}', [CollateralController::class, 'destroy'])->name('collaterals.destroy');
    });

    Route::middleware('permission:loan-applications.view')->group(function () {
        Route::get('loan-applications', [LoanApplicationController::class, 'index'])->name('loan-applications.index');
        Route::get('loan-applications/lookup', [LoanApplicationController::class, 'lookup'])->name('loan-applications.lookup');
        Route::get('loan-applications/{loanApplication}', [LoanApplicationController::class, 'show'])->name('loan-applications.show');
    });
    Route::middleware('permission:loan-applications.manage')->scopeBindings()->group(function () {
        Route::post('loan-applications', [LoanApplicationController::class, 'store'])->name('loan-applications.store');
        Route::put('loan-applications/{loanApplication}', [LoanApplicationController::class, 'update'])->name('loan-applications.update');
        Route::post('loan-applications/{loanApplication}/collaterals', [LoanApplicationController::class, 'attachCollateral'])->name('loan-applications.collaterals.attach');
        Route::post('loan-applications/{loanApplication}/collaterals/new', [LoanApplicationController::class, 'storeCollateral'])->name('loan-applications.collaterals.store');
        Route::delete('loan-applications/{loanApplication}/collaterals/{collateral}', [LoanApplicationController::class, 'detachCollateral'])->name('loan-applications.collaterals.detach');
        Route::post('loan-applications/{loanApplication}/confirm', [LoanApplicationController::class, 'confirm'])->name('loan-applications.confirm');
        Route::delete('loan-applications/{loanApplication}', [LoanApplicationController::class, 'destroy'])->name('loan-applications.destroy');
    });

    Route::get('scheduling', [SchedulingController::class, 'index'])->middleware('permission:scheduling.view')->name('scheduling.index');
    Route::middleware('permission:scheduling.manage')->group(function () {
        Route::post('scheduling/{loanApplication}', [SchedulingController::class, 'store'])->name('scheduling.store');
        Route::post('scheduling/{loanApplication}/void', [SchedulingController::class, 'void'])->name('scheduling.void');
    });
    Route::post('scheduling/{loanApplication}/cancel', [SchedulingController::class, 'cancel'])->middleware('permission:surveys.manage')->name('scheduling.cancel');

    Route::get('surveys', [SurveyController::class, 'index'])->middleware('permission:surveys.view')->name('surveys.index');
    Route::middleware('permission:surveys.manage')->scopeBindings()->group(function () {
        Route::get('surveys/{loanApplication}', [SurveyController::class, 'show'])->name('surveys.show');
        Route::post('surveys/{loanApplication}/photos', [SurveyController::class, 'storePhoto'])->name('surveys.photos.store');
        Route::post('surveys/{loanApplication}/locations', [SurveyLocationController::class, 'store'])->name('surveys.locations.store');
        Route::delete('surveys/{loanApplication}/locations', [SurveyLocationController::class, 'destroy'])->name('surveys.locations.destroy');
        Route::delete('surveys/{loanApplication}/photos/{photo}', [SurveyController::class, 'destroyPhoto'])->name('surveys.photos.destroy');
        Route::post('surveys/{loanApplication}', [SurveyController::class, 'store'])->name('surveys.store');
    });

    Route::middleware('permission:credit-analysis.view')->prefix('credit-analysis')->name('credit-analysis.')->group(function () {
        Route::get('/', [CreditAnalysisController::class, 'index'])->name('index');
        Route::get('{loanApplication}', [CreditAnalysisController::class, 'show'])->name('show');
        Route::post('{loanApplication}/submit', [CreditAnalysisController::class, 'submit'])->name('submit');
        Route::post('{loanApplication}/corrections', [AnalysisCorrectionController::class, 'store'])->name('corrections.store');
        Route::post('{loanApplication}/corrections/{correction}/open', [AnalysisCorrectionController::class, 'open'])->name('corrections.open');
        Route::post('{loanApplication}/corrections/{correction}/decline', [AnalysisCorrectionController::class, 'decline'])->name('corrections.decline');
        Route::post('{loanApplication}/corrections/{correction}/close', [AnalysisCorrectionController::class, 'close'])->name('corrections.close');
        Route::put('{loanApplication}/memorandum', [AnalysisMemorandumController::class, 'update'])->name('memorandum.update');
        Route::put('{loanApplication}/administration', [AnalysisAdministrationController::class, 'update'])->name('administration.update');
        Route::put('{loanApplication}/collaterals', [AnalysisCollateralController::class, 'update'])->name('collaterals.update');
        Route::put('{loanApplication}/finance', [AnalysisFinanceController::class, 'updateFinance'])->name('finance.update');
        Route::put('{loanApplication}/ownership', [AnalysisFinanceController::class, 'updateOwnership'])->name('ownership.update');
        Route::put('{loanApplication}/five-c', [AnalysisAssessmentController::class, 'updateFiveC'])->name('five-c.update');
        Route::put('{loanApplication}/qualitative', [AnalysisAssessmentController::class, 'updateQualitative'])->name('qualitative.update');
        Route::post('{loanApplication}/businesses', [AnalysisBusinessController::class, 'store'])->name('businesses.store');
        Route::get('{loanApplication}/businesses/{business}', [AnalysisBusinessController::class, 'show'])->name('businesses.show');
        Route::put('{loanApplication}/businesses/{business}', [AnalysisBusinessController::class, 'update'])->name('businesses.update');
        Route::delete('{loanApplication}/businesses/{business}', [AnalysisBusinessController::class, 'destroy'])->name('businesses.destroy');
    });
    Route::middleware('permission:approvals.view')->prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('{loanApplication}', [ApprovalController::class, 'show'])->name('show');
        Route::post('{loanApplication}/decide', [ApprovalController::class, 'decide'])->name('decide');
    });

    // Reference data and access management belong to Super Admin only (a role check, not per-module permissions).
    Route::middleware('role:'.RoleName::SuperAdmin->value)->group(function () {
        Route::get('references/bmpk', [BmpkController::class, 'show'])->name('bmpk.show');
        Route::put('references/bmpk', [BmpkController::class, 'update'])->name('bmpk.update');
        Route::get('references/products/{product}/parameters', [ProductParameterController::class, 'show'])->name('products.parameters');
        Route::put('references/products/{product}/parameters', [ProductParameterController::class, 'update'])->name('products.parameters.update');

        Route::prefix('references/{resource}')->group(function () {
            Route::get('/', [ReferenceController::class, 'index'])->name('references.index');
            Route::post('/', [ReferenceController::class, 'store'])->name('references.store');
            Route::put('{id}', [ReferenceController::class, 'update'])->whereNumber('id')->name('references.update');
            Route::delete('{id}', [ReferenceController::class, 'destroy'])->whereNumber('id')->name('references.destroy');
        });

        Route::redirect('committees', '/committees/levels')->name('committees.index');
        Route::get('committees/levels', [CommitteeController::class, 'index'])->name('committees.levels');
        Route::get('committees/members', [CommitteeMemberController::class, 'index'])->name('committees.members');
        Route::put('committees/members/{user}', [CommitteeMemberController::class, 'update'])->whereNumber('user')->name('committees.members.update');
        Route::delete('committees/members/{user}', [CommitteeMemberController::class, 'destroy'])->whereNumber('user')->name('committees.members.destroy');
        Route::get('committees/special-rules', [CommitteeController::class, 'specialRules'])->name('committees.special-rules');
        Route::get('committees/exceptions', [CommitteeController::class, 'exceptions'])->name('committees.exceptions');
        Route::get('committees/authority', [CommitteeController::class, 'authority'])->name('committees.authority');
        Route::get('committees/{path}', [CommitteeController::class, 'show'])->name('committees.show');
        Route::scopeBindings()->group(function () {
            Route::post('committees', [CommitteeController::class, 'store'])->name('committees.store');
            Route::put('committees/{path}', [CommitteeController::class, 'update'])->name('committees.update');
            Route::delete('committees/{path}', [CommitteeController::class, 'destroy'])->name('committees.destroy');
            Route::put('committees/{path}/follow', [CommitteeController::class, 'follow'])->name('committees.follow');
            Route::post('committees/{path}/tiers', [CommitteeController::class, 'storeTier'])->name('committees.tiers.store');
            Route::put('committees/{path}/tiers/{tier}', [CommitteeController::class, 'updateTier'])->name('committees.tiers.update');
            Route::delete('committees/{path}/tiers/{tier}', [CommitteeController::class, 'destroyTier'])->name('committees.tiers.destroy');
            Route::put('committees/{path}/tiers/{tier}/move/{direction}', [CommitteeController::class, 'moveTier'])->whereIn('direction', ['up', 'down'])->name('committees.tiers.move');
        });

        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('permissions/generate', [PermissionController::class, 'generate'])->name('permissions.generate');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->whereNumber('permission')->name('permissions.destroy');

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('users', [UserController::class, 'index'])->name('users.index');

        Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
            Route::get('/', [AuditLogController::class, 'index'])->name('index');
            Route::get('export', [AuditLogController::class, 'export'])->name('export');
            Route::post('verify', [AuditLogController::class, 'verify'])->name('verify');
        });
    });
});
