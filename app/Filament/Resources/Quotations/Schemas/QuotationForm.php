<?php

namespace App\Filament\Resources\Quotations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Klien & Penawaran')->schema([
                    Select::make('client_id')
                        ->label('Klien')
                        ->relationship('client', 'name')
                        ->searchable()
                        ->preload()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if (!$state) return;
                            $client = \App\Models\Client::find($state);
                            $abbr = $client ? $client->abbreviation : 'KI';
                            $dateObj = $get('date') ? \Carbon\Carbon::parse($get('date')) : now();
                            $seq = \App\Models\Quotation::withTrashed()
                                ->whereYear('date', $dateObj->year)
                                ->count() + 1;
                            $monthRoman = self::toRoman($dateObj->month);
                            $year = $dateObj->format('Y');
                            $day = $dateObj->format('d');
                            $set('reference_number', str_pad($seq, 3, '0', STR_PAD_LEFT) . '/SPH/IMG-' . $abbr . '/' . $monthRoman . '/' . $year . '/' . $day);
                        })
                        ->createOptionForm([
                            TextInput::make('name')->label('Nama')->required(),
                            Textarea::make('address')->label('Alamat')->columnSpanFull()->required(),
                            TextInput::make('phone')->label('Telepon')->tel(),
                            TextInput::make('email')->label('Email')->email(),
                            TextInput::make('pic_name')->label('Nama PIC'),
                        ])
                        ->required(),
                    TextInput::make('reference_number')
                        ->label('Nomor Penawaran')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->default(function ($get) {
                            $dateObj = $get('date') ? \Carbon\Carbon::parse($get('date')) : now();
                            $seq = \App\Models\Quotation::withTrashed()
                                ->whereYear('date', $dateObj->year)
                                ->count() + 1;
                            $monthRoman = self::toRoman($dateObj->month);
                            $year = $dateObj->format('Y');
                            $day = $dateObj->format('d');
                            return str_pad($seq, 3, '0', STR_PAD_LEFT) . '/SPH/IMG-KI/' . $monthRoman . '/' . $year . '/' . $day;
                        }),
                    DatePicker::make('date')
                        ->label('Tanggal')
                        ->default(now())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if (!$state) return;
                            $client = $get('client_id') ? \App\Models\Client::find($get('client_id')) : null;
                            $abbr = $client ? $client->abbreviation : 'KI';
                            $dateObj = \Carbon\Carbon::parse($state);
                            $seq = \App\Models\Quotation::withTrashed()
                                ->whereYear('date', $dateObj->year)
                                ->count() + 1;
                            $monthRoman = self::toRoman($dateObj->month);
                            $year = $dateObj->format('Y');
                            $day = $dateObj->format('d');
                            $set('reference_number', str_pad($seq, 3, '0', STR_PAD_LEFT) . '/SPH/IMG-' . $abbr . '/' . $monthRoman . '/' . $year . '/' . $day);
                        }),
                    Select::make('type')->label('Tipe Penawaran')->options([
                        'po' => 'Purchase Order (PO)',
                        'wo' => 'Work Order (WO)'
                    ])->required(),
                    Select::make('status')->label('Status')->options([
                        'draft' => 'Draft',
                        'approve' => 'Diterima',
                        'reject' => 'Ditolak'
                    ])->default('draft')->required(),
                ])->columns(2),

                Section::make('Detail Proyek')->schema([
                    TextInput::make('project_name')->label('Nama Proyek')->required(),
                    TextInput::make('project_subname')->label('Subyek Proyek'),
                    TextInput::make('service_type')->label('Tipe Layanan (Service)')->required(),
                    TextInput::make('work_category')->label('Kategori Pekerjaan')->required(),
                    Textarea::make('subject_description')->label('Deskripsi/Perihal')->required()->columnSpanFull(),
                ])->columns(2),

                Section::make('Daftar Item')->schema([
                    Repeater::make('items')
                        ->schema([
                            TextInput::make('uraian')->label('Uraian')->required()->columnSpan(3),
                            TextInput::make('qty')->label('Qty')->numeric()->required()->columnSpan(1)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $qty = (float) ($state ?? 0);
                                    $harga = (float) ($get('harga_satuan') ?? 0);
                                    $set('sub_total', $qty * $harga);
                                    self::recalculateTotal($get, $set);
                                }),
                            TextInput::make('unit')->label('Unit')->required()->placeholder('Bag, Ls...')->columnSpan(1),
                            TextInput::make('harga_satuan')->label('Harga Satuan')->numeric()->required()->prefix('Rp')->columnSpan(2)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $qty = (float) ($get('qty') ?? 0);
                                    $harga = (float) ($state ?? 0);
                                    $set('sub_total', $qty * $harga);
                                    self::recalculateTotal($get, $set);
                                }),
                            TextInput::make('sub_total')->label('Sub Total')->numeric()->readOnly()->prefix('Rp')->columnSpan(2)
                                ->default(0),
                        ])
                        ->columns(9)
                        ->defaultItems(1)
                        ->addActionLabel('+ Tambah Item')
                        ->columnSpanFull()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) { $set('total_amount', 0); return; }
                            $total = collect($state)->sum(fn($item) => (float)($item['sub_total'] ?? ((float)($item['qty'] ?? 0) * (float)($item['harga_satuan'] ?? 0))));
                            $set('total_amount', $total);
                        }),

                    TextInput::make('total_amount')->label('Total Keseluruhan')->numeric()->readOnly()->prefix('Rp')->default(0),
                ])->columnSpanFull(),

                Section::make('Tanda Tangan & Stempel')->schema([
                    TextInput::make('signature_name')
                        ->label('Nama Penanda Tangan')
                        ->required(),
                    TextInput::make('signature_role')
                        ->label('Jabatan Penanda Tangan')
                        ->required(),
                    FileUpload::make('signature_path')
                        ->label('Upload Tanda Tangan')
                        ->image()
                        ->directory('quotations/signatures')
                        ->nullable(),
                    FileUpload::make('stamp_path')
                        ->label('Upload Stempel')
                        ->image()
                        ->directory('quotations/stamps')
                        ->nullable(),
                ])->columns(2)->columnSpanFull()
            ]);
    }

    private static function recalculateTotal($get, $set): void
    {
        $items = $get('../../items');
        if (!$items) return;
        $total = collect($items)->sum(fn($item) => (float)($item['sub_total'] ?? ((float)($item['qty'] ?? 0) * (float)($item['harga_satuan'] ?? 0))));
        $set('../../total_amount', $total);
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
