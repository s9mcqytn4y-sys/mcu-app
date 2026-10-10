<?php

declare(strict_types=1);

namespace App\Filament\Resources\PemeriksaanMcuResource\Pages;

use App\Filament\Resources\PemeriksaanMcuResource;
use App\Models\LogAudit;
use App\Models\PemeriksaanMcu;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanMcu extends EditRecord
{
    protected static string $resource = PemeriksaanMcuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Konfirmasi Hapus Data MCU')
                ->modalDescription('Apakah Anda yakin ingin menghapus data pemeriksaan MCU ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus Data')
                ->modalCancelActionLabel('Batal')
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $user */
                    $user = \Illuminate\Support\Facades\Auth::user();
                    return $user !== null && $user->apakahAdministrator();
                }),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data pemeriksaan MCU berhasil diperbarui';
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->keyBindings(['mod+s'])
            ->label('Simpan Perubahan (Ctrl+S)');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->keyBindings(['escape'])
            ->label('Batal (Esc)');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing(['pasien', 'lampiranMcu']);

        $data['nama_pasien'] = $this->record->pasien?->nama;
        $data['jenis_kelamin'] = $this->record->pasien?->jenis_kelamin;
        $data['tanggal_lahir'] = $this->record->pasien?->tanggal_lahir?->format('Y-m-d');
        $data['no_ktp'] = $this->record->pasien?->no_ktp;
        $data['no_paspor'] = $this->record->pasien?->no_paspor;
        $data['lampiran_file'] = $this->record->lampiranMcu?->first()?->path_berkas;

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
            if ($record->pasien) {
                $record->pasien->update([
                    'nama' => $data['nama_pasien'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'no_ktp' => $data['no_ktp'],
                    'no_paspor' => $data['no_paspor'] ?? null,
                ]);
            }

            $lampiranPath = $data['lampiran_file'] ?? null;
            unset($data['nama_pasien'], $data['jenis_kelamin'], $data['tanggal_lahir'], $data['no_ktp'], $data['no_paspor'], $data['lampiran_file']);

            if (! empty($lampiranPath)) {
                $lampiran = $record->lampiranMcu()->first();
                if ($lampiran) {
                    $lampiran->update(['path_berkas' => $lampiranPath]);
                } else {
                    $record->lampiranMcu()->create([
                        'path_berkas' => $lampiranPath,
                        'jenis_lampiran' => 'sertifikat',
                    ]);
                }
            }

            $record->update($data);

            return $record;
        });
    }

    protected function afterSave(): void
    {
        LogAudit::rekam(
            tindakan: 'ubah_mcu',
            tipeModel: PemeriksaanMcu::class,
            idModel: $this->record->id,
            keterangan: 'Pembaruan data rekam MCU nomor ' . $this->record->nomor_mcu,
        );
    }
}
