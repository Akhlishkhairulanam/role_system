<?php

namespace App\Http\Controllers\Api\Ortu;

use App\Http\Controllers\Api\ApiController;

use Illuminate\Http\Request;

use App\Models\Student;

class PerkembanganController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $students = Student::with([

            'classroom',

            'attendances',

            'grades'

        ])

        ->where(

            'parent_user_id',

            $user->id

        )

        ->get();

        return $this->success(

            $students->toArray(),

            'Data perkembangan berhasil diambil.'

        );
    }
}
