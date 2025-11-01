<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManageRoleController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\SiteOfficer\PepTalkController;
use App\Http\Controllers\SiteOfficer\SicMeetingController;
use App\Http\Controllers\SiteOfficer\InductionTrainingController;
use App\Http\Controllers\SiteOfficer\SpecialTechnicalTrainingController;

// --------------------- AUTH ROUTES ---------------------
Route::post('/Login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/', [AuthController::class, 'loginView'])->name('Auth.LoginView');


// --------------------- DASHBOARD ---------------------
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'permission:view-dashboard'])
    ->name('dashboard');
// --------------------- ADMIN ROUTES ---------------------
Route::get('/Add-User/Page', [ManageUserController::class, 'index'])->middleware('permission:view-users')->name('admin.manageusers');
Route::get('/User/List', [ManageUserController::class, 'listAll'])->middleware('permission:view-users')->name('admin.users.list');
Route::get('/admin/users/{id}', [ManageUserController::class, 'show'])->middleware('permission:view-users')->name('admin.users.show');
Route::get('/admin/users/{id}/edit', [ManageUserController::class, 'edit'])->middleware('permission:edit-users')->name('admin.users.edit');
Route::post('/admin/users/{id}', [ManageUserController::class, 'update'])->middleware('permission:edit-users')->name('admin.users.update');
Route::post('/User/Add', [ManageUserController::class, 'store'])->middleware('permission:create-users')->name('admin.users.store');

// --------------------- ROLE MANAGEMENT ROUTES ---------------------
Route::get('/roles', [ManageRoleController::class, 'index'])->name('roles.index');
Route::get('/roles/list', [ManageRoleController::class, 'rolelist'])->name('roles.list');
Route::get('/roles/permissions', [ManageRoleController::class, 'getPermissions'])->name('roles.permissions');
Route::post('/roles', [ManageRoleController::class, 'store'])->middleware('permission:create-roles')->name('roles.store');
Route::match(['put', 'patch'], '/roles/{id}', [ManageRoleController::class, 'update'])->middleware('permission:edit-roles')->name('roles.update');
Route::delete('/roles/{id}', [ManageRoleController::class, 'destroy'])->middleware('permission:delete-roles')->name('roles.destroy');
Route::get('/roles/{id}/edit', [ManageRoleController::class, 'edit'])->middleware('permission:edit-roles')->name('roles.edit');

// --------------------- INDUCTION TRAINING ROUTES ---------------------
Route::get('/Induction/Training', [InductionTrainingController::class, 'index'])->middleware('permission:view_own_induction_training')->name('induction.training');
Route::post('/Induction/Training/Store', [InductionTrainingController::class, 'store'])->middleware('permission:add_own_induction_training')->name('induction.training.store');
Route::get('/Induction/Training/{id}/Edit', [InductionTrainingController::class, 'edit'])->middleware('permission:edit_own_induction_training')->name('induction.training.edit');
Route::post('/Induction/Training/{id}/Update', [InductionTrainingController::class, 'update'])->middleware('permission:edit_own_induction_training')->name('induction.training.update');
Route::get('/Induction/Training/{id}', [InductionTrainingController::class, 'show'])->middleware('permission:view_own_induction_training')->name('induction.training.show');

// --------------------- PEP TALK ROUTES ---------------------
Route::get('/Pep/Talk', [PepTalkController::class, 'index'])->middleware('permission:view_own_pep_talk')->name('pep.talk');
Route::post('/Pep/Talk/Store', [PepTalkController::class, 'store'])->middleware('permission:add_own_pep_talk')->name('pep.talk.store');
Route::get('/Pep/Talk/{id}/Edit', [PepTalkController::class, 'edit'])->middleware('permission:edit_own_pep_talk')->name('pep.talk.edit');
Route::post('/Pep/Talk/{id}/Update', [PepTalkController::class, 'update'])->middleware('permission:edit_own_pep_talk')->name('pep.talk.update');
Route::get('/Pep/Talk/{id}', [PepTalkController::class, 'show'])->middleware('permission:view_own_pep_talk')->name('pep.talk.show');
Route::get('/get-assigned-project', [PepTalkController::class, 'getAssignedProject'])->name('get.assigned.project');

// --------------------- SPECIAL TECHNICAL TRAINING ROUTES ---------------------

Route::get('/Special/Technical/Training', [SpecialTechnicalTrainingController::class, 'index'])->middleware('permission:view_own_STT')->name('special.technical.training');
Route::post('/Special/Technical/Training/Store', [SpecialTechnicalTrainingController::class, 'store'])->middleware('permission:add_own_STT')->name('special.technical.training.store');
Route::get('/Special/Technical/Training/{id}/Edit', [SpecialTechnicalTrainingController::class, 'edit'])->middleware('permission:edit_own_STT')->name('special.technical.training.edit');
Route::post('/Special/Technical/Training/{id}/Update', [SpecialTechnicalTrainingController::class, 'update'])->middleware('permission:edit_own_STT')->name('special.technical.training.update');
Route::get('/Special/Technical/Training/{id}', [SpecialTechnicalTrainingController::class, 'show'])->middleware('permission:edit_own_STT')->name('special.technical.training.show');

// --------------------- SIC MEETING ROUTES ---------------------

Route::get('/sic-meeting', [SicMeetingController::class, 'index'])->name('sic.meeting')->middleware('permission:view_own_SIC_meetings');
Route::post('/sic-meeting/store', [SicMeetingController::class, 'store'])->name('sic.meeting.store')->middleware('permission:add_own_SIC_meetings');
Route::get('/sic-meeting/{id}/edit', [SicMeetingController::class, 'edit'])->name('sic.meeting.edit')->middleware('permission:edit_own_SIC_meetings');
Route::post('/sic-meeting/{id}/update', [SicMeetingController::class, 'update'])->name('sic.meeting.update')->middleware('permission:edit_own_SIC_meetings');
Route::get('/sic-meeting/{id}', [SicMeetingController::class, 'show'])->name('sic.meeting.show')->middleware('permission:view_own_SIC_meetings');

Route::get('/admin/projects', [ProjectController::class, 'index'])
         ->name('admin.projects');

    Route::get('/projects/{id}', [ProjectController::class, 'show'])
         ->name('projects.show');
    Route::post('/projects/store', [ProjectController::class, 'store'])
         ->name('projects.store')
         ->middleware('permission:add_projects');

   
    Route::post('/projects/{id}/update', [ProjectController::class, 'update'])
         ->name('projects.update')
         ->middleware('permission:edit_projects');

    
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])
         ->name('projects.destroy')
         ->middleware('permission:delete_projects');