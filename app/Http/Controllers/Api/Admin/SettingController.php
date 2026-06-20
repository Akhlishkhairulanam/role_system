<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class SettingController extends ApiController
{
    public function index()
    {
        return $this->success([], 'Pengaturan belum diimplementasikan.');
    }

    public function update(Request $request)
    {
        return $this->success([], 'Update setting belum diimplementasikan.');
    }
}
