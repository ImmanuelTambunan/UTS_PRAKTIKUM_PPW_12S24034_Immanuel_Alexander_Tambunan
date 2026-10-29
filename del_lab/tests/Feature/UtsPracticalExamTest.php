<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UtsPracticalExamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * KASUS UJI 1: Pendaftaran inventaris baru yang valid (Skenario Positif)
     */
    public function test_kasus_uji_1_pendaftaran_inventaris_valid(): void
    {
        Storage::fake('public');

        $iotCategory = Category::where('code', 'IOT')->first();

        $samplePngPath = storage_path('app/public/equipments/raspberry_pi4_kit.png');
        $file = UploadedFile::fake()->createWithContent('raspberry_pi4_kit.png', file_get_contents($samplePngPath));

        $response = $this->post('/equipments', [
            'category_id' => $iotCategory->id,
            'asset_code' => 'EQ-IOT-005',
            'name' => 'Raspberry Pi 4 Model B (8GB RAM) Starter Kit',
            'specification' => 'Quad-core Cortex-A72 @1.5GHz, 8GB LPDDR4, MicroSD 64GB Class 10, Casing Alumunium Aktif Fan, Power Supply Type-C Resmi',
            'condition' => 'baik',
            'daily_rental_fee' => 15000,
            'image' => $file,
        ]);

        // 1. Respons HTTP 302 Dialihkan ke /equipments
        $response->assertStatus(302);
        $response->assertRedirect('/equipments');

        // 2. Flash Session Notification
        $response->assertSessionHas('success', "[SUKSES] Perangkat 'Raspberry Pi 4 Model B (8GB RAM) Starter Kit' (Kode: EQ-IOT-005) berhasil didaftarkan!");

        // 3. Verifikasi Data pada Basis Data
        $this->assertDatabaseHas('equipments', [
            'category_id' => $iotCategory->id,
            'asset_code' => 'EQ-IOT-005',
            'name' => 'Raspberry Pi 4 Model B (8GB RAM) Starter Kit',
            'condition' => 'baik',
            'status' => 'tersedia',
            'daily_rental_fee' => 15000,
        ]);

        $createdEquipment = Equipment::where('asset_code', 'EQ-IOT-005')->first();
        $this->assertNotNull($createdEquipment);
        $this->assertNotNull($createdEquipment->image_path);

        // 4. File disimpan di public disk
        Storage::disk('public')->assertExists($createdEquipment->image_path);
    }

    /**
     * KASUS UJI 2: Pengajuan peminjaman dengan data tidak valid (Skenario Negatif)
     */
    public function test_kasus_uji_2_pengajuan_peminjaman_tidak_valid(): void
    {
        $borrowedServer = Equipment::where('asset_code', 'EQ-SRV-001')->first();
        $this->assertEquals('dipinjam', $borrowedServer->status);

        $response = $this->from('/loans/create')->post('/loans', [
            'equipment_id' => $borrowedServer->id,
            'borrower_name' => 'Jo', // < 3 karakter
            'borrower_nim' => '12S23', // != 8 karakter
            'borrower_email' => 'johannes.del.ac.id', // invalid email
            'loan_date' => '2026-10-20', // lampau
            'expected_return_date' => '2026-11-20', // durasi 31 hari (> 7 hari)
            'purpose' => 'Praktikum',
        ]);

        // 1. Respons HTTP 302 dialihkan kembali ke /loans/create
        $response->assertStatus(302);
        $response->assertRedirect('/loans/create');

        // 2. Flash alert gagal
        $response->assertSessionHas('error', '[GAGAL] Pengajuan tidak dapat diproses. Silakan periksa kembali seluruh masukan data Anda.');

        // 3. Pesan Galat Spesifik di Bawah Masing-masing Elemen Input
        $response->assertSessionHasErrors([
            'equipment_id' => 'Perangkat Dell PowerEdge T40 Server saat ini sedang dipinjam dan tidak tersedia.',
            'borrower_name' => 'Nama peminjam wajib diisi minimal 3 karakter.',
            'borrower_nim' => 'Format NIM harus terdiri dari tepat 8 digit angka/huruf institusi.',
            'borrower_email' => 'Format alamat surel yang Anda masukkan tidak valid.',
            'loan_date' => 'Tanggal peminjaman tidak boleh sebelum tanggal hari ini.',
            'expected_return_date' => 'Durasi peminjaman maksimal adalah 7 hari kalender.',
        ]);
    }

    /**
     * KASUS UJI 3: Pencarian & Pemfilteran Katalog Aset (HTTP GET Query String)
     */
    public function test_kasus_uji_3_pencarian_dan_filter_katalog(): void
    {
        $response = $this->get('/equipments?search=drone&category_id=3&status=tersedia&page=1');

        $response->assertStatus(200);

        // Keterangan ringkasan
        $response->assertSee('Menampilkan');
        $response->assertSee('drone');

        // Konten kartu yang dirender
        $response->assertSee('EQ-DRN-001');
        $response->assertSee('DJI Mavic Air 2 Fly More Combo');
        $response->assertSee('EQ-DRN-003');
        $response->assertSee('FPV Racing Drone BetaFPV Cetus Pro');

        // Memastikan server tidak muncul dalam filter drone
        $response->assertDontSee('EQ-SRV-001');
    }

    /**
     * Pengujian Skenario Peminjaman Sukses & Mutasi Status Perangkat
     */
    public function test_pengajuan_peminjaman_sukses_dan_mutasi_status(): void
    {
        $availableEquipment = Equipment::where('status', 'tersedia')->first();
        $this->assertNotNull($availableEquipment);

        $response = $this->post('/loans', [
            'equipment_id' => $availableEquipment->id,
            'borrower_name' => 'Immanuel Alexander Tambunan',
            'borrower_nim' => '12S24034',
            'borrower_email' => 'immanuel.tambunan@students.del.ac.id',
            'loan_date' => now()->toDateString(),
            'expected_return_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Pengujian drone mapping dan computer vision di area Danau Toba.',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/equipments');
        $response->assertSessionHas('success');

        // Status perangkat termutasi menjadi 'dipinjam'
        $availableEquipment->refresh();
        $this->assertEquals('dipinjam', $availableEquipment->status);

        // Record loan_requests tersimpan
        $this->assertDatabaseHas('loan_requests', [
            'equipment_id' => $availableEquipment->id,
            'borrower_nim' => '12S24034',
            'borrower_name' => 'Immanuel Alexander Tambunan',
        ]);
    }
}
