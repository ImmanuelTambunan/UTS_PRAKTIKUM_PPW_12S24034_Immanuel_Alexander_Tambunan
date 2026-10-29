<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\LoanRequest;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Categories (Minimal 3 kategori)
        $srv = Category::create([
            'name' => 'Server & Cloud',
            'code' => 'SRV',
        ]);

        $iot = Category::create([
            'name' => 'Internet of Things',
            'code' => 'IOT',
        ]);

        $drn = Category::create([
            'name' => 'Multimedia & Drone',
            'code' => 'DRN',
        ]);

        // 2. Seed Equipments (Minimal 6 data perangkat dengan status dan kondisi bervariasi)
        $eq1 = Equipment::create([
            'category_id' => $srv->id,
            'asset_code' => 'EQ-SRV-001',
            'name' => 'Dell PowerEdge T40 Server',
            'specification' => 'Intel Xeon E-2224G 3.5GHz, 16GB DDR4 ECC UDIMM, 1TB SATA HDD 7.2k Entry Level Server, 300W Bronze PSU.',
            'condition' => 'baik',
            'status' => 'dipinjam',
            'daily_rental_fee' => 75000,
            'image_path' => 'equipments/sample_dell_server.png',
        ]);

        $eq2 = Equipment::create([
            'category_id' => $srv->id,
            'asset_code' => 'EQ-SRV-002',
            'name' => 'Synology DiskStation DS923+ NAS Server',
            'specification' => 'AMD Ryzen R1600 Dual-Core 2.6GHz, 4GB DDR4 ECC RAM, 4 Bay NAS Storage Hot-swappable, Dual 1GbE RJ-45 ports.',
            'condition' => 'baik',
            'status' => 'tersedia',
            'daily_rental_fee' => 45000,
            'image_path' => 'equipments/sample_synology_nas.png',
        ]);

        $eq3 = Equipment::create([
            'category_id' => $iot->id,
            'asset_code' => 'EQ-IOT-001',
            'name' => 'Arduino Mega 2560 Advanced Sensor Kit',
            'specification' => 'Microcontroller ATmega2560 16MHz, 54 digital IO pins, 16 analog inputs, 37 in 1 sensor modules suite with acrylic case.',
            'condition' => 'baik',
            'status' => 'tersedia',
            'daily_rental_fee' => 12000,
            'image_path' => 'equipments/sample_arduino_mega.png',
        ]);

        $eq4 = Equipment::create([
            'category_id' => $iot->id,
            'asset_code' => 'EQ-IOT-002',
            'name' => 'ESP32 LoRa WAN Gateway Node',
            'specification' => 'Semtech SX1276 915MHz LoRa Transceiver, Tensilica Xtensa 32-bit LX6 dual-core, onboard 0.96 inch OLED display.',
            'condition' => 'rusak_ringan',
            'status' => 'dalam_perbaikan',
            'daily_rental_fee' => 20000,
            'image_path' => 'equipments/sample_esp32_lora.png',
        ]);

        $eq5 = Equipment::create([
            'category_id' => $drn->id,
            'asset_code' => 'EQ-DRN-001',
            'name' => 'DJI Mavic Air 2 Fly More Combo',
            'specification' => '4K/60fps video, 48MP photo resolution, 1/2-inch CMOS sensor, 34-min flight time, OcuSync 2.0 10km video transmission.',
            'condition' => 'baik',
            'status' => 'tersedia',
            'daily_rental_fee' => 50000,
            'image_path' => 'equipments/sample_dji_mavic.png',
        ]);

        $eq6 = Equipment::create([
            'category_id' => $drn->id,
            'asset_code' => 'EQ-DRN-003',
            'name' => 'FPV Racing Drone BetaFPV Cetus Pro',
            'specification' => 'Brushless flight controller, LiteRadio 2 SE transmitter, VR02 FPV goggles, Altitude hold & Turtle mode ready.',
            'condition' => 'baik',
            'status' => 'tersedia',
            'daily_rental_fee' => 25000,
            'image_path' => 'equipments/sample_fpv_drone.png',
        ]);

        // 3. Seed an existing loan request for EQ-SRV-001 to demonstrate borrowed record
        LoanRequest::create([
            'equipment_id' => $eq1->id,
            'borrower_name' => 'Johannes Sitompul',
            'borrower_nim' => '12S21001',
            'borrower_email' => 'johannes.sitompul@students.del.ac.id',
            'loan_date' => now()->subDays(2)->toDateString(),
            'expected_return_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Pengujian performa klaster server cloud untuk riset tugas akhir rekayasa perangkat lunak.',
            'status' => 'disetujui',
            'total_estimated_fee' => 75000 * 5,
        ]);
    }
}
