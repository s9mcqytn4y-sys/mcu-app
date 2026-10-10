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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas Pengguna')
                    ->description('Kelola akun login staf dan administrator klinik.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('username')
                            ->label('Nama Pengguna (Username)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->maxLength(50),

                        TextInput::make('email')
                            ->label('Alamat Surel (Email)')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('peran')
                            ->label('Peran Akses (RBAC)')
                            ->options([
                                'administrator' => 'Administrator (Akses Penuh)',
                                'staf' => 'Staf Operasional (MCU & Pasien)',
                            ])
                            ->required()
                            ->default('staf'),

                        Toggle::make('aktif')
                            ->label('Status Akun Aktif')
                            ->helperText('Pengguna non-aktif tidak dapat masuk ke sistem.')
                            ->default(true),

                        TextInput::make('password')
                            ->label('Kata Sandi Baru')
                            ->password()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->columnSpanFull(),
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

                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('peran')
                    ->label('Filter Peran')
                    ->options([
                        'administrator' => 'Administrator',
                        'staf' => 'Staf',
                    ]),
            ])
            ->actions([
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
