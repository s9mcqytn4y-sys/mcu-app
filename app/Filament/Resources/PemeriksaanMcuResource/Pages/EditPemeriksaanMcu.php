<?php

declare(strict_types=1);

namespace App\Filament\Resources\PemeriksaanMcuResource\Pages;

use App\Filament\Resources\PemeriksaanMcuResource;
use App\Models\LogAudit;
use App\Models\PemeriksaanMcu;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditPemeriksaanMcu extends EditRecord
{
    protected static string $resource = PemeriksaanMcuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revisi_klinis')
                ->label('Revisi Klinis')
                ->icon('heroicon-o-shield-exclamation')
                ->color('warning')
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();

                    return $user !== null
                        && $user->apakahAdministrator()
                        && in_array($this->record->status_kelayakan, ['FIT', 'UNFIT', 'DICABUT'], true);
                })
                ->form([
                    Textarea::make('alasan_revisi')
                        ->label('Alasan Revisi Klinis')
                        ->required()
                        ->minLength(10)
                        ->maxLength(500)
                        ->placeholder('Contoh: Koreksi hasil audiometri susulan dari dokter spesialis.')
                        ->helperText('Alasan revisi dicatat secara permanen pada log audit medikolegal.'),

                    Select::make('status_baru')
                        ->label('Status Kelayakan Medis Baru')
                        ->options([
                            'FIT' => 'FIT',
                            'UNFIT' => 'UNFIT',
                            'DICABUT' => 'DICABUT',
                            'Draf' => 'Draf',
                        ])
                        ->default(fn () => $this->record->status_kelayakan)
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    $statusLama = $this->record->status_kelayakan;
                    $statusBaru = $data['status_baru'];
                    $alasan = $data['alasan_revisi'];

                    $this->record->update([
                        'status_kelayakan' => $statusBaru,
                        'catatan' => trim(($this->record->catatan ?? '') . "\n[Revisi Klinis " . now()->format('d/m/Y H:i') . "] " . $alasan),
                    ]);

                    LogAudit::rekam(
                        tindakan: 'revisi_klinis_mcu',
                        tipeModel: PemeriksaanMcu::class,
                        idModel: $this->record->id,
                        keterangan: "Revisi klinis oleh Administrator dari {$statusLama} menjadi {$statusBaru}. Alasan: {$alasan}",
                    );

                    Notification::make()
                        ->title('Revisi klinis berhasil diterapkan')
                        ->body("Status kelayakan diubah menjadi {$statusBaru}.")
                        ->success()
                        ->send();

                    $this->refreshFormData(['status_kelayakan', 'catatan']);
                }),

            DeleteAction::make()
                ->modalHeading('Konfirmasi Hapus Data MCU')
                ->modalDescription('Apakah Anda yakin ingin menghapus data pemeriksaan MCU ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus Data')
                ->modalCancelActionLabel('Batal')
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();
                    return $user !== null && $user->apakahAdministrator();
                }),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data pemeriksaan MCU berhasil diperbarui';
    }

    protected function getSaveFormAction(): Action
    {
        $action = parent::getSaveFormAction()
            ->keyBindings(['mod+s'])
            ->label('Simpan Perubahan (Ctrl+S)');

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isPublished = in_array($this->record->status_kelayakan, ['FIT', 'UNFIT', 'DICABUT'], true);

        if ($isPublished && ($user === null || ! $user->apakahAdministrator())) {
            $action->disabled()->tooltip('Sertifikat terbit terkunci. Perubahan memerlukan prosedur Revisi Klinis oleh Administrator.');
        }

        return $action;
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->keyBindings(['escape'])
            ->label('Batal (Esc)');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing(['pasien', 'lampiranMcu']);

        $data['pasien_id'] = $this->record->pasien_id;
        $data['pasien_nama_display'] = $this->record->pasien?->nama;
        $data['pasien_nik_display'] = $this->record->pasien?->no_ktp;
        $data['pasien_jk_display'] = $this->record->pasien?->jenis_kelamin;
        $data['pasien_tgl_lahir_display'] = $this->record->pasien?->tanggal_lahir?->format('d/m/Y');
        $data['pasien_paspor_display'] = $this->record->pasien?->no_paspor ?? '-';
        $data['lampiran_file'] = $this->record->lampiranMcu?->first()?->path_berkas;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isPublished = in_array($record->status_kelayakan, ['FIT', 'UNFIT', 'DICABUT'], true);

        if ($isPublished && ($user === null || ! $user->apakahAdministrator())) {
            throw new AuthorizationException('Sertifikat yang telah terbit hanya dapat diubah oleh Administrator melalui prosedur Revisi Klinis.');
        }

        return DB::transaction(function () use ($record, $data) {
            $lampiranPath = $data['lampiran_file'] ?? null;
            unset(
                $data['lampiran_file'],
                $data['pasien_nama_display'],
                $data['pasien_nik_display'],
                $data['pasien_jk_display'],
                $data['pasien_tgl_lahir_display'],
                $data['pasien_paspor_display']
            );

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
