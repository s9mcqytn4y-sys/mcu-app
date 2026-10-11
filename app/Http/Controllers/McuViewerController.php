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

        if (! $pemeriksaan || $pemeriksaan->status_kelayakan === 'DRAF') {
            return response()->view('errors.invalid-qr', [], 404);
        }

        // Rekam audit log akses publik hasil MCU dengan metadata minimum tanpa PII mentah
        LogAudit::rekam(
            tindakan: 'AKSES_VERIFIKASI_MCU',
            tipeModel: PemeriksaanMcu::class,
            idModel: $pemeriksaan->id,
            rincian: [
                'nomor_mcu' => $pemeriksaan->nomor_mcu,
                'status' => 'TERVERIFIKASI',
            ]
        );

        return view('mcu.viewer', compact('pemeriksaan'));
    }

    /**
     * Mengambil path berkas fisik lampiran secara aman.
     * Mendukung disk privat utama serta fallback disk publik untuk data lama.
     */
    protected function getAttachmentFilePath(string $uuid, int $index): array
    {
        $pemeriksaan = PemeriksaanMcu::with(['pasien', 'lampiranMcu'])
            ->where('token_verifikasi', $uuid)
            ->firstOrFail();

        $lampiran = $pemeriksaan->lampiranMcu->values()->get($index);

        if (! $lampiran) {
            abort(404, 'Lampiran pemeriksaan MCU tidak ditemukan.');
        }

        $relativePath = ltrim($lampiran->path_berkas, '/\\');
        
        // Cek disk privat/lokal terlebih dahulu
        $disk = Storage::disk('local')->exists($relativePath) ? 'local' : (Storage::disk('public')->exists($relativePath) ? 'public' : null);

        if (! $disk) {
            abort(404, 'Berkas fisik lampiran tidak ditemukan di penyimpanan server.');
        }

        $fullPath = Storage::disk($disk)->path($relativePath);

        // Pencegahan path traversal
        $realPath = realpath($fullPath);
        if ($realPath === false || ! file_exists($realPath)) {
            abort(404, 'Akses berkas tidak valid atau berkas tidak tersedia.');
        }

        return [$realPath, $lampiran, $pemeriksaan];
    }

    /**
     * Membuka dan menampilkan berkas lampiran langsung di peramban (inline PDF/Gambar) dengan audit log.
     * Dibatasi hanya untuk petugas terautentikasi sesuai UU Pelindungan Data Pribadi (UU PDP).
     */
    public function viewFile(string $uuid, int $index): BinaryFileResponse
    {
        abort_unless(auth()->check(), 403, 'Akses berkas lampiran medis dibatasi untuk petugas berwenang sesuai ketentuan UU Pelindungan Data Pribadi (UU PDP).');

        [$path, $lampiran, $pemeriksaan] = $this->getAttachmentFilePath($uuid, $index);
        $mimeType = File::mimeType($path) ?: 'application/octet-stream';
        $safeFilename = basename($path);

        LogAudit::rekam(
            tindakan: 'LIHAT_LAMPIRAN_MCU',
            tipeModel: PemeriksaanMcu::class,
            idModel: $pemeriksaan->id,
            rincian: [
                'nomor_mcu' => $pemeriksaan->nomor_mcu,
                'lampiran_id' => $lampiran->id,
                'nama_berkas' => $lampiran->nama_berkas ?? $safeFilename,
            ]
        );

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . addslashes($safeFilename) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Mengunduh berkas fisik lampiran MCU dengan audit log.
     * Dibatasi hanya untuk petugas terautentikasi sesuai UU Pelindungan Data Pribadi (UU PDP).
     */
    public function downloadFile(string $uuid, int $index): BinaryFileResponse
    {
        abort_unless(auth()->check(), 403, 'Akses unduh lampiran medis dibatasi untuk petugas berwenang sesuai ketentuan UU Pelindungan Data Pribadi (UU PDP).');

        [$path, $lampiran, $pemeriksaan] = $this->getAttachmentFilePath($uuid, $index);
        $safeFilename = basename($path);

        LogAudit::rekam(
            tindakan: 'UNDUH_LAMPIRAN_MCU',
            tipeModel: PemeriksaanMcu::class,
            idModel: $pemeriksaan->id,
            rincian: [
                'nomor_mcu' => $pemeriksaan->nomor_mcu,
                'lampiran_id' => $lampiran->id,
                'nama_berkas' => $lampiran->nama_berkas ?? $safeFilename,
            ]
        );

        return response()->download($path, $safeFilename, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
