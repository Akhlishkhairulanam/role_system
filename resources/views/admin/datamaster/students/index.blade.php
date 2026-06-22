@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="row align-items-end mb-4">
            <div class="col-md-8">
                <h1 class="h3 text-gray-800 font-weight-bold mb-1">📁 Direktori Siswa</h1>
                <p class="text-muted small mb-0">
                    Total <b>{{ $classrooms->count() }}</b> Kelas &
                    <b>{{ $classrooms->sum('students_count') }}</b> Siswa Terdaftar.
                </p>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
<<<<<<< Updated upstream
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-2"
                        placeholder="Cari nama kelas, murid, atau NISN..." style="height: 45px;">
=======
                    <input type="text" id="searchClass" class="form-control border-start-0 ps-2"
                        placeholder="Cari nama kelas..." style="height: 45px;">
>>>>>>> Stashed changes
                </div>
            </div>
        </div>

<<<<<<< Updated upstream
        <div id="studentListContainer" class="d-none mb-4">
            <h5 class="fw-bold mb-3 text-dark">
                Hasil Pencarian Murid: <span id="searchKeyword" class="text-primary"></span>
            </h5>
            <div class="list-group shadow-sm border-0" id="studentListResult">
            </div>
        </div>

        <div class="row" id="classContainer">
            @forelse($classrooms as $c)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4 class-item" data-classname="{{ strtolower($c->nama_kelas) }}">
                    <a href="{{ route('guru.data.students.show_class', $c->id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 hover-lift position-relative overflow-hidden">
                            <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>

                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-folder-open fa-lg"></i>
                                    </div>
                                    <span class="badge bg-light text-secondary border">
                                        Level {{ $c->level }}
                                    </span>
                                </div>

                                <h5 class="fw-bold text-dark mb-1 class-name">{{ $c->nama_kelas }}</h5>

                                <div class="text-muted small mb-3 text-truncate">
                                    <i class="fas fa-chalkboard-teacher me-1 text-primary opacity-50"></i>
                                    {{ $c->waliKelas->nama_lengkap ?? 'Belum ada Wali Kelas' }}
                                </div>

                                <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-2">
                                    <div class="d-flex align-items-center text-secondary small">
                                        <i class="fas fa-users me-2"></i>
                                        <span class="fw-bold text-dark">{{ $c->students_count }}</span>
                                        <span class="ms-1">Siswa</span>
                                    </div>
                                    <div class="small text-primary fw-bold">
                                        Buka <i class="fas fa-arrow-right ms-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
                        <div class="mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-folder-plus fa-3x"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold">Belum Ada Data Kelas</h5>
                        <p class="text-muted">Silakan tambahkan data kelas terlebih dahulu.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div id="noResult" class="d-none text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50">
            <p class="text-muted">Kelas atau murid yang dicari tidak ditemukan.</p>
        </div>
    </div>

    <script>
        // 1. Ambil data semua murid dari controller ke dalam variabel Javascript
        const allStudents = @json($allStudents);

        // 2. Siapkan template URL (route). Ganti 'DUMMY_ID' nantinya pakai ID kelas betulan
        const baseClassRoute = "{{ route('guru.data.students.show_class', 'DUMMY_ID') }}";

        document.getElementById('searchInput').addEventListener('keyup', function () {
            let filter = this.value.toLowerCase().trim();

            let classContainer = document.getElementById('classContainer');
            let studentListContainer = document.getElementById('studentListContainer');
            let studentListResult = document.getElementById('studentListResult');
            let noResult = document.getElementById('noResult');
            let classItems = document.querySelectorAll('.class-item');

            // Reset pencarian jika input kosong (kembali ke tampilan awal)
            if (filter === '') {
                classContainer.classList.remove('d-none');
                studentListContainer.classList.add('d-none');
                noResult.classList.add('d-none');

                // Tampilkan semua card kelas
                classItems.forEach(item => item.style.display = '');
                return;
            }

            let isClassMatched = false;
            let matchedStudents = [];

            // --- A. Cek kecocokan di Nama Kelas ---
            classItems.forEach(function (item) {
                let className = item.getAttribute('data-classname');
                if (className.includes(filter)) {
                    item.style.display = '';
                    isClassMatched = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // --- B. Cek kecocokan di Data Murid ---
            if (allStudents) {
                matchedStudents = allStudents.filter(student => {
                    let nama = (student.nama_lengkap || '').toLowerCase();
                    let nisn = (student.nisn || '').toLowerCase();
                    return nama.includes(filter) || nisn.includes(filter);
                });
            }

            // --- C. Atur Logika Tampilan Berdasarkan Hasil Pencarian ---

            if (matchedStudents.length > 0) {
                // JIKA MURID KETEMU: Sembunyikan card kelas, Tampilkan List Murid
                classContainer.classList.add('d-none');
                studentListContainer.classList.remove('d-none');
                noResult.classList.add('d-none');

                document.getElementById('searchKeyword').innerText = `"${this.value}"`;
                studentListResult.innerHTML = ''; // Kosongkan list sebelumnya

                // Render list murid
                matchedStudents.forEach(student => {
                    let className = student.classroom ? student.classroom.nama_kelas : 'Belum Ada Kelas';
                    let classId = student.classroom_id;

                    // Ganti DUMMY_ID dengan ID kelas yang asli
                    let urlRedirect = baseClassRoute.replace('DUMMY_ID', classId);

                    let li = `
                                <a href="${urlRedirect}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 px-4 border-0 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">${student.nama_lengkap}</h6>
                                            <small class="text-muted"><i class="fas fa-id-card me-1"></i> NISN: ${student.nisn || '-'}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-primary border border-primary px-3 py-2 rounded-pill">
                                        <i class="fas fa-door-open me-1"></i> ${className}
                                    </span>
                                </a>`;
                    studentListResult.innerHTML += li;
                });

            } else if (isClassMatched) {
                // JIKA HANYA KELAS YANG KETEMU: Tampilkan card kelas
                classContainer.classList.remove('d-none');
                studentListContainer.classList.add('d-none');
                noResult.classList.add('d-none');
            } else {
                // JIKA TIDAK KETEMU KEDUANYA
                classContainer.classList.add('d-none');
                studentListContainer.classList.add('d-none');
                noResult.classList.remove('d-none');
=======
        <div class="row" id="classContainer">
            @forelse($classrooms as $c)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4 class-item">
                    <a href="{{ route('guru.data.students.show_class', $c->id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 hover-lift position-relative overflow-hidden">
                            <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>

                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-folder-open fa-lg"></i>
                                    </div>
                                    <span class="badge bg-light text-secondary border">
                                        Level {{ $c->level }}
                                    </span>
                                </div>

                                <h5 class="fw-bold text-dark mb-1 class-name">{{ $c->nama_kelas }}</h5>

                                <div class="text-muted small mb-3 text-truncate">
                                    <i class="fas fa-chalkboard-teacher me-1 text-primary opacity-50"></i>
                                    {{ $c->waliKelas->nama_lengkap ?? 'Belum ada Wali Kelas' }}
                                </div>

                                <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-2">
                                    <div class="d-flex align-items-center text-secondary small">
                                        <i class="fas fa-users me-2"></i>
                                        <span class="fw-bold text-dark">{{ $c->students_count }}</span>
                                        <span class="ms-1">Siswa</span>
                                    </div>
                                    <div class="small text-primary fw-bold">
                                        Buka <i class="fas fa-arrow-right ms-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
                        <div class="mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-folder-plus fa-3x"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold">Belum Ada Data Kelas</h5>
                        <p class="text-muted">Silakan tambahkan data kelas terlebih dahulu di menu Data Master.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div id="noResult" class="d-none text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50">
            <p class="text-muted">Kelas yang Anda cari tidak ditemukan.</p>
        </div>
    </div>

    <script>
        document.getElementById('searchClass').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.class-item');
            let visibleCount = 0;

            items.forEach(function(item) {
                let name = item.querySelector('.class-name').innerText.toLowerCase();
                if (name.includes(filter)) {
                    item.style.display = ''; // Show
                    visibleCount++;
                } else {
                    item.style.display = 'none'; // Hide
                }
            });

            // Tampilkan pesan jika tidak ada hasil
            let noResult = document.getElementById('noResult');
            if (visibleCount === 0 && filter !== '') {
                noResult.classList.remove('d-none');
            } else {
                noResult.classList.add('d-none');
>>>>>>> Stashed changes
            }
        });
    </script>

    <style>
<<<<<<< Updated upstream
=======
        /* Efek Hover Profesional */
>>>>>>> Stashed changes
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15) !important;
<<<<<<< Updated upstream
=======
            /* Bayangan halus biru */
>>>>>>> Stashed changes
        }

        .hover-lift:active {
            transform: translateY(-2px);
        }
<<<<<<< Updated upstream

        .list-group-item-action:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
            transition: all 0.2s ease;
        }
    </style>
@endsection
=======
    </style>
@endsection
>>>>>>> Stashed changes
