<?php

namespace App\Filament\Resources\Approvals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApprovalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penawaran')->schema([
                    Placeholder::make('quotation_ref')
                        ->label('No. Penawaran')
                        ->content(fn($record) => $record?->quotation?->reference_number ?? '-'),
                    Placeholder::make('quotation_client')
                        ->label('Klien')
                        ->content(fn($record) => $record?->quotation?->client?->name ?? '-'),
                    Placeholder::make('quotation_project')
                        ->label('Proyek')
                        ->content(fn($record) => ($record?->quotation?->project_name ?? '-') .
                            ($record?->quotation?->project_subname ? ' — ' . $record->quotation->project_subname : '')),
                    Placeholder::make('quotation_type')
                        ->label('Tipe')
                        ->content(fn($record) => strtoupper($record?->quotation?->type ?? '-')),
                ])->columns(2),

                Section::make('Data Persetujuan')->schema([
                    TextInput::make('reference_number')
                        ->label('No. Persetujuan')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->default(function ($get) {
                            $dateObj = $get('approval_date') ? \Carbon\Carbon::parse($get('approval_date')) : now();
                            $seq = \App\Models\Approval::withTrashed()
                                ->whereYear('approval_date', $dateObj->year)
                                ->count() + 1;
                            $monthRoman = self::toRoman($dateObj->month);
                            $year = $dateObj->format('Y');
                            return str_pad($seq, 3, '0', STR_PAD_LEFT) . '/APP/IMG-KI/' . $monthRoman . '/' . $year . '/' . $dateObj->format('d');
                        }),
                    DatePicker::make('approval_date')
                        ->label('Tanggal Persetujuan')
                        ->default(now())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) return;
                            $dateObj = \Carbon\Carbon::parse($state);
                            $seq = \App\Models\Approval::withTrashed()
                                ->whereYear('approval_date', $dateObj->year)
                                ->count() + 1;
                            $monthRoman = self::toRoman($dateObj->month);
                            $year = $dateObj->format('Y');
                            $set('reference_number', str_pad($seq, 3, '0', STR_PAD_LEFT) . '/APP/IMG-KI/' . $monthRoman . '/' . $year . '/' . $dateObj->format('d'));
                        }),
                    TextInput::make('client_pic_name')->label('Nama PIC Klien')->required(),
                    Select::make('status')->label('Status Persetujuan')
                        ->options(['pending' => 'Berjalan', 'completed' => 'Selesai'])
                        ->required()
                        ->default('pending'),
                ])->columns(2),

                Section::make('Lampiran')->schema([
                    FileUpload::make('attachment_path')
                        ->label('Bukti Persetujuan (Foto/PDF)')
                        ->directory('approvals')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->openable()
                        ->maxSize(10240),
                    Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                ]),
            ]);
    }

    private static function toRoman(int $number): string
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
        ];

        $result = '';
        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }
}
