<?php

namespace App\Filament\Resources\Handovers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HandoverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penawaran')->schema([
                    Placeholder::make('quotation_ref')
                        ->label('No. Surat Penawaran')
                        ->content(fn ($record) => $record?->approval?->quotation?->reference_number ?? '-'),
                    Placeholder::make('approval_ref')
                        ->label('No. Surat Persetujuan')
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

                Section::make('Data Berita Acara')->schema([
                    TextInput::make('reference_number')->label('Nomor Referensi')->unique(ignoreRecord: true)->required(),
                    DatePicker::make('date')->label('Tanggal'),
                ])->columns(2),

                Section::make('Lampiran Berita Acara')->schema([
                    FileUpload::make('attachment_path')
                        ->label('Upload Dokumen BAST')
                        ->directory('handovers')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->openable()
                        ->maxSize(10240),
                    Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                ]),
            ]);
    }
}
