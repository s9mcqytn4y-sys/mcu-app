<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LogAudit;
use App\Models\PemeriksaanMcu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class McuViewerController extends Controller
{
    /**
     * Menampilkan halaman publik verifikasi sertifikat hasil pemeriksaan MCU.
     */
    public function show(string $uuid): Response|View
    {
        $pemeriksaan = PemeriksaanMcu::with(['pasien', 'lampiranMcu'])
            ->where('token_verifikasi', $uuid)
            ->first();

        if (! $pemeriksaan) {
            return response()->view('errors.invalid-qr', [], 404);
        }

        // Rekam audit log akses publik hasil MCU untuk compliance dan tracking
        LogAudit::rekam(
            tindakan: 'AKSES_VERIFIKASI_MCU',
            tipeModel: PemeriksaanMcu::class,
            idModel: $pemeriksaan->id,
            rincian: [
                'token' => $uuid,
                'nomor_mcu' => $pemeriksaan->nomor_mcu,
                'pasien' => $pemeriksaan->pasien->nama,
            ]
        );

        return view('mcu.viewer', compact('pemeriksaan'));
    }

    /**
     * Mengambil path berkas fisik lampiran berdasarkan token pemeriksaan dan indeks lampiran.
     */
    protected function getAttachmentFilePath(string $uuid, int $index): string
    {
        $pemeriksaan = PemeriksaanMcu::with('lampiranMcu')
            ->where('token_verifikasi', $uuid)
            ->firstOrFail();

        $lampiran = $pemeriksaan->lampiranMcu->values()->get($index);

        if (! $lampiran) {
            abort(404, 'Lampiran pemeriksaan MCU tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($lampiran->path_berkas);

        if (! File::exists($path)) {
            abort(404, 'Berkas fisik lampiran tidak ditemukan di penyimpanan server.');
        }

        return $path;
    }

    /**
     * Membuka dan menampilkan berkas lampiran langsung di peramban (inline PDF/Gambar).
     */
    public function viewFile(string $uuid, int $index): BinaryFileResponse
    {
        $path = $this->getAttachmentFilePath($uuid, $index);
        $mimeType = File::mimeType($path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    /**
     * Mengunduh berkas fisik lampiran MCU.
     */
    public function downloadFile(string $uuid, int $index): BinaryFileResponse
    {
        $path = $this->getAttachmentFilePath($uuid, $index);

        return response()->download($path);
    }
}
