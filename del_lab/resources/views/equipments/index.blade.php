@extends('layouts.app')

@section('title', 'Katalog Aset & Inventaris - DEL-LEATS')

@section('content')
    <!-- Judul Halaman & Tombol Aksi -->
    <div class="page-title-section">
        <div>
            <h2>Katalog Peralatan Laboratorium</h2>
            <p>Jelajahi ketersediaan perangkat keras dan peralatan riset laboratorium teknologi terpadu IT Del.</p>
        </div>
        <div>
            <a href="{{ route('equipments.create') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Aset Baru
            </a>
        </div>
    </div>

    <!-- Filter dan Pencarian -->
    <section class="search-filter-card" aria-label="Filter Pencarian Aset">
        <form action="{{ route('equipments.index') }}" method="GET" class="search-filter-form">
            <div class="form-group">
                <label for="search" class="form-label">Kata Kunci Pencarian</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       class="form-control" 
                       placeholder="Cari nama atau kode aset (misal: drone, EQ-SRV)..." 
                       value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label for="category_id" class="form-label">Kategori Perangkat</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="semua">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status Ketersediaan</label>
                <select name="status" id="status" class="form-select">
                    <option value="semua">Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dalam_perbaikan" {{ request('status') == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary" title="Terapkan Filter">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Cari
                </button>
                @if (request()->hasAny(['search', 'category_id', 'status']))
                    <a href="{{ route('equipments.index') }}" class="btn btn-secondary" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Keterangan Ringkasan Hasil Filter / Pencarian -->
        @if (request('search') || (request('category_id') && request('category_id') !== 'semua') || (request('status') && request('status') !== 'semua'))
            <div class="search-summary-bar">
                <div>
                    @if (request('search'))
                        Menampilkan <strong>{{ $equipments->count() }}</strong> dari <strong>{{ $equipments->total() }}</strong> hasil pencarian untuk kata kunci <strong>"{{ request('search') }}"</strong>
                    @else
                        Menampilkan <strong>{{ $equipments->count() }}</strong> dari <strong>{{ $equipments->total() }}</strong> hasil terfilter
                    @endif
                </div>
                <div>
                    Halaman {{ $equipments->currentPage() }} dari {{ $equipments->lastPage() }}
                </div>
            </div>
        @endif
    </section>

    <!-- Daftar Kartu Aset -->
    <section class="equipments-grid" aria-label="Daftar Kartu Inventaris Aset">
        @forelse ($equipments as $equipment)
            <article class="equipment-card">
                <!-- Thumbnail Foto Fisik -->
                <div class="card-media-wrapper">
                    @if ($equipment->image_path)
                        <img src="{{ asset('storage/' . $equipment->image_path) }}" 
                             alt="Foto {{ $equipment->name }}" 
                             loading="lazy">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94a3b8;font-size:0.9rem;">
                            Tidak ada foto
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="card-badge-top">
                        @if ($equipment->status === 'tersedia')
                            <span class="status-badge badge-tersedia">
                                <span class="badge-dot"></span> Tersedia
                            </span>
                        @elseif ($equipment->status === 'dipinjam')
                            <span class="status-badge badge-dipinjam">
                                <span class="badge-dot"></span> Dipinjam
                            </span>
                        @else
                            <span class="status-badge badge-dalam_perbaikan">
                                <span class="badge-dot"></span> Dalam Perbaikan
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Konten Informasi Aset -->
                <div class="card-content">
                    <div class="asset-meta">
                        <span class="asset-code">{{ $equipment->asset_code }}</span>
                        <span class="asset-category">{{ $equipment->category->name }}</span>
                    </div>

                    <h3 class="asset-title" title="{{ $equipment->name }}">{{ $equipment->name }}</h3>

                    <p class="asset-spec">{{ $equipment->specification }}</p>

                    <div class="asset-fee-block">
                        <span class="fee-label">Biaya Sewa:</span>
                        <span class="fee-amount">Rp {{ number_format($equipment->daily_rental_fee, 0, ',', '.') }}</span>
                        <span class="fee-period">/ hari</span>
                    </div>

                    <div class="card-actions">
                        <a href="{{ route('equipments.show', $equipment->id) }}" class="btn btn-secondary btn-sm">
                            Detail Spesifikasi
                        </a>
                        
                        @if ($equipment->status === 'tersedia')
                            <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-primary btn-sm">
                                Ajukan Pinjam
                            </a>
                        @else
                            <button type="button" class="btn btn-secondary btn-sm" disabled title="Perangkat sedang {{ str_replace('_', ' ', $equipment->status) }}">
                                {{ $equipment->status === 'dipinjam' ? 'Sedang Dipinjam' : 'Dalam Perbaikan' }}
                            </button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <h3>Tidak Ada Perangkat yang Ditemukan</h3>
                <p>Tidak ada peralatan yang sesuai dengan kriteria pencarian atau filter yang Anda pilih.</p>
                <a href="{{ route('equipments.index') }}" class="btn btn-primary">Reset Filter &amp; Tampilkan Semua</a>
            </div>
        @endforelse
    </section>

    <!-- Paginasi -->
    @if ($equipments->hasPages())
        <div class="pagination-wrapper">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
                Menampilkan data ke-<strong>{{ $equipments->firstItem() }}</strong> sampai ke-<strong>{{ $equipments->lastItem() }}</strong> dari <strong>{{ $equipments->total() }}</strong> total perangkat.
            </div>
            <div>
                <ul class="pagination-custom">
                    {{-- Previous Page Link --}}
                    @if ($equipments->onFirstPage())
                        <li class="disabled"><span>&laquo; Prev</span></li>
                    @else
                        <li><a href="{{ $equipments->previousPageUrl() }}" rel="prev">&laquo; Prev</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($equipments->getUrlRange(1, $equipments->lastPage()) as $page => $url)
                        @if ($page == $equipments->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($equipments->hasMorePages())
                        <li><a href="{{ $equipments->nextPageUrl() }}" rel="next">Next &raquo;</a></li>
                    @else
                        <li class="disabled"><span>Next &raquo;</span></li>
                    @endif
                </ul>
            </div>
        </div>
    @endif
@endsection
