<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\PerangkatDaerah;

class PerangkatDaerahController extends Controller
{
    public function perangkatIndex(Request $request): View
    {
        $search =$request->query('search');

        $perangkatList = PerangkatDaerah::withCount(['bidang', 'employments'])
            ->when($search, function ($q,$search) {
                $q->where('name', 'like', "\%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.perangkat', compact('perangkatList', 'search'));
    }

    /**
     * Simpan Perangkat Daerah Baru
     */
    public function storePerangkat(Request $request): RedirectResponse
    {
        $validated =$request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Nama dinas / perangkat daerah wajib diisi.',
            'code.max'      => 'Kode perangkat daerah maksimal 50 karakter.',
        ]);

        PerangkatDaerah::create($validated);

        return redirect()->route('perangkat')->with('success', 'Perangkat Daerah baru berhasil ditambahkan.');
    }

    public function updatePerangkat(Request $request, PerangkatDaerah$perangkatDaerah): RedirectResponse
    {
        $validated =$request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Nama dinas / perangkat daerah wajib diisi.',
            'code.max'      => 'Kode perangkat daerah maksimal 50 karakter.',
        ]);

        $perangkatDaerah->update($validated);

        return redirect()->route('perangkat')->with('success', "Data {$perangkatDaerah->name} berhasil diperbarui.");
    }

    public function destroyPerangkat(PerangkatDaerah $perangkatDaerah): RedirectResponse
    {
        if ($perangkatDaerah->bidang()->exists()) {
            return back()->withErrors(['error' => "Gagal menghapus: {$perangkatDaerah->name} masih memiliki data bidang/divisi aktif."]);
        }

        if ($perangkatDaerah->employments()->exists()) {
            return back()->withErrors(['error' => "Gagal menghapus: {$perangkatDaerah->name} masih memiliki data pegawai ASN terkait."]);
        }

        $namaDinas =$perangkatDaerah->name;
        $perangkatDaerah->delete();

        return redirect()->route('perangkat')->with('success', "Perangkat Daerah {$namaDinas} berhasil dihapus.");
    }
}
