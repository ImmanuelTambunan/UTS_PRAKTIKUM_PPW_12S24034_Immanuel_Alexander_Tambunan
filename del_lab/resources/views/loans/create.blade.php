@extends('layouts.app')

@section('title', 'Pengajuan Peminjaman Perangkat - DEL-LEATS')

@section('content')
    <div class="page-title-section" style="max-width: 800px; margin: 0 auto 1.5rem;">
        <div>
            <h2>Formulir Pengajuan Peminjaman Aset</h2>
            <p>Ajukan permohonan peminjaman inventaris perangkat laboratorium untuk kegiatan praktikum, riset, atau tugas akhir.</p>
        </div>
        <div>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                &larr; Kembali ke Katalog
            </a>
        </div>
    </div>

    <section class="form-container-card" aria-label="Formulir Peminjaman">
        <form action="{{ route('loans.store') }}" method="POST">
            @csrf

            <!-- Pilihan Perangkat Laboratorium -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="equipment_id" class="form-label">Pilihan Perangkat Laboratorium <span style="color:#dc2626;">*</span></label>
                <select name="equipment_id" 
                        id="equipment_id" 
                        class="form-select @error('equipment_id') is-invalid @enderror" 
                        required>
                    <option value="">-- Pilih Perangkat yang Ingin Dipinjam --</option>
                    @foreach ($equipments as $item)
                        @php
                            $isSelected = old('equipment_id', $selectedEquipmentId) == $item->id;
                            $statusText = $item->status === 'tersedia' ? '[Tersedia]' : ($item->status === 'dipinjam' ? '[Sedang Dipinjam]' : '[Dalam Perbaikan]');
                        @endphp
                        <option value="{{ $item->id }}" 
                                data-fee="{{ $item->daily_rental_fee }}" 
                                data-status="{{ $item->status }}"
                                {{ $isSelected ? 'selected' : '' }}>
                            {{ $item->asset_code }} - {{ $item->name }} ({{ $statusText }} - Rp {{ number_format($item->daily_rental_fee, 0, ',', '.') }}/hari)
                        </option>
                    @endforeach
                </select>
                @error('equipment_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid-2" style="margin-bottom: 1.25rem;">
                <!-- Nama Lengkap Peminjam -->
                <div class="form-group">
                    <label for="borrower_name" class="form-label">Nama Lengkap Mahasiswa <span style="color:#dc2626;">*</span></label>
                    <input type="text" 
                           name="borrower_name" 
                           id="borrower_name" 
                           class="form-control @error('borrower_name') is-invalid @enderror" 
                           placeholder="Contoh: Jo atau Johannes Alexander" 
                           value="{{ old('borrower_name') }}" 
                           required>
                    @error('borrower_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- NIM Mahasiswa -->
                <div class="form-group">
                    <label for="borrower_nim" class="form-label">NIM Mahasiswa <span style="color:#dc2626;">*</span></label>
                    <input type="text" 
                           name="borrower_nim" 
                           id="borrower_nim" 
                           class="form-control @error('borrower_nim') is-invalid @enderror" 
                           placeholder="Format 8 karakter, contoh: 12S24034" 
                           value="{{ old('borrower_nim') }}" 
                           maxlength="8" 
                           required>
                    <span class="form-help-text">Wajib tepat 8 digit angka/huruf institusi IT Del.</span>
                    @error('borrower_nim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Alamat Surel Mahasiswa -->
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="borrower_email" class="form-label">Alamat Surel Mahasiswa (Email) <span style="color:#dc2626;">*</span></label>
                <input type="email" 
                       name="borrower_email" 
                       id="borrower_email" 
                       class="form-control @error('borrower_email') is-invalid @enderror" 
                       placeholder="Contoh: johannes@del.ac.id" 
                       value="{{ old('borrower_email') }}" 
                       required>
                @error('borrower_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-grid-2" style="margin-bottom: 1.25rem;">
                <!-- Tanggal Mulai Pinjam -->
                <div class="form-group">
                    <label for="loan_date" class="form-label">Tanggal Mulai Peminjaman <span style="color:#dc2626;">*</span></label>
                    <input type="date" 
                           name="loan_date" 
                           id="loan_date" 
                           class="form-control @error('loan_date') is-invalid @enderror" 
                           value="{{ old('loan_date', date('Y-m-d')) }}" 
                           required>
                    @error('loan_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Rencana Tanggal Pengembalian -->
                <div class="form-group">
                    <label for="expected_return_date" class="form-label">Rencana Tanggal Pengembalian <span style="color:#dc2626;">*</span></label>
                    <input type="date" 
                           name="expected_return_date" 
                           id="expected_return_date" 
                           class="form-control @error('expected_return_date') is-invalid @enderror" 
                           value="{{ old('expected_return_date') }}" 
                           required>
                    <span class="form-help-text">Maksimal durasi peminjaman adalah 7 hari kalender.</span>
                    @error('expected_return_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tujuan Peminjaman -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="purpose" class="form-label">Tujuan Peminjaman &amp; Kebutuhan Proyek <span style="color:#dc2626;">*</span></label>
                <textarea name="purpose" 
                          id="purpose" 
                          rows="3" 
                          class="form-control @error('purpose') is-invalid @enderror" 
                          placeholder="Jelaskan mata kuliah, judul tugas akhir, atau nama kegiatan riset penggunaan perangkat..." 
                          required>{{ old('purpose') }}</textarea>
                @error('purpose')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Estimasi Biaya -->
            <div id="feeCalculationBox" style="background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: var(--radius-sm); padding: 1rem 1.25rem; margin-bottom: 1.75rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.825rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">
                        Estimasi Kompensasi Sewa
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);" id="feeDurationText">
                        Pilih perangkat dan tanggal peminjaman
                    </div>
                </div>
                <div style="font-size: 1.35rem; font-weight: 800; color: var(--primary);" id="totalFeeDisplay">
                    Rp 0
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('equipments.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding-left: 2rem; padding-right: 2rem;">
                    Kirim Pengajuan Peminjaman
                </button>
            </div>
        </form>
    </section>

    <!-- Script hitung estimasi biaya -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const eqSelect = document.getElementById('equipment_id');
            const loanDateInput = document.getElementById('loan_date');
            const returnDateInput = document.getElementById('expected_return_date');
            const totalFeeDisplay = document.getElementById('totalFeeDisplay');
            const feeDurationText = document.getElementById('feeDurationText');

            function updateFee() {
                const selectedOption = eqSelect.options[eqSelect.selectedIndex];
                const feePerDay = parseInt(selectedOption ? selectedOption.getAttribute('data-fee') : 0) || 0;
                const loanDateVal = loanDateInput.value;
                const returnDateVal = returnDateInput.value;

                if (feePerDay > 0 && loanDateVal && returnDateVal) {
                    const start = new Date(loanDateVal);
                    const end = new Date(returnDateVal);
                    const diffTime = end - start;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    if (diffDays >= 0) {
                        const days = diffDays === 0 ? 1 : diffDays;
                        const total = days * feePerDay;
                        feeDurationText.textContent = `${days} hari x Rp ${feePerDay.toLocaleString('id-ID')} / hari`;
                        totalFeeDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
                        return;
                    }
                }
                feeDurationText.textContent = 'Pilih perangkat dan tanggal peminjaman';
                totalFeeDisplay.textContent = 'Rp 0';
            }

            eqSelect.addEventListener('change', updateFee);
            loanDateInput.addEventListener('change', updateFee);
            returnDateInput.addEventListener('change', updateFee);
            updateFee();
        });
    </script>
@endsection
