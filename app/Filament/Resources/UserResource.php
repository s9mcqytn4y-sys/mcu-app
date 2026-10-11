<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen Pengguna';
    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Daftar Pengguna';
    protected static ?string $navigationGroup = 'Keamanan & Tata Kelola';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user !== null && $user->apakahAdministrator();
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas & Akses Pengguna')
                    ->description('Kelola kredensial akun login dan tingkat wewenang staf/administrator klinik.')
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Dr. Budi Santoso')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->autocomplete('off')
                            ->extraInputAttributes(['class' => 'min-h-[44px]']),

                        TextInput::make('username')
                            ->label('Nama Pengguna')
                            ->placeholder('Contoh: budi_santoso')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->maxLength(50)
                            ->autocomplete('off')
                            ->extraInputAttributes(['class' => 'min-h-[44px]']),

                        TextInput::make('email')
                            ->label('Alamat Surel')
                            ->placeholder('Contoh: budi@klinikindosehat2003.com')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->autocomplete('off')
                            ->extraInputAttributes(['class' => 'min-h-[44px]', 'inputmode' => 'email']),

                        Select::make('peran')
                            ->label('Peran Akses')
                            ->options([
                                'administrator' => 'Administrator (Akses Penuh)',
                                'staf' => 'Staf Operasional (MCU & Pasien)',
                            ])
                            ->required()
                            ->default('staf')
                            ->native(false),

                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Kosongkan untuk mengirim surel undangan aktivasi' : 'Kosongkan jika tidak ingin mengubah kata sandi')
                            ->helperText(fn (string $operation): string => $operation === 'create' ? 'Jika dikosongkan, tautan aktivasi & setel kata sandi akan otomatis dikirimkan ke email pengguna.' : 'Biarkan kosong kecuali ingin mereset kata sandi.')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(false)
                            ->autocomplete('new-password')
                            ->maxLength(255)
                            ->extraInputAttributes(['class' => 'min-h-[44px]']),

                        Toggle::make('aktif')
                            ->label('Status Akun Aktif')
                            ->helperText('Pengguna non-aktif akan diblokir dari akses masuk sistem.')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('peran')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrator' => 'danger',
                        'staf' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('status_verifikasi')
                    ->label('Verifikasi Surel')
                    ->badge()
                    ->state(fn (User $record): string => $record->hasVerifiedEmail() ? 'Terverifikasi' : 'Menunggu Aktivasi')
                    ->color(fn (string $state): string => match ($state) {
                        'Terverifikasi' => 'success',
                        default => 'warning',
                    }),

                TextColumn::make('status_online')
                    ->label('Status Sesi')
                    ->badge()
                    ->state(fn (User $record): string => $record->apakahOnline() ? 'Online' : 'Offline')
                    ->color(fn (string $state): string => match ($state) {
                        'Online' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Online' => 'heroicon-m-signal',
                        default => 'heroicon-m-signal-slash',
                    }),

                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->poll('15s')
            ->filters([
                SelectFilter::make('peran')
                    ->label('Filter Peran')
                    ->options([
                        'administrator' => 'Administrator',
                        'staf' => 'Staf',
                    ]),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('kirim_undangan')
                    ->label('Kirim Undangan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (User $record): bool => ! $record->hasVerifiedEmail())
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Ulang Undangan Aktivasi')
                    ->modalDescription('Tautan aktivasi akun dan penyetelan kata sandi baru akan dikirimkan ke alamat email pengguna.')
                    ->modalSubmitAction(fn (\Filament\Actions\StaticAction $action) => $action
                        ->label('Kirim Surel Undangan')
                        ->icon('heroicon-o-paper-airplane')
                    )
                    ->action(function (User $record, \Filament\Tables\Actions\Action $action): void {
                        try {
                            $token = \Illuminate\Support\Facades\Password::broker()->createToken($record);
                            $record->forceFill([
                                'token_undangan' => \Illuminate\Support\Str::random(60),
                                'undangan_dikirim_pada' => now(),
                            ])->saveQuietly();

                            $tautanAktivasi = \Filament\Facades\Filament::getResetPasswordUrl($token, $record);

                            $record->notify(new \App\Notifications\UndanganPenggunaNotification($token));

                            \App\Models\LogAudit::catat(
                                tindakan: 'kirim_undangan_pengguna',
                                model: $record,
                                rincian: [
                                    'email' => $record->email,
                                    'username' => $record->username,
                                ]
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Undangan Aktivasi Terkirim')
                                ->body('Surel undangan aktivasi berhasil dikirimkan ke ' . $record->email)
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('salin_tautan')
                                        ->label('Buka Tautan Aktivasi')
                                        ->url($tautanAktivasi)
                                        ->openUrlInNewTab(),
                                ])
                                ->send();
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('Gagal mengirim surel aktivasi pengguna: ' . $e->getMessage(), [
                                'email' => $record->email,
                                'trace' => $e->getTraceAsString(),
                            ]);

                            $token = $token ?? \Illuminate\Support\Facades\Password::broker()->createToken($record);
                            $tautanAktivasi = \Filament\Facades\Filament::getResetPasswordUrl($token, $record);

                            \Filament\Notifications\Notification::make()
                                ->title('Pengiriman Surel Terkendala')
                                ->body('Surel tidak dapat dikirim otomatis (' . $e->getMessage() . '). Tautan aktivasi tetap aktif dan dapat disalin langsung.')
                                ->warning()
                                ->persistent()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('buka_tautan_darurat')
                                        ->label('Buka Tautan Aktivasi')
                                        ->url($tautanAktivasi)
                                        ->openUrlInNewTab(),
                                ])
                                ->send();
                        }
                    }),
                \Filament\Tables\Actions\Action::make('salin_tautan_aktivasi')
                    ->label('Salin Tautan')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->visible(fn (User $record): bool => ! $record->hasVerifiedEmail())
                    ->action(function (User $record, \Filament\Tables\Actions\Action $action): void {
                        $token = \Illuminate\Support\Facades\Password::broker()->createToken($record);
                        $record->forceFill([
                            'token_undangan' => \Illuminate\Support\Str::random(60),
                            'undangan_dikirim_pada' => now(),
                        ])->saveQuietly();

                        $tautanAktivasi = \Filament\Facades\Filament::getResetPasswordUrl($token, $record);

                        // Salin langsung ke clipboard pengguna di peramban
                        $action->getLivewire()->js('navigator.clipboard.writeText(' . json_encode($tautanAktivasi) . ')');

                        \App\Models\LogAudit::catat(
                            tindakan: 'salin_tautan_aktivasi',
                            model: $record,
                            rincian: [
                                'email' => $record->email,
                                'username' => $record->username,
                            ]
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Tautan Aktivasi Telah Disalin')
                            ->body('Tautan aktivasi untuk ' . $record->name . ' (' . $record->email . ') otomatis tersalin ke papan klip.')
                            ->success()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('buka_tautan')
                                    ->label('Buka Tautan Aktivasi')
                                    ->url($tautanAktivasi)
                                    ->openUrlInNewTab(),
                            ])
                            ->send();
                    }),
                EditAction::make()
                    ->label('Ubah Data'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Hapus Pengguna')
                    ->modalDescription('Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini bersifat permanen.')
                    ->modalSubmitActionLabel('Ya, Hapus Pengguna')
                    ->modalCancelActionLabel('Batal')
                    ->color('danger')
                    ->hidden(fn (User $record): bool => $record->id === Auth::id()),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
