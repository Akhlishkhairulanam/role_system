<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Api\ApiController;

use Illuminate\Http\Request;

use App\Models\Attendance;

use App\Models\Teacher;

use App\Models\TeacherAllocation;

use App\Models\Student;

class AbsensiController extends ApiController
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

        $data = TeacherAllocation::with([

            'classroom',

            'subject',

            'attendances.student',

        ])

        ->where(
            'teacher_id',
            $teacher->id
        )

        ->get();

        return $this->success(

            $data->toArray(),

            'Data absensi berhasil diambil.'

        );
    }

    public function showMapel($classroom_id)
    {
        $data = TeacherAllocation::with([

            'subject',

            'classroom',

        ])

        ->where(
            'classroom_id',
            $classroom_id
        )

        ->get();

        return $this->success(

            $data->toArray(),

            'Mapel berhasil diambil.'

        );
    }

    public function jurnal($allocation_id)
    {
        $data = Attendance::with([

            'student',

            'schedule',

        ])

        ->where(
            'teacher_allocation_id',
            $allocation_id
        )

        ->get();

        return $this->success(

            $data->toArray(),

            'Jurnal absensi berhasil diambil.'

        );
    }

    public function create($allocation_id)
    {
        $allocation = TeacherAllocation::findOrFail(
            $allocation_id
        );

        $students = Student::where(

            'classroom_id',

            $allocation->classroom_id

        )

        ->get();

        return $this->success(

            $students->toArray(),

            'Daftar siswa berhasil diambil.'

        );

    }

    public function store(Request $request)
    {
        $data = $request->validate([

            'student_id' => 'required',

            'teacher_allocation_id' => 'required',

            'schedule_id' => 'required',

            'date' => 'required',

            'status' => 'required',

            'note' => 'nullable',

        ]);
        $existing = Attendance::where([
        'schedule_id'=>$data['schedule_id'],
        'student_id'=>$data['student_id'],
        'date'=>$data['date']
        ])->first();

        if($existing){

        return $this->error(
        'Absensi sudah ada',
        400
        );

        }
        $attendance = Attendance::create(
            $data
        );

        return $this->success(

            $attendance->toArray(),

            'Absensi berhasil disimpan.'

        );
    }

    public function destroy($allocation_id, $date)
    {
        Attendance::where(

            'teacher_allocation_id',

            $allocation_id

        )

        ->where(

            'date',

            $date

        )

        ->delete();

        return $this->success(

            [],

            'Absensi berhasil dihapus.'

        );
    }

    public function rekap($allocation_id)
    {
        $rekap = Attendance::with([

            'student',

        ])

        ->where(

            'teacher_allocation_id',

            $allocation_id

        )

        ->get()

        ->groupBy('status');

        return $this->success(

            $rekap->toArray(),

            'Rekap absensi berhasil diambil.'

        );
    }
}
