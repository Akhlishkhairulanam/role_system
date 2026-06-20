<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\TeacherAllocation;

class JadwalSiswaController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $student = Student::where('user_id',$user->id)

        ->first();

        if(!$student){

            return $this->error(

                'Data siswa tidak ditemukan',

                404

            );
        }

        $jadwal = TeacherAllocation::with([

            'teacher',

            'subject',

            'schedules'

        ])

        ->where(

            'classroom_id',

            $student->classroom_id

        )

        ->get();

        return $this->success(

            $jadwal->toArray(),

            'Jadwal siswa berhasil diambil.'

        );
    }
}
