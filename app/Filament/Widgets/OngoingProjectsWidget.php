<?php

namespace App\Filament\Widgets;

use App\Models\Approval;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OngoingProjectsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Proyek Berjalan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Approval::query()
                    ->where('status', 'pending')
                    ->whereHas('quotation', fn($q) => $q->where('status', 'approve')->whereNull('deleted_at'))
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('quotation.client.name')->label('Klien')
                    ->limit(15),
                TextColumn::make('quotation.project_name')->label('Proyek')
                    ->limit(20),
                TextColumn::make('quotation.type')->label('Tipe')
                    ->formatStateUsing(fn (string $state) => strtoupper($state))
                    ->badge()
                    ->colors([
                        'primary' => 'po',
                        'warning' => 'wo',
                    ]),
                TextColumn::make('approval_date')->label('Tgl Mulai')
                    ->date('d M Y'),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                    ])
                    ->formatStateUsing(fn () => 'Berjalan'),
            ])
            ->paginated(false);
    }
}
