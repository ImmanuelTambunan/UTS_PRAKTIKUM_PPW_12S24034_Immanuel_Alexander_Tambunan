<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('category');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('specification', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->category_id !== 'semua' && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status') && $request->status !== 'semua' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $equipments = $query->latest('id')->paginate(6)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('equipments.index', compact('equipments', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('equipments.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:20', 'unique:equipments,asset_code'],
            'name' => ['required', 'string', 'min:5', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'daily_rental_fee' => ['required', 'numeric', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'specification' => ['required', 'string'],
            'condition' => ['nullable', 'in:baik,rusak_ringan,rusak_berat'],
        ], [
            'asset_code.required' => 'Kode aset inventaris wajib diisi.',
            'asset_code.unique' => 'Kode aset inventaris ini sudah terdaftar.',
            'name.required' => 'Nama perangkat wajib diisi.',
            'name.min' => 'Nama perangkat minimal 5 karakter.',
            'name.max' => 'Nama perangkat maksimal 150 karakter.',
            'category_id.required' => 'Kategori perangkat wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'daily_rental_fee.required' => 'Tarif kompensasi harian wajib diisi.',
            'daily_rental_fee.numeric' => 'Tarif kompensasi harian harus berupa angka valid.',
            'daily_rental_fee.min' => 'Tarif kompensasi harian tidak boleh bernilai negatif.',
            'image.required' => 'Berkas foto fisik perangkat wajib diunggah.',
            'image.image' => 'Berkas yang diunggah harus berupa file gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan hanya JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran berkas gambar maksimal adalah 2048 KB (2 MB).',
            'specification.required' => 'Spesifikasi teknis perangkat wajib diisi.',
        ]);

        $imageFile = $request->file('image');
        $extension = $imageFile->getClientOriginalExtension();
        $safeAssetCode = Str::slug($validated['asset_code'], '_');
        $fileName = time() . '_' . $safeAssetCode . '.' . $extension;
        $imagePath = $imageFile->storeAs('equipments', $fileName, 'public');

        $equipment = Equipment::create([
            'category_id' => $validated['category_id'],
            'asset_code' => strtoupper($validated['asset_code']),
            'name' => $validated['name'],
            'specification' => $validated['specification'],
            'condition' => $request->input('condition', 'baik'),
            'status' => 'tersedia',
            'daily_rental_fee' => $validated['daily_rental_fee'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('equipments.index')
            ->with('success', "[SUKSES] Perangkat '{$equipment->name}' (Kode: {$equipment->asset_code}) berhasil didaftarkan!");
    }

    public function show($id)
    {
        $equipment = Equipment::with(['category', 'loanRequests' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        return view('equipments.show', compact('equipment'));
    }
}
