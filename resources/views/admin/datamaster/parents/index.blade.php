<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Wali Murid
        </h2>
    </x-slot>

    <div class="container-fluid px-4 mt-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 text-gray-800 font-weight-bold">👨‍👩‍👧‍👦 Data Wali Murid</h1>
                <p class="text-muted small mb-0">Pilih kelas atau cari data orang tua siswa.</p>
            </div>

            <div class="d-flex gap-3 align-items-center">
                {{-- Search Bar --}}
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-2"
                        placeholder="Cari nama ortu, email, atau anak..." style="width: 280px; height: 40px;">
                </div>

                {{-- Tombol Tambah Akun (Global) --}}
                <button class="btn btn-primary rounded-pill shadow-sm px-4 text-nowrap" style="height: 40px;"
                    onclick="openAddModal()">
                    <i class="fas fa-plus me-2"></i> Buat Akun Wali
                </button>
            </div>
        </div>

        {{-- ALERT: LOGIC DETEKSI AKUN MENGGANTUNG --}}
        @if(isset($unlinked_parents) && $unlinked_parents > 0)
            <div
                class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex justify-content-between align-items-center bg-warning bg-opacity-10 text-dark">
                <div class="d-flex align-items-center">
                    <div class="me-3 text-warning">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Perhatian</h6>
                        <small class="d-block text-muted" style="line-height: 1.5;">
                            Ada <b class="text-danger">{{ $unlinked_parents }}</b> akun orang tua yang baru dibuat tapi
                            belum
                            terhubung ke siswa manapun.<br>
                            Silakan tautkan akun tersebut lewat menu <b>Data Siswa</b>.
                        </small>
                    </div>
                </div>
                {{-- Tombol Tambahan untuk Melihat Ortu yang Belum Ditautkan --}}
                {{-- Tombol Tambahan untuk Melihat Ortu yang Belum Ditautkan --}}
                <button id="toggleUnlinkedBtn"
                    class="btn btn-warning rounded-pill fw-bold shadow-sm ms-3 text-nowrap px-4 py-2"
                    onclick="toggleUnlinkedParents()">
                    <i class="fas fa-eye me-1"></i> Lihat Daftar
                </button>
            </div>
        @endif

        {{-- CONTAINER HASIL PENCARIAN (TABEL) --}}
        <div id="parentTableContainer" class="d-none mb-4">
            <h5 class="fw-bold mb-3 text-dark">
                Hasil Pencarian: <span id="searchKeyword" class="text-primary"></span>
            </h5>

            <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="px-4 py-3" style="width: 50px;">No</th>
                                    <th class="py-3">Akun Wali Murid</th>
                                    <th class="py-3">Anak yang Tertaut</th>
                                    <th class="text-end px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="parentTableResult">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- PESAN TIDAK DITEMUKAN --}}
        {{-- PESAN MINIMAL KARAKTER (TAMBAHAN BARU) --}}
        <div id="minCharState" class="d-none text-center py-5">
            <div class="mb-3">
                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px;">
                    <i class="fas fa-keyboard fa-2x opacity-75"></i>
                </div>
            </div>
            <h5 class="fw-bold text-dark">Menunggu...</h5>
            <p class="text-muted">Ketik minimal <b class="text-primary">3 huruf</b> untuk mulai mencari data.</p>
        </div>
        <div id="noResult" class="d-none text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="mb-3 opacity-50">
            <p class="text-muted">Orang tua, kelas, atau anak yang dicari tidak ditemukan.</p>
        </div>

        {{-- Grid Kelas (Default) --}}
        <div class="row g-4" id="classContainer">
            @foreach($classrooms as $kelas)
                <div class="col-md-6 col-xl-4 class-item" data-classname="{{ strtolower($kelas->nama_kelas) }}">
                    <a href="{{ route('guru.data.parents.show_class', $kelas->id) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 hover-up rounded-4 overflow-hidden position-relative">
                            <div class="card-body p-4 d-flex flex-column position-relative z-1">
                                {{-- Header Kartu --}}
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                        <i class="fas fa-user-friends fa-2x"></i>
                                    </div>
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                        {{ $kelas->students_count }} Siswa
                                    </span>
                                </div>

                                {{-- Footer Kartu --}}
                                <div class="mt-auto">
                                    <h5 class="fw-bold text-dark mb-1">{{ $kelas->nama_kelas }}</h5>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>
                                        <span class="text-truncate" style="max-width: 200px;">
                                            Wali Kelas: {{ $kelas->waliKelas->nama_lengkap ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Hiasan Background --}}
                            <div class="position-absolute bottom-0 end-0 opacity-10 p-3">
                                <i class="fas fa-users fa-4x text-secondary"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MODAL TAMBAH AKUN (GLOBAL) --}}
    <div class="modal fade" id="parentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <form action="{{ route('guru.data.parents.store') }}" method="POST"
                class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Tambah Akun Orang Tua</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info-emphasis small mb-4">
                        <div class="d-flex">
                            <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                            <div>
                                <b>PENTING:</b><br>
                                Form ini hanya untuk membuat <b>Akun Login</b>.<br>
                                Agar akun ini aktif, Anda harus menautkannya lewat menu <b>Data Siswa</b> setelah akun
                                dibuat.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Budi Santoso"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username Login</label>
                        <input type="text" name="username" class="form-control rounded-3" placeholder="Contoh: budi123"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Aktif</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="email@contoh.com"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control rounded-3"
                            placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // --- 1. SCRIPT MODAL ---
            function openAddModal() {
                new bootstrap.Modal(document.getElementById('parentModal')).show();
            }

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

                                                // --- 2. SCRIPT LIVE SEARCH ---
                                                // --- 2. SCRIPT LIVE SEARCH (DENGAN DEBOUNCE & MIN CHAR) ---
                                                const allParents = @json($allParents);
            const baseClassRoute = "{{ route('guru.data.parents.show_class', 'DUMMY_ID') }}";

            let searchTimeout = null; // Variabel untuk menyimpan timer debounce

            document.getElementById('searchInput').addEventListener('keyup', function () {
                let filter = this.value.toLowerCase().trim();

                let classContainer = document.getElementById('classContainer');
                let parentTableContainer = document.getElementById('parentTableContainer');
                let parentTableResult = document.getElementById('parentTableResult');
                let noResult = document.getElementById('noResult');
                let minCharState = document.getElementById('minCharState'); // <--- Deklarasi variabel baru
                let classItems = document.querySelectorAll('.class-item');

                clearTimeout(searchTimeout);

                // JIKA INPUT KOSONG
                if (filter === '') {
                    classContainer.classList.remove('d-none');
                    parentTableContainer.classList.add('d-none');
                    noResult.classList.add('d-none');
                    minCharState.classList.add('d-none'); // <--- Sembunyikan pesan 3 huruf
                    classItems.forEach(item => item.style.display = '');
                    return;
                }

                // JIKA HURUF KURANG DARI 3
                if (filter.length > 0 && filter.length < 3) {
                    classContainer.classList.add('d-none');
                    parentTableContainer.classList.add('d-none');
                    noResult.classList.add('d-none');
                    minCharState.classList.remove('d-none'); // <--- Tampilkan pesan 3 huruf
                    return;
                }

                // JIKA HURUF SUDAH 3 ATAU LEBIH, SEMBUNYIKAN PESAN DAN MULAI PENCARIAN
                minCharState.classList.add('d-none');

                // DEBOUNCING: Beri jeda 300 milidetik setelah user berhenti mengetik
                searchTimeout = setTimeout(function () {

                    let isClassMatched = false;
                    let matchedParents = [];

                    // A. Cek kecocokan dengan Nama Kelas
                    classItems.forEach(function (item) {
                        let className = item.getAttribute('data-classname');
                        if (className.includes(filter)) {
                            item.style.display = '';
                            isClassMatched = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // B. Cek kecocokan di Data Orang Tua
                    if (allParents) {
                        matchedParents = allParents.filter(parent => {
                            let parentName = (parent.name || '').toLowerCase();
                            let parentEmail = (parent.email || '').toLowerCase();
                            let parentUsername = (parent.username || '').toLowerCase();

                            let childrenNames = '';
                            if (parent.students && parent.students.length > 0) {
                                childrenNames = parent.students.map(s => s.nama_lengkap.toLowerCase()).join(' ');
                            }

                            return parentName.includes(filter) ||
                                parentEmail.includes(filter) ||
                                parentUsername.includes(filter) ||
                                childrenNames.includes(filter);
                        });
                    }

                    // C. Render Hasil
                    if (matchedParents.length > 0) {
                        classContainer.classList.add('d-none');
                        parentTableContainer.classList.remove('d-none');
                        noResult.classList.add('d-none');

                        document.getElementById('searchKeyword').innerText = `"${filter}"`;

                        // Gunakan array mapping lalu di-join untuk merender tabel jauh lebih cepat 
                        // dibandingkan innerHTML += berulang kali di dalam loop
                        let rowsHtml = matchedParents.map((parent, index) => {
                            let childrenHtml = '';
                            let urlRedirect = '#';

                            if (parent.students && parent.students.length > 0) {
                                childrenHtml = parent.students.map(student => {
                                    let className = student.classroom ? student.classroom.nama_kelas : '-';
                                    return `
                                                                                            <div class="mb-1">
                                                                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill">
                                                                                                    <i class="fas fa-child me-1"></i> ${student.nama_lengkap} (Kls: ${className})
                                                                                                </span>
                                                                                            </div>
                                                                                        `;
                                }).join('');

                                let firstClassId = parent.students[0].classroom_id;
                                if (firstClassId) urlRedirect = baseClassRoute.replace('DUMMY_ID', firstClassId);
                            } else {
                                childrenHtml = `<span class="small text-muted fst-italic">Belum ada anak yang ditautkan</span>`;
                            }

                            let btnDisabled = urlRedirect === '#' ? 'disabled' : '';

                            return `
                                                                                <tr>
                                                                                    <td class="px-4 text-center text-muted">${index + 1}</td>
                                                                                    <td>
                                                                                        <div class="fw-bold text-dark">${parent.name}</div>
                                                                                        <div class="small text-muted">
                                                                                            <i class="fas fa-envelope me-1"></i> ${parent.email || '-'} | Usr: ${parent.username}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>${childrenHtml}</td>
                                                                                    <td class="text-end px-4">
                                                                                        <a href="${urlRedirect}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" ${btnDisabled}>
                                                                                            Buka di Kelas <i class="fas fa-arrow-right ms-1"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>`;
                        }).join('');

                        // Render semua baris ke DOM sekaligus dalam satu waktu
                        parentTableResult.innerHTML = rowsHtml;

                    } else if (isClassMatched) {
                        classContainer.classList.remove('d-none');
                        parentTableContainer.classList.add('d-none');
                        noResult.classList.add('d-none');
                    } else {
                        classContainer.classList.add('d-none');
                        parentTableContainer.classList.add('d-none');
                        noResult.classList.remove('d-none');
                    }

                }, 300); // Angka 300 adalah jeda 300ms. Bisa dinaikkan ke 500 jika masih lag.
            });

            // --- 3. SCRIPT TOGGLE ORTU BELUM DITAUTKAN ---
            function toggleUnlinkedParents() {
                let classContainer = document.getElementById('classContainer');
                let parentTableContainer = document.getElementById('parentTableContainer');
                let parentTableResult = document.getElementById('parentTableResult');
                let noResult = document.getElementById('noResult');
                let minCharState = document.getElementById('minCharState');
                let toggleBtn = document.getElementById('toggleUnlinkedBtn');
                let searchInput = document.getElementById('searchInput');

                // Cek kondisi: Apakah tabel unlinked sedang tampil?
                // (Ditandai dengan disembunyikannya classContainer)
                let isShowingUnlinked = classContainer.classList.contains('d-none') && searchInput.value === '';

                if (isShowingUnlinked) {
                    // === SKENARIO A: KEMBALI KE TAMPILAN AWAL (KELAS) ===
                    classContainer.classList.remove('d-none');
                    parentTableContainer.classList.add('d-none');
                    noResult.classList.add('d-none');
                    if (minCharState) minCharState.classList.add('d-none');

                    // Kembalikan wujud tombol ke "Lihat Daftar" (Warna Kuning)
                    toggleBtn.innerHTML = '<i class="fas fa-eye me-1"></i> Lihat Daftar';
                    toggleBtn.classList.remove('btn-secondary');
                    toggleBtn.classList.add('btn-warning');

                    // Munculkan kembali semua card kelas yang mungkin sempat terfilter
                    let classItems = document.querySelectorAll('.class-item');
                    classItems.forEach(item => item.style.display = '');

                } else {
                    // === SKENARIO B: TAMPILKAN TABEL BELUM DITAUTKAN ===
                    classContainer.classList.add('d-none');
                    noResult.classList.add('d-none');
                    if (minCharState) minCharState.classList.add('d-none');
                    parentTableContainer.classList.remove('d-none');

                    // Ubah wujud tombol menjadi "Kembali" (Warna Abu-abu/Secondary)
                    toggleBtn.innerHTML = '<i class="fas fa-arrow-left me-1"></i> Kembali';
                    toggleBtn.classList.remove('btn-warning');
                    toggleBtn.classList.add('btn-secondary');

                    // Kosongkan kotak pencarian (reset)
                    searchInput.value = '';

                    document.getElementById('searchKeyword').innerText = "Akun Belum Ditautkan (Baru)";

                    let unlinked = allParents.filter(parent => !parent.students || parent.students.length === 0);

                    if (unlinked.length > 0) {
                        let baseDeleteRoute = "{{ route('guru.data.parents.destroy', 'DUMMY_ID') }}";
                        let csrfToken = "{{ csrf_token() }}";

                        let rowsHtml = unlinked.map((parent, index) => {
                            let deleteUrl = baseDeleteRoute.replace('DUMMY_ID', parent.id);

                            return `
                                        <tr>
                                            <td class="px-4 text-center text-muted">${index + 1}</td>
                                            <td>
                                                <div class="fw-bold text-dark">${parent.name}</div>
                                                <div class="small text-muted">
                                                    <i class="fas fa-envelope me-1"></i> ${parent.email || '-'} | Usr: ${parent.username}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="small text-danger fw-bold bg-danger bg-opacity-10 px-2 py-1 rounded">
                                                    <i class="fas fa-exclamation-circle"></i> Belum ada anak
                                                </span>
                                            </td>
                                            <td class="text-end px-4">
                                                <div class="d-flex justify-content-end align-items-center gap-1">
                                                    <button class="btn btn-sm btn-light text-danger border-0 rounded-circle"
                                                            onclick="deleteParent(${parent.id})" title="Hapus Akun">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    <form id="delete-parent-${parent.id}" action="${deleteUrl}" method="POST" class="d-none">
                                                        <input type="hidden" name="_token" value="${csrfToken}">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                    </form>
                                                    <a href="{{ route('guru.data.students.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm ms-2">
                                                        <i class="fas fa-link me-1"></i> Tautkan
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>`;
                        }).join('');

                        parentTableResult.innerHTML = rowsHtml;
                    }
                }
            }

            // --- 4. SCRIPT HAPUS AKUN (SWEETALERT) ---
            function deleteParent(id) {
                Swal.fire({
                    title: 'Hapus Akun?',
                    text: "Akun orang tua ini belum ditautkan dan akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form hapus yang ada di baris tabel
                        document.getElementById('delete-parent-' + id).submit();
                    }
                });
            }
        </script>
    @endpush

    <style>
        .hover-up {
            transition: transform 0.2s;
        }

        .hover-up:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }
    </style>
</x-app-layout>