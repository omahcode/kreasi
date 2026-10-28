<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\PklGroupController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\LogbookController;
use App\Http\Controllers\Teacher\PklController;
use App\Http\Controllers\Teacher\ProjectController;
use App\Http\Controllers\Teacher\SubmissionReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        return redirect()->route('siswa.dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function () {
        return back()->with('status', 'Fitur reset sandi melalui email akan mengirimkan token jika mail server telah dikonfigurasi.');
    })->name('password.email');
});

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Common Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Phase 1 & 2: Academic & User Management)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // 1. User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // 2. Classes & Academic Years
        Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
        Route::post('/classes', [ClassController::class, 'storeClass'])->name('classes.store');
        Route::put('/classes/{id}', [ClassController::class, 'updateClass'])->name('classes.update');
        Route::delete('/classes/{id}', [ClassController::class, 'destroyClass'])->name('classes.destroy');
        Route::post('/academic-years', [ClassController::class, 'storeAcademicYear'])->name('academic-years.store');
        Route::post('/academic-years/{id}/activate', [ClassController::class, 'setActiveYear'])->name('academic-years.activate');

        // 3. Student Excel Import
        Route::get('/students/import', [StudentImportController::class, 'index'])->name('students.import');
        Route::get('/students/import/template', [StudentImportController::class, 'downloadTemplate'])->name('students.import.template');
        Route::post('/students/import/preview', [StudentImportController::class, 'preview'])->name('students.import.preview');
        Route::post('/students/import/commit', [StudentImportController::class, 'commit'])->name('students.import.commit');

        // 4. Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');

        // 5. Companies / Mitra Industri DU/DI
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::put('/companies/{id}', [CompanyController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{id}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::delete('/companies/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');

        // 6. Ploting Kelompok PKL
        Route::resource('/pkl-groups', PklGroupController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Guru Routes (Phase 1, 3 & 4)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'guru'])->name('dashboard');

        // PjBL Projects Management
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::post('/projects/auto-group', [ProjectController::class, 'autoGroupPreview'])->name('projects.auto-group');
        Route::post('/projects/batch-store', [ProjectController::class, 'batchStoreGroupProjects'])->name('projects.batch-store');
        Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Reviews & Grading
        Route::get('/submissions', [SubmissionReviewController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{id}', [SubmissionReviewController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{id}/review', [SubmissionReviewController::class, 'storeReview'])->name('submissions.review');

        // PKL Ploting Kelompok Siswa Bimbingan
        Route::resource('/pkl-groups', App\Http\Controllers\Teacher\PklGroupController::class);

        // PKL Management (Phase 4)
        Route::get('/pkl', [PklController::class, 'index'])->name('pkl.index');
        Route::get('/pkl/{id}', [PklController::class, 'show'])->name('pkl.show');
        Route::post('/pkl/{id}/review-title', [PklController::class, 'reviewTitle'])->name('pkl.title.review');
        Route::post('/pkl/chapters/{chapterId}/review', [PklController::class, 'reviewChapter'])->name('pkl.chapters.review');
        Route::post('/pkl/{id}/final-approval', [PklController::class, 'finalApproval'])->name('pkl.final-approval');
    });

    /*
    |--------------------------------------------------------------------------
    | Siswa & Ketua Kelompok Routes (Phase 1, 3 & 4)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'siswa'])->name('dashboard');

        // Siswa PjBL
        Route::get('/projects', [App\Http\Controllers\Student\ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{id}', [App\Http\Controllers\Student\ProjectController::class, 'show'])->name('projects.show');
        Route::post('/projects/{id}/submit', [App\Http\Controllers\Student\ProjectController::class, 'submitTask'])->name('projects.submit');

        // Siswa Logbook
        Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
        Route::get('/logbook/create', [LogbookController::class, 'create'])->name('logbook.create');
        Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');

        // Siswa PKL (Phase 4)
        Route::get('/pkl', [App\Http\Controllers\Student\PklController::class, 'index'])->name('pkl.index');
        Route::post('/pkl/title', [App\Http\Controllers\Student\PklController::class, 'storeTitle'])->name('pkl.title.store');
        Route::put('/pkl/title', [App\Http\Controllers\Student\PklController::class, 'updateTitle'])->name('pkl.title.update');
        Route::post('/pkl/chapters/{chapterNumber}/upload', [App\Http\Controllers\Student\PklController::class, 'uploadChapter'])->name('pkl.chapters.upload');
    });
});
