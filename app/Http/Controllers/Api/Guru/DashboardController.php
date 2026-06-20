<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Api\ApiController;

use Illuminate\Http\Request;

use App\Models\Teacher;

use App\Models\TeacherAllocation;

class DashboardController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $teacher = Teacher::where(
            'user_id',
            $user->id
        )->first();

        if (!$teacher) {

            return $this->error(
                'Data guru tidak ditemukan',
                404
            );
        }

        $allocations = TeacherAllocation::with([

            'subject',

            'classroom',

            'schedules',

        ])

        ->where(
            'teacher_id',
            $teacher->id
        )

        ->get();

        $jumlahKelas =

        $allocations

        ->pluck('classroom_id')

        ->unique()

        ->count();

        $jumlahSiswa =

        $allocations

        ->sum(function ($item) {

            return $item->classroom

            ? $item->classroom->students()->count()

            : 0;

        });

        return $this->success([

            'teacher' => $teacher,

            'jumlah_kelas' => $jumlahKelas,

            'jumlah_siswa' => $jumlahSiswa,

            'jadwal' => $allocations,

        ], 'Dashboard guru berhasil diambil.');
    }
}
