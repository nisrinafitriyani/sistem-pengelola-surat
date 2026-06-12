<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotationsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Penawaran Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quotation::query()
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('reference_number')->label('No. Surat')
                    ->limit(20),
                TextColumn::make('client.name')->label('Klien')
                    ->limit(15),
                TextColumn::make('type')->label('Tipe')
                    ->formatStateUsing(fn (string $state) => strtoupper($state))
                    ->badge()
                    ->colors([
                        'primary' => 'po',
                        'warning' => 'wo',
                    ]),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => 'Draft',
                        'approve' => 'Diterima',
                        'reject' => 'Ditolak',
                    })
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'approve',
                        'danger' => 'reject',
                    ]),
                TextColumn::make('total_amount')->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->paginated(false);
    }
}
