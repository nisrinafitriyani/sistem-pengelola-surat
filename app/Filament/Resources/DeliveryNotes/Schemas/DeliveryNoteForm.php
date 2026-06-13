<?php

namespace App\Filament\Resources\DeliveryNotes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeliveryNoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penawaran')->schema([
                    Placeholder::make('quotation_ref')
                        ->label('No. Penawaran')
                        ->content(fn ($record) => $record?->approval?->quotation?->reference_number ?? '-'),
                    Placeholder::make('approval_ref')
                        ->label('No. Persetujuan')
                        ->content(fn ($record) => $record?->approval?->reference_number ?? '-'),
                    Placeholder::make('quotation_client')
                        ->label('Klien')
                        ->content(fn ($record) => $record?->approval?->quotation?->client?->name ?? '-'),
                    Placeholder::make('quotation_project')
                        ->label('Proyek')
                        ->content(fn ($record) => ($record?->approval?->quotation?->project_name ?? '-') .
                            ($record?->approval?->quotation?->project_subname ? ' — ' . $record->approval->quotation->project_subname : '')),
                    Placeholder::make('quotation_type')
                        ->label('Tipe')
                        ->content(fn ($record) => strtoupper($record?->approval?->quotation?->type ?? '-')),
                ])->columns(2),

                Section::make('Data Surat Jalan')->schema([
                    TextInput::make('delivery_number')->label('Nomor Surat Jalan')->unique(ignoreRecord: true)->required(),
                    DatePicker::make('date')->label('Tanggal')->required(),
                    TextInput::make('vehicle_type')->label('Kendaraan')->placeholder('Mobil, Truk, dll'),
                    TextInput::make('vehicle_plate')->label('No. Polisi'),
                ])->columns(2),

                Section::make('Pengirim & Penerima')->schema([
                    TextInput::make('driver_name')->label('Nama Driver / Pengirim'),
                    TextInput::make('receiver_name')->label('Nama Penerima'),
                    Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                ])->columns(2),

                Section::make('Tanda Tangan')->schema([
                    TextInput::make('signature_name')
                        ->label('Nama Penanda Tangan')
                        ->required(),
                    TextInput::make('signature_role')
                        ->label('Jabatan Penanda Tangan')
                        ->required(),
                    FileUpload::make('signature_image')
                        ->label('Upload Tanda Tangan')
                        ->image()
                        ->directory('signatures/delivery-notes')
                        ->imageEditor()
                        ->nullable()
                        ->columnSpanFull()
                ])->columns(2),
            ]);
    }
}

