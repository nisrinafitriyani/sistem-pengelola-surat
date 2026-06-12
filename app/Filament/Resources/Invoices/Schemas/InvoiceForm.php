<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
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

                Section::make('Data Invoice')->schema([
                    TextInput::make('invoice_number')->label('Nomor Invoice')->unique(ignoreRecord: true)->required(),
                    DatePicker::make('date')->label('Tanggal'),
                    TextInput::make('reff_po_number')
                        ->required()
                        ->label(fn ($record) => $record?->approval?->quotation?->type === 'wo' ? 'Reff WO No' : 'Reff PO No'),
                    TextInput::make('contract_sum')->label('Contract Sum')->numeric()->prefix('Rp')->readOnly(),
                ])->columns(2),

                Section::make('Informasi Bank')->schema([
                    TextInput::make('bank_name')->label('Bank')->placeholder('Bank BCA'),
                    TextInput::make('bank_branch')->label('Cabang')->placeholder('KCU Rawamangun, Jakarta Timur'),
                    TextInput::make('bank_account_number')->label('No. Rekening'),
                    TextInput::make('bank_account_name')->label('Atas Nama'),
                ])->columns(2),

                Section::make('Tanda Tangan & Stempel')->schema([
                    TextInput::make('signature_name')->label('Nama Penanda Tangan'),
                    TextInput::make('signature_role')->label('Jabatan'),
                    FileUpload::make('signature_path')
                        ->label('Upload Tanda Tangan')
                        ->image()
                        ->directory('invoices/signatures')
                        ->nullable(),
                    FileUpload::make('stamp_path')
                        ->label('Upload Stempel')
                        ->image()
                        ->directory('invoices/stamps')
                        ->nullable(),
                ])->columns(2),

                Section::make('Catatan')->schema([
                    Textarea::make('notes')->label('Catatan Tambahan')->columnSpanFull(),
                ]),
            ]);
    }
}
