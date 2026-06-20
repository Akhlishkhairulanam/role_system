<?php

namespace App\Http\Controllers\Api\Ortu;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class SppOrtuController extends ApiController
{
    public function index()
    {
        return $this->success([], 'Daftar tagihan SPP orang tua belum diimplementasikan.');
    }

    public function uploadBukti(Request $request, $id)
    {
        return $this->success([], 'Upload bukti pembayaran belum diimplementasikan.');
    }

    public function storeDispensation(Request $request, $id)
    {
        return $this->success([], 'Store dispensasi SPP belum diimplementasikan.');
    }
}
