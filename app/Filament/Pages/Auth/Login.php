<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function getTitle(): string | Htmlable
    {
        return 'Masuk Sistem - Klinik Indosehat 2003';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Masuk ke Sistem';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Klinik & Laboratorium Medical Check-Up Indosehat 2003';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Nama Pengguna (Username) atau Email')
            ->placeholder('Masukkan nama pengguna atau email')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes([
                'tabindex' => 1,
                'aria-label' => 'Nama Pengguna atau Email',
                'class' => 'min-h-[44px]',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Kata Sandi')
            ->placeholder('Masukkan kata sandi akun')
            ->password()
            ->revealable()
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes([
                'tabindex' => 2,
                'aria-label' => 'Kata Sandi Akun',
                'class' => 'min-h-[44px]',
            ]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Ingat Saya di Perangkat Ini')
            ->extraAttributes([
                'class' => 'cursor-pointer min-w-[20px] min-h-[20px]',
            ]);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Masuk ke Sistem')
            ->submit('authenticate');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $login = (string) ($data['login'] ?? '');
        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $loginType => $login,
            'password' => $data['password'] ?? '',
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => 'Nama pengguna/email atau kata sandi tidak valid.',
        ]);
    }
}
