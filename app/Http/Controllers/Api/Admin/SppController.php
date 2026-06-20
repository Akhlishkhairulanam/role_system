<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class SppController extends ApiController
{
    public function index()
    {
        return $this->success([], 'Daftar SPP belum diimplementasikan.');
    }

    public function verification()
    {
        return $this->success([], 'Verifikasi SPP belum diimplementasikan.');
    }

    public function archive()
    {
        return $this->success([], 'Arsip SPP belum diimplementasikan.');
    }

    public function store(Request $request)
    {
        return $this->success([], 'Generate tagihan massal belum diimplementasikan.');
    }

    public function storeIndividual(Request $request)
    {
        return $this->success([], 'Generate tagihan individual belum diimplementasikan.');
    }

    public function getStudentsByClass($classroom_id)
    {
        return $this->success([], 'Get students belum diimplementasikan.');
    }

    public function publishAll(Request $request)
    {
        return $this->success([], 'Publish semua tagihan belum diimplementasikan.');
    }

    public function deleteAll(Request $request)
    {
        return $this->success([], 'Delete semua tagihan belum diimplementasikan.');
    }

    public function saveDispensation($id)
    {
        return $this->success([], 'Simpan dispensasi belum diimplementasikan.');
    }

    public function approveDispensation($id)
    {
        return $this->success([], 'Approve dispensasi belum diimplementasikan.');
    }

    public function rejectDispensation($id)
    {
        return $this->success([], 'Reject dispensasi belum diimplementasikan.');
    }

    public function verify($id)
    {
        return $this->success([], 'Verifikasi online belum diimplementasikan.');
    }

    public function reject($id)
    {
        return $this->success([], 'Tolak online belum diimplementasikan.');
    }

    public function payManual($id)
    {
        return $this->success([], 'Pembayaran manual belum diimplementasikan.');
    }

    public function cancelPayment($id)
    {
        return $this->success([], 'Batal bayar belum diimplementasikan.');
    }

    public function togglePublish($id)
    {
        return $this->success([], 'Toggle publish belum diimplementasikan.');
    }

    public function update(Request $request, $id)
    {
        return $this->success([], 'Update tagihan belum diimplementasikan.');
    }

    public function destroy($id)
    {
        return $this->success([], 'Hapus tagihan belum diimplementasikan.');
    }
}
