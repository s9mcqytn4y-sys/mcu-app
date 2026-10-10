<?php

declare(strict_types=1);

namespace App\Filament\Resources\PemeriksaanMcuResource\Pages;

use App\Filament\Resources\PemeriksaanMcuResource;
use App\Models\LogAudit;
use App\Models\PemeriksaanMcu;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePemeriksaanMcu extends CreateRecord
{
    protected static string $resource = PemeriksaanMcuResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data pemeriksaan MCU berhasil dibuat';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->keyBindings(['mod+s'])
            ->label('Terbitkan Sertifikat (Ctrl+S)');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->keyBindings(['escape'])
            ->label('Batal (Esc)');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            // Sinkronisasi data pasien (Cari berdasarkan NIK atau buat baru, perbarui jika sudah ada)
            $pasien = \App\Models\Pasien::updateOrCreate(
                ['no_ktp' => $data['no_ktp']],
                [
                    'nama' => $data['nama_pasien'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'no_paspor' => $data['no_paspor'] ?? null,
                ]
            );

            $lampiranPath = $data['lampiran_file'] ?? null;
            unset($data['nama_pasien'], $data['jenis_kelamin'], $data['tanggal_lahir'], $data['no_ktp'], $data['no_paspor'], $data['lampiran_file']);

            $data['pasien_id'] = $pasien->id;
            $data['nomor_mcu'] = app(\App\Actions\Mcu\GenerateNomorMcuAction::class)->generate();
            $data['token_verifikasi'] = (string) Str::uuid();
            $data['dibuat_oleh'] = \Illuminate\Support\Facades\Auth::id() ?? 1;

            /** @var \App\Models\PemeriksaanMcu $record */
            $record = static::getModel()::create($data);

            if (! empty($lampiranPath)) {
                $record->lampiranMcu()->create([
                    'path_berkas' => $lampiranPath,
                    'jenis_lampiran' => 'sertifikat',
                ]);
            }

            return $record;
        });
    }

    protected function afterCreate(): void
    {
        LogAudit::rekam(
            tindakan: 'buat_mcu',
            tipeModel: PemeriksaanMcu::class,
            idModel: $this->record->id,
            keterangan: 'Penerbitan baru sertifikat MCU nomor ' . $this->record->nomor_mcu,
        );
    }
}
