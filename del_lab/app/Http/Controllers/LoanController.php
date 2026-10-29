<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\LoanRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    public function index()
    {
        $loans = LoanRequest::with('equipment.category')->latest('id')->paginate(10);
        return view('loans.index', compact('loans'));
    }

    public function create(Request $request)
    {
        $equipments = Equipment::with('category')->orderBy('name')->get();
        $selectedEquipmentId = $request->query('equipment_id');

        return view('loans.create', compact('equipments', 'selectedEquipmentId'));
    }

    public function store(Request $request)
    {
        $messages = [
            'equipment_id.required' => 'Pilihan perangkat laboratorium wajib dipilih.',
            'equipment_id.exists' => 'Perangkat yang dipilih tidak ditemukan dalam basis data.',
            'borrower_name.required' => 'Nama peminjam wajib diisi.',
            'borrower_name.min' => 'Nama peminjam wajib diisi minimal 3 karakter.',
            'borrower_nim.required' => 'NIM mahasiswa wajib diisi.',
            'borrower_nim.regex' => 'Format NIM harus terdiri dari tepat 8 digit angka/huruf institusi.',
            'borrower_email.required' => 'Alamat surel mahasiswa wajib diisi.',
            'borrower_email.email' => 'Format alamat surel yang Anda masukkan tidak valid.',
            'loan_date.required' => 'Tanggal peminjaman wajib diisi.',
            'loan_date.after_or_equal' => 'Tanggal peminjaman tidak boleh sebelum tanggal hari ini.',
            'expected_return_date.required' => 'Rencana tanggal pengembalian wajib diisi.',
            'expected_return_date.after_or_equal' => 'Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.',
            'purpose.required' => 'Tujuan peminjaman wajib diisi secara jelas.',
        ];

        $validator = Validator::make($request->all(), [
            'equipment_id' => ['required', 'exists:equipments,id'],
            'borrower_name' => ['required', 'string', 'min:3'],
            'borrower_nim' => ['required', 'string', 'regex:/^[a-zA-Z0-9]{8}$/'],
            'borrower_email' => ['required', 'email'],
            'loan_date' => ['required', 'date', 'after_or_equal:today'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'purpose' => ['required', 'string'],
        ], $messages);

        $validator->after(function ($validator) use ($request) {
            // cek status perangkat
            if ($request->filled('equipment_id')) {
                $equipment = Equipment::find($request->equipment_id);
                if ($equipment && $equipment->status !== 'tersedia') {
                    $statusLabel = $equipment->status === 'dipinjam' ? 'dipinjam' : 'dalam perbaikan';
                    $validator->errors()->add(
                        'equipment_id',
                        "Perangkat {$equipment->name} saat ini sedang {$statusLabel} dan tidak tersedia."
                    );
                }
            }

            // cek durasi sewa maksimal 7 hari
            if ($request->filled('loan_date') && $request->filled('expected_return_date')) {
                try {
                    $loanDate = Carbon::parse($request->loan_date);
                    $returnDate = Carbon::parse($request->expected_return_date);

                    if ($returnDate->greaterThanOrEqualTo($loanDate)) {
                        if ($loanDate->diffInDays($returnDate) > 7) {
                            $validator->errors()->add(
                                'expected_return_date',
                                'Durasi peminjaman maksimal adalah 7 hari kalender.'
                            );
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->route('loans.create')
                ->withErrors($validator)
                ->withInput()
                ->with('error', '[GAGAL] Pengajuan tidak dapat diproses. Silakan periksa kembali seluruh masukan data Anda.');
        }

        $equipment = Equipment::findOrFail($request->equipment_id);

        // hitung estimasi biaya sewa
        $loanDate = Carbon::parse($request->loan_date);
        $returnDate = Carbon::parse($request->expected_return_date);
        $diffDays = $loanDate->diffInDays($returnDate);
        $durationDays = $diffDays == 0 ? 1 : $diffDays;
        $totalEstimatedFee = $durationDays * $equipment->daily_rental_fee;

        $loanRequest = LoanRequest::create([
            'equipment_id' => $equipment->id,
            'borrower_name' => $request->borrower_name,
            'borrower_nim' => strtoupper($request->borrower_nim),
            'borrower_email' => $request->borrower_email,
            'loan_date' => $request->loan_date,
            'expected_return_date' => $request->expected_return_date,
            'purpose' => $request->purpose,
            'status' => 'diajukan',
            'total_estimated_fee' => $totalEstimatedFee,
        ]);

        // update status perangkat menjadi dipinjam
        $equipment->update([
            'status' => 'dipinjam',
        ]);

        return redirect()->route('equipments.index')
            ->with('success', "[SUKSES] Pengajuan peminjaman untuk perangkat '{$equipment->name}' berhasil diajukan dengan estimasi biaya Rp " . number_format($totalEstimatedFee, 0, ',', '.') . "!");
    }
}
