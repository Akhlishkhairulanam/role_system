<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Import Models
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\TeacherAllocation; // Tambahan Model untuk Rapor

class PerkembanganController extends Controller
{
    // ==========================================
    // 1. HALAMAN DASHBOARD PERKEMBANGAN ANAK
    // ==========================================
    public function index()
    {
        // Ambil Data Anak berdasarkan User Login (Ortu)
        $parent = Auth::user();
        $anak = Student::where('parent_user_id', $parent->id)->with('classroom')->first();

        // Jika data anak belum di-link
        if (!$anak) {
            return redirect()->route('ortu.dashboard')->with('error', 'Data profil siswa tidak ditemukan.');
        }

        // Hitung Statistik Absensi (Status: H, S, I, A)
        $hadir = Attendance::where('student_id', $anak->id)->where('status', 'H')->count();
        $sakit = Attendance::where('student_id', $anak->id)->where('status', 'S')->count();
        $izin  = Attendance::where('student_id', $anak->id)->where('status', 'I')->count();
        $alpha = Attendance::where('student_id', $anak->id)->where('status', 'A')->count();

        $total_pertemuan = $hadir + $sakit + $izin + $alpha;

        // Hindari pembagian dengan nol
        $persentase_kehadiran = $total_pertemuan > 0
            ? round(($hadir / $total_pertemuan) * 100)
            : 0;

        // Ambil History Absensi Terakhir (5 Data Terbaru)
        $riwayat_absensi = Attendance::with(['teacher_allocation.subject', 'teacher_allocation.teacher'])
            ->where('student_id', $anak->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // Ambil Nilai Terakhir
        $nilai_terbaru = Grade::with(['teacher_allocation.subject'])
            ->where('student_id', $anak->id)
            ->latest()
            ->take(5)
            ->get();

        return view('ortu.perkembangan.index', compact(
            'anak',
            'hadir',
            'sakit',
            'izin',
            'alpha',
            'persentase_kehadiran',
            'riwayat_absensi',
            'nilai_terbaru'
        ));
    }

    // ==========================================
    // 2. HALAMAN CETAK / E-RAPOR FULL UNTUK ORTU
    // ==========================================
    public function rapor()
    {
        $parent = Auth::user();

        // Cari Data Siswa menggunakan PARENT_USER_ID (Kunci perbaikannya di sini)
        $student = Student::with(['classroom.waliKelas', 'user'])
            ->where('parent_user_id', $parent->id)
            ->first();

        // --- SAFETY CHECK 1: Ortu punya data siswa? ---
        if (!$student) {
            return redirect()->back()->with('error', 'Data profil siswa tidak ditemukan.');
        }

        // --- SAFETY CHECK 2: Siswa masuk kelas? ---
        if (!$student->classroom) {
            return redirect()->back()->with('error', 'Anak Anda belum terdaftar di kelas manapun. Silakan hubungi Tata Usaha.');
        }

        // --- LOGIC UTAMA: CEK STATUS PUBLIKASI RAPOR ---
        if ($student->classroom->is_rapor_published == 0) {
            return redirect()->back()->with('error', 'Wali Kelas belum mempublikasikan rapor. Harap tunggu info pembagian rapor dari sekolah.');
        }

        // --- AMBIL DATA RAPOR LENGKAP ---

        // 1. Ambil Mapel
        $allocations = TeacherAllocation::with(['subject', 'teacher.user'])
            ->where('classroom_id', $student->classroom_id)
            ->get();

        // 2. Ambil Nilai
        $rawGrades = Grade::where('student_id', $student->id)->get();

        // 3. Format Nilai
        $grades = [];
        foreach ($rawGrades as $g) {
            $type = strtolower($g->type);
            $grades[$g->teacher_allocation_id][$type] = $g->score;
        }

        // 4. Data Absensi (Diubah jadi data Real-time dari Database)
        $absensi = [
            'sakit' => Attendance::where('student_id', $student->id)->where('status', 'S')->count(),
            'izin'  => Attendance::where('student_id', $student->id)->where('status', 'I')->count(),
            'alpha' => Attendance::where('student_id', $student->id)->where('status', 'A')->count()
        ];

        // 5. Data Ekskul (Dummy sementara sesuai aslinya)
        $ekskul = [
            [
                'nama' => 'Pramuka (Wajib)',
                'predikat' => 'B',
                'keterangan' => 'Aktif mengikuti kegiatan.'
            ]
        ];

        // Menampilkan view Rapor A4 yang sama persis seperti admin/siswa
        return view('admin.datamaster.students.rapor', compact('student', 'allocations', 'grades', 'absensi', 'ekskul'));
    }

    // ==========================================
    // 3. HALAMAN KHUSUS "SEMUA NILAI" (TRANSKRIP)
    // ==========================================
    public function semuaNilai()
    {
        $parent = Auth::user();
        $anak = Student::where('parent_user_id', $parent->id)->first();

        if (!$anak) {
            return redirect()->route('ortu.dashboard')->with('error', 'Data profil siswa tidak ditemukan.');
        }

        // Ambil semua nilai milik anak ini beserta relasi mata pelajarannya
        $semua_nilai = Grade::with(['teacher_allocation.subject', 'teacher_allocation.teacher'])
            ->where('student_id', $anak->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Kelompokkan data nilai berdasarkan nama Mata Pelajaran agar rapi
        $nilai_per_mapel = $semua_nilai->groupBy(function ($grade) {
            return $grade->teacher_allocation->subject->nama_mapel ?? 'Mapel Umum';
        });

        return view('ortu.perkembangan.nilai', compact('anak', 'nilai_per_mapel'));
    }
}
