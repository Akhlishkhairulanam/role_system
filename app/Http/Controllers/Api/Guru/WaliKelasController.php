<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Api\ApiController;

use App\Models\Classroom;

use App\Models\Student;

class WaliKelasController extends ApiController
{
    public function index()
    {
        $kelas = Classroom::with([

            'students',

            'waliKelas',

        ])->get();

        return $this->success(

            $kelas->toArray(),

            'Monitoring kelas berhasil.'

        );
    }

    public function toggleRapor($classroom_id)
    {
        $classroom = Classroom::findOrFail($classroom_id);

        $classroom->is_rapor_published =

        !$classroom->is_rapor_published;

        $classroom->save();

        return $this->success(

            $classroom->toArray(),

            'Status rapor berhasil diubah.'

        );
    }

    public function show($student_id)
    {
        $student = Student::with([

            'grades',

            'attendances',

            'classroom',

        ])

        ->findOrFail($student_id);

        return $this->success(

            $student->toArray(),

            'Detail rapor siswa berhasil diambil.'

        );
    }
}
