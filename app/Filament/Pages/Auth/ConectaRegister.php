<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Override;

class ConectaRegister extends Register
{
    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome Completo')
                    ->required()
                    ->maxLength(255),

                TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->placeholder('00.000.000/0000-00')
                    ->required()
                    ->unique()
                    ->mask(RawJs::make(<<<'JS'
                        '99.999.999/9999-99'
                    JS
                    )),

                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->same('passwordConfirmation'),

                TextInput::make('passwordConfirmation')
                    ->label('Confirmar Senha')
                    ->password()
                    ->required(),
            ]);
    }
}
