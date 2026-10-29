@extends('layouts.app')

@section('title', 'Riwayat Pengajuan Peminjaman - DEL-LEATS')

@section('content')
    <div class="page-title-section">
        <div>
            <h2>Riwayat Pengajuan Peminjaman Inventaris</h2>
            <p>Daftar seluruh transaksi permohonan peminjaman perangkat laboratorium oleh mahasiswa Institut Teknologi Del.</p>
        </div>
        <div>
            <a href="{{ route('loans.create') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ajukan Pinjam Baru
            </a>
        </div>
    </div>

    <section class="table-card" aria-label="Tabel Riwayat Peminjaman">
        @if ($loans->count() > 0)
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No / ID</th>
                            <th>Perangkat Dipinjam</th>
                            <th>Peminjam (NIM)</th>
                            <th>Surel</th>
                            <th>Periode Pinjam</th>
                            <th>Estimasi Biaya</th>
                            <th>Status Pengajuan</th>
                            <th>Tanggal Diajukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>#{{ $loan->id }}</td>
                                <td>
                                    <div style="font-weight: 700; color: var(--dark);">
                                        <a href="{{ route('equipments.show', $loan->equipment->id) }}">
                                            {{ $loan->equipment->name }}
                                        </a>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); font-family: monospace;">
                                        {{ $loan->equipment->asset_code }} &bull; {{ $loan->equipment->category->name }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--dark);">{{ $loan->borrower_name }}</div>
                                    <div style="font-size: 0.825rem; color: var(--text-muted); font-family: monospace;">{{ $loan->borrower_nim }}</div>
                                </td>
                                <td style="font-size: 0.85rem;">{{ $loan->borrower_email }}</td>
                                <td style="font-size: 0.85rem;">
                                    <div><strong>Pinjam:</strong> {{ date('d M Y', strtotime($loan->loan_date)) }}</div>
                                    <div><strong>Kembali:</strong> {{ date('d M Y', strtotime($loan->expected_return_date)) }}</div>
                                </td>
                                <td>
                                    <strong style="color: var(--primary);">
                                        Rp {{ number_format($loan->total_estimated_fee, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    @if ($loan->status === 'disetujui')
                                        <span class="status-badge badge-tersedia">Disetujui</span>
                                    @elseif ($loan->status === 'diajukan')
                                        <span class="status-badge badge-dipinjam">Diajukan</span>
                                    @elseif ($loan->status === 'ditolak')
                                        <span class="status-badge badge-dalam_perbaikan">Ditolak</span>
                                    @else
                                        <span class="status-badge" style="background:#E2E8F0; color:#334155;">Selesai</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.825rem; color: var(--text-muted);">
                                    {{ $loan->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>Belum Ada Riwayat Peminjaman</h3>
                <p>Saat ini belum ada pengajuan peminjaman perangkat laboratorium yang tercatat di sistem.</p>
                <a href="{{ route('loans.create') }}" class="btn btn-primary">Ajukan Peminjaman Pertama</a>
            </div>
        @endif
    </section>

    <!-- Pagination Riwayat Peminjaman -->
    @if ($loans->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
                Menampilkan data ke-<strong>{{ $loans->firstItem() }}</strong> sampai ke-<strong>{{ $loans->lastItem() }}</strong> dari <strong>{{ $loans->total() }}</strong> total pengajuan.
            </div>
            <div>
                {{ $loans->links() }}
            </div>
        </div>
    @endif
@endsection
