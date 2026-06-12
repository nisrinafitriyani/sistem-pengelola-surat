<?php

namespace App\Filament\Resources\Users\Schemas;

//use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                \Filament\Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'viewer' => 'Viewer',
                    ])
                    ->required()
                    ->default('viewer'),
            ]);
    }
}
