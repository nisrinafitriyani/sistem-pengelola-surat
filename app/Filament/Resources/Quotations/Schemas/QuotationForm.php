<?php

namespace App\Filament\Resources\Quotations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                        ->searchPrompt('Ketik untuk mencari...')
                        ->noSearchResultsMessage('Klien tidak ditemukan')
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
                    Select::make('service_type')
                        ->label('Tipe Layanan (Service)')
                        ->multiple()
                        ->searchable()
                        ->searchPrompt('Ketik untuk mencari...')
                        ->noSearchResultsMessage('Layanan tidak ditemukan')
                        ->options([
                            'Supply' => 'Supply',
                            'Delivery' => 'Delivery',
                            'Installation' => 'Installation',
                            'Demolish' => 'Demolish',
                            'Dismantle' => 'Dismantle',
                        ])->required(),
                    Select::make('work_category')
                        ->label('Kategori Pekerjaan')
                        ->options([
                            'MISC BUILDING WORK' => 'MISC BUILDING WORK',
                        ])
                        // ->searchable()
                        ->default('MISC BUILDING WORK')
                        ->required(),
                    Textarea::make('subject_description')->label('Deskripsi/Perihal')->required()->columnSpanFull(),
                ])->columns(2),

                Section::make('Daftar Item')->schema([
                    Builder::make('items')
                        ->blocks([
                            Builder\Block::make('header_row')
                                ->label('Judul Kategori')
                                ->schema([
                                    TextInput::make('uraian')->label('Judul')->required()
                                ]),
                            Builder\Block::make('item_row')
                                ->label('Item Barang/Jasa')
                                ->schema([
                                    TextInput::make('uraian')->label('Uraian')->required()->columnSpan(3)
                                        ->live(onBlur: true),
                                    TextInput::make('qty')->label('Qty')->numeric()->minValue(0)->required()->columnSpan(1)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $qty = (float) ($state ?? 0);
                                            if ($qty < 0) { $qty = 0; $set('qty', null); }
                                            $harga = (float) ($get('harga_satuan') ?? 0);
                                            $set('sub_total', $qty * $harga);
                                            self::recalculateTotal($get, $set);
                                        }),
                                    TextInput::make('unit')->label('Unit')->required()->placeholder('Bag, Ls...')->columnSpan(1)
                                        ->live(onBlur: true),
                                    TextInput::make('harga_satuan')->label('Harga Satuan')->numeric()->minValue(0)->required()->prefix('Rp')->columnSpan(2)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $harga = (float) ($state ?? 0);
                                            if ($harga < 0) { $harga = 0; $set('harga_satuan', null); }
                                            $qty = (float) ($get('qty') ?? 0);
                                            $set('sub_total', $qty * $harga);
                                            self::recalculateTotal($get, $set);
                                        }),
                                    TextInput::make('sub_total')->label('Sub Total')->numeric()->readOnly()->prefix('Rp')->columnSpan(2)
                                        ->default(0),
                                ])
                                ->columns(9),
                            Builder\Block::make('note_row')
                                ->label('Catatan Khusus')
                                ->schema([
                                    TextInput::make('uraian')->label('Catatan')->required()
                                ]),
                            Builder\Block::make('discount_row')
                                ->label('Potongan / Diskon')
                                ->schema([
                                    TextInput::make('uraian')->label('Keterangan')->default('Discount')->required()->columnSpan(3),
                                    TextInput::make('harga_satuan')->label('Nominal Diskon')->numeric()->minValue(0)->required()->prefix('- Rp')->columnSpan(4)
                                        ->live(debounce: 500)
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $harga = (float) ($state ?? 0);
                                            if ($harga < 0) { $harga = 0; $set('harga_satuan', null); }
                                            $set('sub_total', -$harga);
                                            self::recalculateTotal($get, $set);
                                        }),
                                    TextInput::make('sub_total')->label('Sub Total Diskon')->numeric()->readOnly()->prefix('Rp')->columnSpan(2)->default(0)
                                ])
                                ->columns(9)
                        ])
                        ->addActionLabel('+ Tambah Data Baru')
                        ->deleteAction(
                            fn ($action) => $action->after(function ($get, $set) {
                                self::recalculateTotal($get, $set);
                            })
                        )
                        ->columnSpanFull(),

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
        // Try getting Builder items first, if not found try Repeater items
        $items = $get('../../../items');
        $totalPath = '../../../total_amount';
        if ($items === null) {
            $items = $get('../../items');
            $totalPath = '../../total_amount';
        }
        
        if (!$items) return;
        
        $total = collect($items)->sum(function($item) {
            $data = $item['data'] ?? $item; // 'data' for builder, fallback to item for repeater
            return (float)($data['sub_total'] ?? 0);
        });
        
        $set($totalPath, $total);
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
