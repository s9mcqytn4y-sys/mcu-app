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
        return 'Silakan masukkan kredensial akun Anda';
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
            ->label('Username atau Email')
            ->placeholder('nama@email.com atau username')
            ->prefixIcon('heroicon-m-user')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes([
                'tabindex' => 1,
                'aria-label' => 'Username atau Email',
                'class' => 'min-h-[44px]',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Kata Sandi')
            ->placeholder('••••••••')
            ->password()
            ->revealable()
            ->prefixIcon('heroicon-m-lock-closed')
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
            ->label('Ingat saya')
            ->extraAttributes([
                'class' => 'cursor-pointer select-none text-sm text-gray-400',
            ]);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Masuk ke Sistem')
            ->icon('heroicon-m-arrow-right-end-on-rectangle')
            ->iconPosition('after')
            ->size('lg')
            ->extraAttributes([
                'wire:loading.attr' => 'disabled',
                'wire:target' => 'authenticate',
                'class' => 'w-full min-h-11 transition hover:scale-[1.01] active:scale-[0.99]',
            ])
            ->submit('authenticate');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $login = trim((string) ($data['login'] ?? ''));
        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $loginType => $login,
            'password' => (string) ($data['password'] ?? ''),
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => 'Username/email atau kata sandi yang Anda masukkan salah.',
        ]);
    }
}
