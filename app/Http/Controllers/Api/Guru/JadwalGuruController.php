<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\TeacherAllocation;

class JadwalGuruController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user('api');

        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {

            return $this->error('Data guru tidak ditemukan',404);

        }

        $jadwal = TeacherAllocation::with([
            'subject',
            'classroom',
            'schedules'
        ])

        ->where('teacher_id',$teacher->id)

        ->get();

        return $this->success(

            $jadwal->toArray(),

            'Jadwal guru berhasil diambil.'

        );
    }
}
