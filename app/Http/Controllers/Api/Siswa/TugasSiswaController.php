<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Api\ApiController;

use Illuminate\Http\Request;

use App\Models\Student;

use App\Models\Assignment;

use App\Models\AssignmentSubmission;

class TugasSiswaController extends ApiController
{
    public function index(Request $request)
    {
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

        $tugas = Assignment::with([

            'teacherAllocation.subject',

            'teacherAllocation.teacher',

        ])

        ->where(

            'classroom_id',

            $student->classroom_id

        )

        ->latest()

        ->get();

        return $this->success(

            $tugas->toArray(),

            'Daftar tugas berhasil diambil.'

        );
    }

    public function show($id)
    {
        $tugas = Assignment::find($id);

        if (!$tugas) {

            return $this->error(

                'Tugas tidak ditemukan',

                404

            );
        }

        return $this->success(

            $tugas->toArray(),

            'Detail tugas berhasil diambil.'

        );
    }

    public function store(
        Request $request,
        $id
    )
    {
        $request->validate([

            'file' => 'required',

        ]);

        return $this->success(

            [],

            'Submit tugas berhasil.'

        );
    }

    public function destroy($id)
    {
        return $this->success(

            [],

            'Pengumpulan tugas dihapus.'

        );
    }
}
