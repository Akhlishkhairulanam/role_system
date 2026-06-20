<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;

// ADMIN
use App\Http\Controllers\Api\Admin\DataMasterController;
use App\Http\Controllers\Api\Admin\ScheduleController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\SppController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;

// GURU
use App\Http\Controllers\Api\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Api\Guru\JadwalGuruController;
use App\Http\Controllers\Api\Guru\AbsensiController;
use App\Http\Controllers\Api\Guru\NilaiController;
use App\Http\Controllers\Api\Guru\WaliKelasController;
use App\Http\Controllers\Api\Guru\AssignmentController as ApiAssignmentController;

// SISWA
use App\Http\Controllers\Api\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Api\Siswa\JadwalSiswaController as ApiJadwalSiswaController;
use App\Http\Controllers\Api\Siswa\RaporController as ApiRaporController;
use App\Http\Controllers\Api\Siswa\TugasSiswaController as ApiTugasSiswaController;

// ORTU
use App\Http\Controllers\Api\Ortu\DashboardController as OrtuDashboardController;
use App\Http\Controllers\Api\Ortu\PerkembanganController as ApiPerkembanganController;
use App\Http\Controllers\Api\Ortu\SppOrtuController as ApiSppOrtuController;

//notifikasi
use App\Http\Controllers\Api\NotificationController;

