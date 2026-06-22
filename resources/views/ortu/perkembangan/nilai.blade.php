<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rekapitulasi Semua Nilai
        </h2>
    </x-slot>

    <div class="container-fluid px-4 mt-4">
        {{-- Tombol Kembali & Info Siswa --}}
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('ortu.perkembangan.index') }}"
                class="btn btn-link text-secondary p-0 me-3 text-decoration-none hover-scale">
                <i class="fas fa-arrow-left fa-lg bg-white p-3 rounded-circle shadow-sm border"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0 text-dark">Daftar Nilai Lengkap</h3>
                <p class="text-muted small mb-0">{{ $anak->nama_lengkap }} • Kelas
                    {{ $anak->classroom->nama_kelas ?? '-' }}</p>
            </div>
        </div>

        {{-- Looping Data Nilai yang Dikelompokkan per Mapel --}}
        <div class="row g-4 mb-5">
            @forelse($nilai_per_mapel as $mapel => $grades)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-primary mb-1">
                                <i class="fas fa-book-open me-2 text-warning"></i>{{ $mapel }}
                            </h6>
                            <small class="text-muted d-block mb-2">Guru:
                                {{ $grades->first()->teacher_allocation->teacher->nama_lengkap ?? '-' }}</small>
                        </div>

                        <div class="card-body p-0 mt-2">
                            <ul class="list-group list-group-flush">
                                @foreach ($grades as $nilai)
                                    <li
                                        class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-0 border-top">
                                        <div>
                                            <span class="fw-bold text-dark d-block"
                                                style="font-size: 0.9rem;">{{ strtoupper($nilai->type) }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;"><i
                                                    class="far fa-calendar-alt me-1"></i>
                                                {{ $nilai->created_at->format('d M Y') }}</small>
                                        </div>

                                        {{-- Logic Warna Badge Nilai (Merah jika di bawah KKM 75) --}}
                                        <span
                                            class="badge bg-{{ $nilai->score >= 75 ? 'success' : 'danger' }} rounded-pill fs-6 px-3 shadow-sm">
                                            {{ $nilai->score }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="bg-white rounded-circle d-inline-flex p-4 mb-3 shadow-sm border">
                        <i class="fas fa-file-signature fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Belum Ada Nilai</h5>
                    <p class="text-muted">Guru belum memasukkan data nilai apapun untuk anak Anda.</p>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .hover-scale {
            transition: transform 0.2s;
        }

        .hover-scale:hover {
            transform: scale(1.1);
        }
    </style>
</x-app-layout>
