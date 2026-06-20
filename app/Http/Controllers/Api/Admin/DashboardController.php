<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;

use App\Models\Teacher;

use App\Models\Student;

use App\Models\Classroom;

use App\Models\Subject;

use App\Models\User;

class DashboardController extends ApiController
{
    public function index()
    {
        return $this->success([

            'jumlah_guru' =>

            Teacher::count(),

            'jumlah_siswa' =>

            Student::count(),

            'jumlah_kelas' =>

            Classroom::count(),

            'jumlah_mapel' =>

            Subject::count(),

            'jumlah_user' =>

            User::count(),

        ], 'Dashboard admin berhasil diambil.');
    }
}
