<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\TeacherAllocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DataMasterController extends ApiController
{
    // ====================================================================
    // 1. MANAGE KELAS (CLASSROOMS)
    // ====================================================================
    public function indexClassrooms()
    {
        $classrooms = Classroom::with('waliKelas')->orderBy('level')->orderBy('nama_kelas')->get();
        return $this->success(['classrooms' => $classrooms]);
    }

    public function storeClassroom(Request $request)
    {
        try {
            $classroom = new Classroom();
            $classroom->nama_kelas = $request->nama_kelas ?? $request->name ?? 'Kelas Baru';
            $classroom->level = $request->level ?? $request->room ?? '10';
            $classroom->major = 'Umum';
            $classroom->academic_year = date('Y') . '/' . (date('Y') + 1);
            $classroom->is_rapor_published = false;
            $classroom->save();

            return $this->success(['classroom' => $classroom], 'Kelas berhasil ditambahkan.');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function updateClassroom(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->nama_kelas = $request->nama_kelas ?? $request->name ?? $classroom->nama_kelas;
        $classroom->level = $request->level ?? $request->room ?? $classroom->level;
        $classroom->save();

        return $this->success(['classroom' => $classroom], 'Kelas berhasil diupdate.');
    }

    public function destroyClassroom($id)
    {
        Classroom::findOrFail($id)->delete();
        return $this->success([], 'Kelas berhasil dihapus.');
    }

    // ====================================================================
    // 2. MANAGE SISWA (STUDENTS)
    // ====================================================================
    public function getAllStudents()
    {
        $students = Student::with(['user', 'classroom'])->get();
        return $this->success(['students' => $students], 'Data siswa berhasil diambil.');
    }

    public function storeStudent(Request $request)
    {
        // FIX: Cek apakah email sudah dipakai orang lain
        if ($request->email) {
            $emailExists = User::where('email', $request->email)->exists();
            if ($emailExists) {
                return response()->json(['success' => false, 'message' => 'Email sudah terdaftar, silakan gunakan email lain.'], 400);
            }
        }

        try {
            DB::beginTransaction();

            $baseUsername = Str::slug($request->name, '');

            $user = new User();
            $user->name = $request->name;
            $user->username = $baseUsername . rand(1000, 9999);
            $user->email = $request->email ?? ($baseUsername . rand(100, 999) . '@siswa.com');
            $user->password = bcrypt($request->password ?? 'password123');
            $user->role = 'student';
            $user->is_active = true;
            $user->save();

            $student = new Student();
            $student->user_id = $user->id;
            $student->classroom_id = $request->classroom_id;
            $student->nisn = (string) rand(100000000, 999999999);
            $student->nis = (string) rand(10000, 99999);
            $student->nama_lengkap = $request->name;
            $student->jenis_kelamin = 'Laki-laki';
            $student->tempat_lahir = 'Semarang';
            $student->tanggal_lahir = date('Y-m-d');
            $student->agama = 'Islam';
            $student->nik = (string) rand(1000000000000000, 9999999999999999);
            $student->nomor_telepon = '-';
            $student->email = $user->email;
            $student->alamat = '-';
            $student->provinsi = '-';
            $student->kabupaten = '-';
            $student->kecamatan = '-';
            $student->kelurahan = '-';
            $student->dusun = '-';
            $student->kode_pos = '00000';
            $student->tahun_masuk = date('Y');
            $student->status_aktif = 'Aktif';
            $student->nama_ayah = '-';
            $student->pekerjaan_ayah = '-';
            $student->nama_ibu = '-';
            $student->pekerjaan_ibu = '-';
            $student->nomor_telepon_ortu = '-';
            $student->save();

            DB::commit();
            return $this->success(['user' => $user], 'Siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;

        // FIX: Cek apakah email edit sudah dipakai orang lain
        if ($request->email && $user) {
            $emailExists = User::where('email', $request->email)->where('id', '!=', $user->id)->exists();
            if ($emailExists) {
                return response()->json(['success' => false, 'message' => 'Email tersebut sudah dipakai akun lain.'], 400);
            }
        }

        if ($user) {
            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            $user->save();
        }

        if ($request->has('classroom_id') && $request->classroom_id != null) {
            $student->classroom_id = $request->classroom_id;
            $student->save();
        }

        return $this->success(['student' => $student], 'Siswa berhasil diupdate.');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        if ($student->user) {
            $student->user->delete();
        }
        $student->delete();
        return $this->success([], 'Siswa berhasil dihapus.');
    }

    // ====================================================================
    // 3. MANAGE GURU (TEACHERS)
    // ====================================================================
    public function indexTeachers()
    {
        $teachers = Teacher::with('user')->get();
        return $this->success(['teachers' => $teachers], 'Data guru berhasil diambil.');
    }

    public function storeTeacher(Request $request)
    {
        // FIX: Cek apakah email guru sudah dipakai
        if ($request->email) {
            $emailExists = User::where('email', $request->email)->exists();
            if ($emailExists) {
                return response()->json(['success' => false, 'message' => 'Email guru sudah terdaftar, silakan gunakan email lain.'], 400);
            }
        }

        try {
            DB::beginTransaction();

            $baseUsername = Str::slug($request->name, '');

            $user = new User();
            $user->name = $request->name;
            $user->username = $baseUsername . rand(1000, 9999);
            $user->email = $request->email ?? ($baseUsername . rand(100, 999) . '@guru.com');
            $user->password = bcrypt($request->password ?? 'password123');
            $user->role = 'teacher';
            $user->is_active = true;
            $user->save();

            $teacher = new Teacher();
            $teacher->user_id = $user->id;
            $teacher->nip = $request->nip ?? (string) rand(10000000, 99999999);
            $teacher->nama_lengkap = $request->name;
            $teacher->jenis_kelamin = 'Laki-laki';
            $teacher->tempat_lahir = 'Semarang';
            $teacher->tanggal_lahir = date('Y-m-d');
            $teacher->agama = 'Islam';
            $teacher->nik = (string) rand(1000000000000000, 9999999999999999);
            $teacher->nomor_telepon = '-';
            $teacher->email = $user->email;
            $teacher->alamat = '-';
            $teacher->provinsi = '-';
            $teacher->kabupaten = '-';
            $teacher->kecamatan = '-';
            $teacher->kelurahan = '-';
            $teacher->dusun = '-';
            $teacher->kode_pos = '00000';
            $teacher->jabatan = 'Guru Mapel';
            $teacher->status = 'Aktif';
            $teacher->tanggal_masuk = date('Y-m-d');
            $teacher->save();

            DB::commit();
            return $this->success(['teacher' => $user], 'Guru berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function updateTeacher(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        if ($request->email && $user) {
            $emailExists = User::where('email', $request->email)->where('id', '!=', $user->id)->exists();
            if ($emailExists) {
                return response()->json(['success' => false, 'message' => 'Email tersebut sudah dipakai akun guru lain.'], 400);
            }
        }

        if ($user) {
            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            $user->save();
        }

        $teacher->nip = $request->nip ?? $teacher->nip;
        $teacher->save();

        return $this->success(['teacher' => $teacher], 'Guru berhasil diupdate.');
    }

    public function destroyTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        if ($teacher->user) {
            $teacher->user->delete();
        }
        $teacher->delete();
        return $this->success([], 'Guru berhasil dihapus.');
    }

    // Fungsi bawaan lainnya...
    public function indexSubjects()
    {
        $subjects = Subject::orderBy('nama_mapel')->get();
        return $this->success(['subjects' => $subjects]);
    }
    public function toggleRaporStatus($id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->update(['is_rapor_published' => !$classroom->is_rapor_published]);
        return $this->success(['classroom' => $classroom], 'Status rapor kelas berhasil diubah.');
    }
    public function previewJadwalKelas($class_id)
    {
        $classroom = Classroom::with('schedules')->findOrFail($class_id);
        return $this->success(['classroom' => $classroom]);
    }
    public function storeSubject(Request $request)
    {
        $data = $request->validate(['nama_mapel' => ['required', 'string', 'max:255'], 'code' => ['required', 'string', 'max:50', 'unique:subjects,code']]);
        $subject = Subject::create($data);
        return $this->success(['subject' => $subject], 'Mata pelajaran berhasil ditambahkan.');
    }
    public function updateSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $data = $request->validate(['nama_mapel' => ['required', 'string', 'max:255'], 'code' => ['required', \Illuminate\Validation\Rule::unique('subjects')->ignore($subject->id)]]);
        $subject->update($data);
        return $this->success(['subject' => $subject], 'Mata pelajaran berhasil diupdate.');
    }
    public function destroySubject($id)
    {
        Subject::findOrFail($id)->delete();
        return $this->success([], 'Mata pelajaran berhasil dihapus.');
    }
    public function indexParents()
    {
        $parents = User::where('role', 'parent')->get();
        return $this->success(['parents' => $parents]);
    }
    public function showParentsByClass($class_id)
    {
        $parents = User::where('role', 'parent')->whereHas('students', fn($query) => $query->where('classroom_id', $class_id))->get();
        return $this->success(['parents' => $parents]);
    }
    public function storeParent(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'string', 'min:8'], 'username' => ['sometimes', 'string', 'max:255', 'unique:users,username']]);
        $data['role'] = 'parent';
        $user = User::create($data);
        return $this->success(['parent' => $user], 'Orang tua berhasil ditambahkan.');
    }
    public function updateParent(Request $request, $id)
    {
        $parent = User::where('role', 'parent')->findOrFail($id);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:255'], 'email' => ['sometimes', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($parent->id)], 'username' => ['sometimes', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($parent->id)], 'password' => ['sometimes', 'string', 'min:8']]);
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $parent->update($data);
        return $this->success(['parent' => $parent], 'Orang tua berhasil diupdate.');
    }
    public function destroyParent($id)
    {
        User::where('role', 'parent')->findOrFail($id)->delete();
        return $this->success([], 'Orang tua berhasil dihapus.');
    }
}