Route::prefix('v1')->group(function () {

    // ================= AUTH =================
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/siswa/login', [AuthController::class, 'loginStudent']);
    Route::post('/ortu/login', [AuthController::class, 'loginParent']);

    Route::middleware('auth:api')->group(function () {

        // ================= PROFILE =================
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/notifications', [NotificationController::class,'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class,'markAsRead']);

        // ================= ADMIN =================
        Route::prefix('admin')
        ->middleware('api.role:admin')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index']);

            // ==========================================
            // FIX: FULL CRUD MASTER DATA UNTUK MOBILE FLUTTER
            // ==========================================
            // CRUD SISWA (STUDENTS)
            Route::get('/students', [DataMasterController::class, 'getAllStudents']);
            Route::post('/students', [DataMasterController::class, 'storeStudent']);
            Route::put('/students/{id}', [DataMasterController::class, 'updateStudent']);
            Route::delete('/students/{id}', [DataMasterController::class, 'destroyStudent']);

            // CRUD GURU (TEACHERS)
            Route::get('/teachers', [DataMasterController::class, 'indexTeachers']);
            Route::post('/teachers', [DataMasterController::class, 'storeTeacher']);
            Route::put('/teachers/{id}', [DataMasterController::class, 'updateTeacher']);
            Route::delete('/teachers/{id}', [DataMasterController::class, 'destroyTeacher']);

            // CRUD KELAS (CLASSROOMS)
            Route::get('/classrooms', [DataMasterController::class, 'indexClassrooms']);
            Route::post('/classrooms', [DataMasterController::class, 'storeClassroom']);
            Route::put('/classrooms/{id}', [DataMasterController::class, 'updateClassroom']);
            Route::delete('/classrooms/{id}', [DataMasterController::class, 'destroyClassroom']);
            // ==========================================

            Route::apiResource('schedule', ScheduleController::class);
            Route::get('/settings', [SettingController::class, 'index']);
            Route::put('/settings', [SettingController::class, 'update']);

            // ---------- SPP ----------
            Route::prefix('spp')->group(function () {
                Route::get('/', [SppController::class, 'index']);
                Route::get('/verifikasi', [SppController::class, 'verification']);
                Route::get('/arsip', [SppController::class, 'archive']);
                Route::post('/generate', [SppController::class, 'store']);
                Route::post('/store-individual', [SppController::class, 'storeIndividual']);
                Route::get('/get-students/{classroom_id}', [SppController::class, 'getStudentsByClass']);
                Route::post('/publish-all', [SppController::class, 'publishAll']);
                Route::delete('/delete-all', [SppController::class, 'deleteAll']);
                Route::post('/dispensation/{id}', [SppController::class, 'saveDispensation']);
                Route::post('/dispensation/{id}/approve', [SppController::class, 'approveDispensation']);
                Route::post('/dispensation/{id}/reject', [SppController::class, 'rejectDispensation']);
                Route::post('/{id}/verify', [SppController::class, 'verify']);
                Route::post('/{id}/reject', [SppController::class, 'reject']);
                Route::post('/{id}/pay-manual', [SppController::class, 'payManual']);
                Route::post('/{id}/cancel-payment', [SppController::class, 'cancelPayment']);
                Route::post('/{id}/toggle', [SppController::class, 'togglePublish']);
                Route::put('/{id}', [SppController::class, 'update']);
                Route::delete('/{id}', [SppController::class, 'destroy']);
            });
        });

        // ================= GURU =================
        Route::prefix('guru')
        ->middleware('api.role:teacher')
        ->group(function () {
            Route::get('/dashboard', [GuruDashboardController::class, 'index']);
            Route::get('/jadwal-saya', [JadwalGuruController::class, 'index']);
            Route::apiResource('tugas', ApiAssignmentController::class);
            Route::post('/tugas/submission/{submission_id}/grade', [ApiAssignmentController::class, 'updateGrade']);
            Route::prefix('absensi')->group(function () {
                Route::get('/', [AbsensiController::class, 'index']);
                Route::get('/kelas/{classroom_id}', [AbsensiController::class, 'showMapel']);
                Route::get('/jurnal/{allocation_id}', [AbsensiController::class, 'jurnal']);
                Route::get('/input/{allocation_id}', [AbsensiController::class, 'create']);
                Route::post('/store', [AbsensiController::class, 'store']);
                Route::delete('/delete/{allocation_id}/{date}', [AbsensiController::class, 'destroy']);
                Route::get('/rekap/{allocation_id}', [AbsensiController::class, 'rekap']);
            });
            Route::prefix('nilai')->group(function () {
                Route::get('/', [NilaiController::class, 'index']);
                Route::get('/input/{allocation_id}', [NilaiController::class, 'create']);
                Route::post('/store/{allocation_id}', [NilaiController::class, 'store']);
            });
            Route::get('/monitoring-kelas', [WaliKelasController::class, 'index']);
            Route::post('/rapor/toggle/{classroom_id}', [WaliKelasController::class, 'toggleRapor']);
            Route::get('/rapor/show/{student_id}', [WaliKelasController::class, 'show']);
        });

        // ================= SISWA =================
        Route::prefix('siswa')
        ->middleware('api.role:student')
        ->group(function () {
            Route::get('/dashboard',[SiswaDashboardController::class,'index']);
            Route::get('/attendance',[SiswaDashboardController::class,'attendance']);
            Route::get('/jadwal',[ApiJadwalSiswaController::class,'index']);
            Route::get('/rapor',[ApiRaporController::class,'index']);
            Route::get('/tugas',[ApiTugasSiswaController::class,'index']);
            Route::get('/tugas/{id}',[ApiTugasSiswaController::class,'show']);
            Route::post('/attendance',[SiswaDashboardController::class, 'storeAttendance']);
            Route::post('/tugas/{id}/submit',[ApiTugasSiswaController::class,'store']);
            Route::delete('/tugas/{id}/delete',[ApiTugasSiswaController::class,'destroy']);
        });

        // ================= ORTU =================
        Route::prefix('ortu')
        ->middleware('api.role:parent')
        ->group(function () {
            Route::get('/dashboard', [OrtuDashboardController::class, 'index']);
            Route::get('/perkembangan', [ApiPerkembanganController::class, 'index']);
            Route::get('/tagihan', [ApiSppOrtuController::class, 'index']);
            Route::post('/tagihan/{id}/pay', [ApiSppOrtuController::class, 'uploadBukti']);
            Route::post('/spp/dispensasi/{id}', [ApiSppOrtuController::class, 'storeDispensation']);
            Route::get('/child/{id}/schedule', [OrtuDashboardController::class, 'schedule']);
            Route::get('/child/{id}/attendance', [OrtuDashboardController::class, 'attendance']);
            Route::get('/child/{id}/grades', [OrtuDashboardController::class, 'grades']);
        });
    });
});
