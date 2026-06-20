<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\TeacherAllocation;


class JadwalSiswaController extends Controller
{
    public function index()
    {
        // 1. Ambil User yang login
        $user = Auth::user();

        // 2. Ambil Data Siswa dari relasi User
        // Pastikan di User.php sudah ada method public function student()
        $student = $user->student;

        // --- SAFETY CHECK ---
        // Jika akun ini tidak terhubung ke data siswa ATAU siswa belum masuk kelas
        if (!$student || !$student->classroom_id) {
            // Kirim collection kosong agar View tidak error
            return view('siswa.jadwal.index', ['schedules' => collect([])]);
        }

        // 3. QUERY UTAMA JADWAL
        $schedules = Schedule::query()
            // Filter: Cari jadwal yang teacher_allocation-nya milik kelas si siswa
            ->whereHas('teacher_allocation', function ($q) use ($student) {
                $q->where('classroom_id', $student->classroom_id);
            })
            // Eager Loading: Ambil data relasi biar tidak berat (N+1 Problem)
            ->with([
                'teacher_allocation.subject',       // Ambil Mapel
                'teacher_allocation.teacher.user'   // Ambil Guru -> User (buat nama akun)
            ])
            // Sorting 1: Urutkan Hari (Senin s/d Sabtu)
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            // Sorting 2: Urutkan Jam (Pagi -> Siang)
            ->orderBy('start_time', 'asc')
            ->get();

        // 4. Return ke View
        return view('siswa.jadwal.index', compact('schedules'));
    }

    public function absensi()
    {
        $user = Auth::user();

        // 1. Ambil data profil Student berdasarkan user_id akun yang sedang login
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // 2. Tarik seluruh riwayat absensi milik siswa ini dari database beserta informasi mapelnya
        $attendances = Attendance::with(['teacher_allocation.subject', 'teacher_allocation.teacher'])
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();

        // 3. Hitung ringkasan total seluruh jenis absensi siswa
        $rekap = [
            'H' => $attendances->where('status', 'H')->count(),
            'S' => $attendances->where('status', 'S')->count(),
            'I' => $attendances->where('status', 'I')->count(),
            'A' => $attendances->where('status', 'A')->count(),
            'total' => $attendances->count()
        ];

        // 4. Hitung rekapitulasi kehadiran per mata pelajaran yang ada di kelas siswa tersebut
        $allocations = TeacherAllocation::with('subject')
            ->where('classroom_id', $student->classroom_id)
            ->get();

        $rekapMapel = $allocations->map(function ($alloc) use ($attendances) {
            $absenMapel = $attendances->where('teacher_allocation_id', $alloc->id);
            $totalPertemuan = $absenMapel->count();
            $hadir = $absenMapel->where('status', 'H')->count();

            return [
                'subject' => $alloc->subject,
                'H' => $hadir,
                'S' => $absenMapel->where('status', 'S')->count(),
                'I' => $absenMapel->where('status', 'I')->count(),
                'A' => $absenMapel->where('status', 'A')->count(),
                'total' => $totalPertemuan,
                'persentase' => $totalPertemuan > 0 ? ($hadir / $totalPertemuan) * 100 : 0
            ];
        });

        return view('siswa.absensi', compact('student', 'attendances', 'rekap', 'rekapMapel'));
    }
}
