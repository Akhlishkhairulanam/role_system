<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Api\ApiController;

use App\Models\Grade;

use App\Models\Student;

use Illuminate\Http\Request;

class NilaiController extends ApiController
{
    public function index()
    {
        return $this->success(

            Grade::with([

                'student',

                'teacher_allocation',

            ])->get()->toArray(),

            'Daftar nilai berhasil diambil.'

        );
    }

    public function create($allocation_id)
    {
        $students = Student::whereHas(

            'classroom.teacher_allocations',

            function($query) use ($allocation_id){

                $query->where('id',$allocation_id);

            }

        )->get();

        return $this->success(

            $students->toArray(),

            'Data siswa berhasil diambil.'

        );
    }

    public function store(Request $request,$allocation_id)
    {
        $data = $request->validate([

            'student_id'=>'required',

            'type'=>'required',

            'score'=>'required',

        ]);

        $data['teacher_allocation_id'] = $allocation_id;

        $grade = Grade::create($data);

        return $this->success(

            $grade->toArray(),

            'Nilai berhasil disimpan.'

        );
    }
}
