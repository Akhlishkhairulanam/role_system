<?php

namespace App\Http\Controllers\Api\Ortu;

use App\Http\Controllers\Api\ApiController;

use Illuminate\Http\Request;

use App\Models\Student;

class DashboardController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $students = Student::with([

            'classroom',

            'grades',

            'attendances',

        ])

        ->where(

            'parent_user_id',

            $user->id

        )

        ->get();

        return $this->success([

            'children' => $students,

        ], 'Dashboard orang tua berhasil diambil.');
    }

    // ================= JADWAL =================

    public function schedule($id)
    {
        $student = Student::with([

            'classroom.teacher_allocations.subject',

            'classroom.teacher_allocations.schedules',

        ])

        ->find($id);

        if (!$student) {

            return $this->error(

                'Data siswa tidak ditemukan',

                404

            );
        }

        $schedule = [];

        foreach (

            $student->classroom->teacher_allocations

            as $allocation

        ) {

            foreach (

                $allocation->schedules

                as $item

            ) {

                $schedule[] = [

                    'day' => $item->day,

                    'time' =>

                    $item->start_time .

                    ' - ' .

                    $item->end_time,

                    'subject' =>

                    $allocation->subject->nama_mapel,

                ];
            }
        }

        return $this->success([

            'schedule' => $schedule,

        ], 'Jadwal berhasil diambil.');
    }

    // ================= ABSENSI =================

    public function attendance($id)
    {
        $student = Student::with([

            'attendances'

        ])

        ->find($id);

        if (!$student) {

            return $this->error(

                'Data siswa tidak ditemukan',

                404

            );
        }

        return $this->success([

            'attendance' =>

            $student->attendances,

        ], 'Absensi berhasil diambil.');
    }

    // ================= NILAI =================

    public function grades($id)
    {
        $student = Student::with([

            'grades.teacher_allocation.subject'

        ])

        ->find($id);

        if (!$student) {

            return $this->error(

                'Data siswa tidak ditemukan',

                404

            );
        }

        $grades = $student->grades->map(

            function ($grade) {

                return [

                    'subject' => optional(

                        optional(

                            $grade->teacher_allocation

                        )->subject

                    )->nama_mapel,

                    'score' => $grade->score,

                ];
            }

        );

        return $this->success([

            'grades' =>

            $grades->values(),

        ], 'Nilai berhasil diambil.');
    }

}
