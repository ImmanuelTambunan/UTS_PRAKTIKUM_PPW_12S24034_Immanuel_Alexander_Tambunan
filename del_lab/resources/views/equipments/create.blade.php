@extends('layouts.app')

@section('title', 'Pendaftaran Inventaris Perangkat Baru - DEL-LEATS')

@section('content')
    <div class="page-title-section" style="max-width: 800px; margin: 0 auto 1.5rem;">
        <div>
            <h2>Pendaftaran Aset Laboratorium Baru</h2>
            <p>Lengkapi formulir di bawah ini untuk mencatatkan perangkat baru ke dalam basis data inventaris.</p>
        </div>
        <div>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                &larr; Kembali ke Katalog
            </a>
        </div>
    </div>

    <section class="form-container-card" aria-label="Formulir Tambah Aset">
        <form action="{{ route('equipments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid-2" style="margin-bottom: 1.25rem;">
                <!-- Kategori Perangkat -->
                <div class="form-group">
                    <label for="category_id" class="form-label">Kategori Perangkat <span style="color:#dc2626;">*</span></label>
                    <select name="category_id" 
                            id="category_id" 
                            class="form-select @error('category_id') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kode Aset Inventaris -->
                <div class="form-group">
                    <label for="asset_code" class="form-label">Kode Aset Inventaris <span style="color:#dc2626;">*</span></label>
                    <input type="text" 
                           name="asset_code" 
                           id="asset_code" 
                           class="form-control @error('asset_code') is-invalid @enderror" 
                           placeholder="Contoh: EQ-IOT-005" 
                           value="{{ old('asset_code') }}" 
                           required>
                    <span class="form-help-text">Format: EQ-[KODE_KAT]-[NOMOR]</span>
                    @error('asset_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Nama Perangkat -->
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="name" class="form-label">Nama Perangkat <span style="color:#dc2626;">*</span></label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       placeholder="Contoh: Raspberry Pi 4 Model B (8GB RAM) Starter Kit" 
                       value="{{ old('name') }}" 
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Spesifikasi Teknis -->
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="specification" class="form-label">Spesifikasi Teknis <span style="color:#dc2626;">*</span></label>
                <textarea name="specification" 
                          id="specification" 
                          rows="4" 
                          class="form-control @error('specification') is-invalid @enderror" 
                          placeholder="Jelaskan spesifikasi prosesor, RAM, penyimpanan, kelengkapan aksesoris, dan detail lainnya..." 
                          required>{{ old('specification') }}</textarea>
                @error('specification')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid-2" style="margin-bottom: 1.25rem;">
                <!-- Kondisi Fisik -->
                <div class="form-group">
                    <label for="condition" class="form-label">Kondisi Fisik Perangkat</label>
                    <select name="condition" id="condition" class="form-select @error('condition') is-invalid @enderror">
                        <option value="baik" {{ old('condition', 'baik') == 'baik' ? 'selected' : '' }}>Baik (Siap Pakai)</option>
                        <option value="rusak_ringan" {{ old('condition') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak_berat" {{ old('condition') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('condition')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tarif Kompensasi Harian -->
                <div class="form-group">
                    <label for="daily_rental_fee" class="form-label">Tarif Kompensasi Harian (Rp) <span style="color:#dc2626;">*</span></label>
                    <input type="number" 
                           name="daily_rental_fee" 
                           id="daily_rental_fee" 
                           class="form-control @error('daily_rental_fee') is-invalid @enderror" 
                           placeholder="Contoh: 15000" 
                           value="{{ old('daily_rental_fee') }}" 
                           min="0" 
                           required>
                    @error('daily_rental_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Berkas Foto Fisik -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="image" class="form-label">Berkas Foto Fisik Perangkat <span style="color:#dc2626;">*</span></label>
                <input type="file" 
                       name="image" 
                       id="image" 
                       class="form-control @error('image') is-invalid @enderror" 
                       accept="image/jpeg,image/png,image/webp" 
                       required>
                <span class="form-help-text">Format didukung: JPG, PNG, WEBP. Maksimal ukuran berkas: 2048 KB (2 MB).</span>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('equipments.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding-left: 2rem; padding-right: 2rem;">
                    Simpan Perangkat
                </button>
            </div>
        </form>
    </section>
@endsection
