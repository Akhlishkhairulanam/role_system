<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class ScheduleController extends ApiController
{
    public function index()
    {
        return $this->success([], 'Daftar schedule belum diimplementasikan.');
    }

    public function store(Request $request)
    {
        return $this->success([], 'Buat schedule belum diimplementasikan.');
    }

    public function show($id)
    {
        return $this->success([], 'Detail schedule belum diimplementasikan.');
    }

    public function update(Request $request, $id)
    {
        return $this->success([], 'Update schedule belum diimplementasikan.');
    }

    public function destroy($id)
    {
        return $this->success([], 'Hapus schedule belum diimplementasikan.');
    }
}
