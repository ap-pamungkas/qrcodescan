<?php

namespace App\Http\Controllers;

use App\Models\LogPerangkat;
use Illuminate\Http\Request;

class LogPerangkatController extends Controller
{
    public function create(Request $request)
    {
        $qrData = $request->input('qr_data', '');
        $parsedData = [];

        if ($qrData && strlen($qrData) === 22) {
            $parsedData = [
                'id_opd' => substr($qrData, 0, 3),
                'id_perangkat' => substr($qrData, 3, 2),
                'tahun' => substr($qrData, 5, 4),
                'bulan' => substr($qrData, 9, 2),
                'tanggal' => substr($qrData, 11, 2),
                'jam' => substr($qrData, 13, 2),
                'menit' => substr($qrData, 15, 2),
                'detik' => substr($qrData, 17, 1),
                'karakter_unik' => substr($qrData, 18, 4),
                'keseluruhan' => $qrData,
            ];
        }

        return view('log_perangkat.create', compact('parsedData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_opd' => 'required|string|size:3',
            'id_perangkat' => 'required|string|size:2',
            'tahun' => 'required|string|size:4',
            'bulan' => 'required|string|size:2',
            'tanggal' => 'required|string|size:2',
            'jam' => 'required|string|size:2',
            'menit' => 'required|string|size:2',
            'detik' => 'required|string|size:1',
            'karakter_unik' => 'required|string|size:4',
            'keseluruhan' => 'required|string|size:22',
            'pegawai_id' => 'required|string|size:22',
        ]);

        LogPerangkat::create($validated);

        return redirect()->route('log_perangkat.create')->with('success', 'Data berhasil disimpan!');
    }
}
