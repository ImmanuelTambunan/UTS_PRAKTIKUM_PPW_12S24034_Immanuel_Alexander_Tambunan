@extends('layouts.app')

@section('title', 'Detail Perangkat ' . $equipment->name . ' - DEL-LEATS')

@section('content')
    <div class="page-title-section">
        <div>
            <h2>Detail Spesifikasi Peralatan</h2>
            <p>Informasi teknis lengkap dan catatan ketersediaan unit laboratorium.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                &larr; Kembali ke Katalog
            </a>
            @if ($equipment->status === 'tersedia')
                <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-primary">
                    Ajukan Peminjaman
                </a>
            @endif
        </div>
    </div>

    <!-- Detail Grid Semantik HTML5 -->
    <article class="detail-grid">
        <div class="detail-gallery">
            @if ($equipment->image_path)
                <img src="{{ asset('storage/' . $equipment->image_path) }}" alt="{{ $equipment->name }}">
            @else
                <div style="display:flex;align-items:center;justify-content:center;height:300px;color:#94a3b8;">
                    Foto tidak tersedia
                </div>
            @endif
        </div>

        <div class="detail-info">
            <div class="detail-header">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="asset-code">{{ $equipment->asset_code }}</span>
                    @if ($equipment->status === 'tersedia')
                        <span class="status-badge badge-tersedia">
                            <span class="badge-dot"></span> Tersedia
                        </span>
                    @elseif ($equipment->status === 'dipinjam')
                        <span class="status-badge badge-dipinjam">
                            <span class="badge-dot"></span> Sedang Dipinjam
                        </span>
                    @else
                        <span class="status-badge badge-dalam_perbaikan">
                            <span class="badge-dot"></span> Dalam Perbaikan
                        </span>
                    @endif
                </div>

                <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--dark); line-height: 1.3;">
                    {{ $equipment->name }}
                </h2>
                <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.35rem;">
                    Kategori: <strong>{{ $equipment->category->name }} ({{ $equipment->category->code }})</strong>
                </div>
            </div>

            <table class="detail-specs-table">
                <tbody>
                    <tr>
                        <th>Tarif Harian</th>
                        <td style="font-size: 1.15rem; font-weight: 800; color: var(--primary);">
                            Rp {{ number_format($equipment->daily_rental_fee, 0, ',', '.') }} / hari
                        </td>
                    </tr>
                    <tr>
                        <th>Kondisi Fisik</th>
                        <td>
                            @if ($equipment->condition === 'baik')
                                <span style="color: #166534; font-weight: 600;">Baik (Siap Digunakan)</span>
                            @elseif ($equipment->condition === 'rusak_ringan')
                                <span style="color: #854d0e; font-weight: 600;">Rusak Ringan</span>
                            @else
                                <span style="color: #991b1b; font-weight: 600;">Rusak Berat</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Terdaftar Sejak</th>
                        <td>{{ $equipment->created_at->format('d F Y, H:i') }} WIB</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-bottom: 1.75rem;">
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--dark-muted); text-transform: uppercase; margin-bottom: 0.5rem;">
                    Spesifikasi Teknis
                </h4>
                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; font-size: 0.925rem; white-space: pre-line;">
                    {{ $equipment->specification }}
                </div>
            </div>

            <div style="margin-top: auto;">
                @if ($equipment->status === 'tersedia')
                    <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-primary btn-block" style="padding: 0.85rem;">
                        Ajukan Peminjaman Unit Ini
                    </a>
                @else
                    <div style="background: #FFFBEB; border: 1px solid #FDE68A; padding: 0.85rem; border-radius: var(--radius-sm); text-align: center; color: #92400E; font-size: 0.9rem;">
                        Unit ini saat ini berstatus <strong>{{ str_replace('_', ' ', $equipment->status) }}</strong> sehingga belum dapat diajukan peminjaman baru.
                    </div>
                @endif
            </div>
        </div>
    </article>

    <!-- Riwayat Peminjaman Unit Ini -->
    @if ($equipment->loanRequests->count() > 0)
        <section style="margin-top: 3rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--dark); margin-bottom: 1rem;">
                Riwayat Peminjaman Unit Ini ({{ $equipment->loanRequests->count() }})
            </h3>
            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Peminjam</th>
                            <th>NIM</th>
                            <th>Tgl Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Tujuan</th>
                            <th>Estimasi Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipment->loanRequests as $req)
                            <tr>
                                <td>#{{ $req->id }}</td>
                                <td><strong>{{ $req->borrower_name }}</strong></td>
                                <td><code>{{ $req->borrower_nim }}</code></td>
                                <td>{{ $req->loan_date }}</td>
                                <td>{{ $req->expected_return_date }}</td>
                                <td>{{ Str::limit($req->purpose, 40) }}</td>
                                <td>Rp {{ number_format($req->total_estimated_fee, 0, ',', '.') }}</td>
                                <td>
                                    <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
                                        {{ $req->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
@endsection
