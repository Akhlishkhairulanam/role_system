<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class AssignmentController extends ApiController
{
    public function index()
    {
        return $this->success([], 'Daftar tugas belum diimplementasikan.');
    }

    public function store(Request $request)
    {
        return $this->success([], 'Buat tugas belum diimplementasikan.');
    }

    public function show($id)
    {
        return $this->success([], 'Detail tugas belum diimplementasikan.');
    }

    public function update(Request $request, $id)
    {
        return $this->success([], 'Update tugas belum diimplementasikan.');
    }

    public function destroy($id)
    {
        return $this->success([], 'Hapus tugas belum diimplementasikan.');
    }

    public function updateGrade(Request $request, $submission_id)
    {
        return $this->success([], 'Update nilai tugas belum diimplementasikan.');
    }
}
