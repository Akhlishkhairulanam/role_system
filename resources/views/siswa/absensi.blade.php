@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="mb-4">
            <h4 class="fw-bold mb-1 text-dark">Kehadiran & Absensi Saya</h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                Pantau ringkasan presensi dan riwayat kehadiran Anda pada seluruh mata pelajaran.
            </p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div
                    class="card border-0 shadow-sm bg-success bg-opacity-10 text-success p-3 rounded-3 border-start border-success border-3">
                    <small class="fw-bold text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total
                        Hadir (H)</small>
                    <h3 class="fw-bold m-0">{{ $rekap['H'] }} <span
                            style="font-size: 0.85rem; font-weight: normal;">Hari</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div
                    class="card border-0 shadow-sm bg-info bg-opacity-10 text-info p-3 rounded-3 border-start border-info border-3">
                    <small class="fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Sakit (S)</small>
                    <h3 class="fw-bold m-0">{{ $rekap['S'] }} <span
                            style="font-size: 0.85rem; font-weight: normal;">Hari</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div
                    class="card border-0 shadow-sm bg-warning bg-opacity-10 text-warning p-3 rounded-3 border-start border-warning border-3">
                    <small class="fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Izin (I)</small>
                    <h3 class="fw-bold m-0">{{ $rekap['I'] }} <span
                            style="font-size: 0.85rem; font-weight: normal;">Hari</span></h3>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div
                    class="card border-0 shadow-sm bg-danger bg-opacity-10 text-danger p-3 rounded-3 border-start border-danger border-3">
                    <small class="fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Alpha (A)</small>
                    <h3 class="fw-bold m-0">{{ $rekap['A'] }} <span
                            style="font-size: 0.85rem; font-weight: normal;">Hari</span></h3>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold m-0 text-dark"><i class="fas fa-book me-2 text-primary"></i>Persentase Per Mata
                            Pelajaran</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3" style="width: 45%;">Nama Mata Pelajaran</th>
                                        <th class="text-center py-3">H</th>
                                        <th class="text-center py-3">S</th>
                                        <th class="text-center py-3">I</th>
                                        <th class="text-center py-3">A</th>
                                        <th class="text-center py-3" style="width: 25%;">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapMapel as $row)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark">{{ $row['subject']->nama_mapel }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">Kode:
                                                    {{ $row['subject']->code ?? '-' }}</small>
                                            </td>
                                            <td class="text-center text-success fw-bold">{{ $row['H'] }}</td>
                                            <td class="text-center text-info">{{ $row['S'] }}</td>
                                            <td class="text-center text-warning">{{ $row['I'] }}</td>
                                            <td class="text-center text-danger">{{ $row['A'] }}</td>
                                            <td class="pe-4">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span
                                                        class="fw-bold small {{ $row['persentase'] < 75 ? 'text-danger' : 'text-success' }}">
                                                        {{ number_format($row['persentase'], 0) }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada mata pelajaran
                                                terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold m-0 text-dark"><i class="fas fa-history me-2 text-primary"></i>Jurnal Kehadiran
                            Harian</h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="ps-4 py-3">Tanggal</th>
                                        <th class="py-3">Mata Pelajaran</th>
                                        <th class="text-center py-3 pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances as $att)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                    {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                                </div>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    {{ \Carbon\Carbon::parse($att->date)->locale('id')->translatedFormat('l') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.85rem;" class="fw-medium text-secondary">
                                                    {{ $att->teacher_allocation->subject->nama_mapel ?? 'Mapel' }}
                                                </div>
                                            </td>
                                            <td class="text-center pe-4">
                                                @if ($att->status == 'H')
                                                    <span class="badge bg-success rounded-pill px-2 py-1"
                                                        style="font-size: 0.7rem;">Hadir</span>
                                                @elseif($att->status == 'S')
                                                    <span class="badge bg-info rounded-pill px-2 py-1"
                                                        style="font-size: 0.7rem;">Sakit</span>
                                                @elseif($att->status == 'I')
                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1"
                                                        style="font-size: 0.7rem;">Izin</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill px-2 py-1"
                                                        style="font-size: 0.7rem;">Alpha</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fas fa-calendar-times fs-3 d-block mb-2 text-light"></i>
                                                Belum ada riwayat absensi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
