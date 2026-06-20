<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Api\ApiController;

use Illuminate\Http\Request;

use App\Models\Student;

class DashboardController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $student = Student::with([

            'classroom',

            'grades',

            'attendances',

        ])

        ->where(
            'user_id',
            $user->id
        )

        ->first();

        if (!$student) {

            return $this->error(
                'Data siswa tidak ditemukan',
                404
            );
        }

        return $this->success([

            'student' => $student,

            'rata_nilai' =>

            round(

                $student->grades->avg('score') ?? 0,

                2

            ),

            'persentase_hadir' =>

            round(

                ($student->attendances

                ->where('status','H')

                ->count()

                /

                max(

                    $student->attendances->count(),

                    1

                ))

                *100,

                0

            ),

        ], 'Dashboard siswa berhasil diambil.');
    }
   // ================= RIWAYAT PRESENSI =================

public function attendance(Request $request)
{
    $user = $request->user();

    $student = Student::with([
        'attendances.schedule.teacher_allocation.subject'
    ])
    ->where(
        'user_id',
        $user->id
    )
    ->first();

    if (!$student) {

        return $this->error(
            'Data siswa tidak ditemukan',
            404
        );
    }

    return $this->success(

        $student->attendances,

        'Riwayat presensi berhasil diambil.'

    );
}


// ================= SCAN PRESENSI =================

public function storeAttendance(Request $request)
{
    $request->validate([

        'schedule_id' => 'required',

    ]);

    $user = $request->user();

    $student = Student::where(

        'user_id',

        $user->id

    )->first();

    if (!$student) {

        return $this->error(

            'Data siswa tidak ditemukan',

            404

        );
    }

    $attendance = \App\Models\Attendance::create([

        'student_id' => $student->id,

        'schedule_id' => $request->schedule_id,

        'teacher_allocation_id' => $request->teacher_allocation_id,

        'date' => now(),

        'status' => 'H',

        'note' => 'QR Scan',

    ]);

    return $this->success(

        $attendance->toArray(),

        'Presensi berhasil.'

    );
}
}
