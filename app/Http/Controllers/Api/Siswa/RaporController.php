<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Student;

class RaporController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $student = Student::with([
            'grades.teacher_allocation.subject'
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

        $rapor = $student->grades->map(function ($grade) {

            return [

                'id' => $grade->id,

                'subject' => optional(
                    optional(
                        $grade->teacher_allocation
                    )->subject
                )->nama_mapel,

                'type' => $grade->type,

                'score' => $grade->score,

            ];
        });

        return $this->success(

            $rapor->toArray(),

            'Rapor siswa berhasil diambil.'

        );
    }
}
