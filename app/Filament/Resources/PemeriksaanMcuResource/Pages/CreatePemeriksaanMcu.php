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

    protected function getCreatedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Sertifikat MCU Berhasil Diterbitkan')
            ->body('Sertifikat ' . $this->record->nomor_mcu . ' untuk pasien ' . ($this->record->pasien?->nama ?? 'Pasien') . ' telah tersimpan dan siap diverifikasi.');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
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
        $userId = \Illuminate\Support\Facades\Auth::id();
        if ($userId === null) {
            throw new \Illuminate\Auth\AuthenticationException('Autentikasi diperlukan. Pengguna harus login untuk menerbitkan sertifikat MCU.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $userId) {
            $lampiranPath = $data['lampiran_file'] ?? null;
            unset($data['lampiran_file'], $data['pasien_nama_display'], $data['pasien_nik_display'], $data['pasien_jk_display'], $data['pasien_tgl_lahir_display'], $data['pasien_paspor_display']);

            $data['nomor_mcu'] = app(\App\Actions\Mcu\GenerateNomorMcuAction::class)->generate();
            $data['token_verifikasi'] = (string) Str::uuid();
            $data['dibuat_oleh'] = $userId;

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
