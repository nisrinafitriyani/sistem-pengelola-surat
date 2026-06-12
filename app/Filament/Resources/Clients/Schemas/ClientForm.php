<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('phone')
                    ->label('Telepon')
                    ->tel(),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email(),
                TextInput::make('pic_name')
                    ->label('Nama PIC'),
            ]);
    }
}
