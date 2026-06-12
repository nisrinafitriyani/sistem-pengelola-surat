<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('approval.quotation', function ($q) {
                $q->whereNull('deleted_at');
            }))
            ->columns([
                TextColumn::make('invoice_number')->label('No. Invoice')->searchable()->copyable(),
                TextColumn::make('approval.quotation.reference_number')->label('No. Penawaran')->searchable()->copyable(),
                TextColumn::make('approval.quotation.client.name')->label('Klien')->searchable()->copyable(),
                TextColumn::make('approval.quotation.project_name')->label('Proyek'),
                TextColumn::make('approval.quotation.type')->label('Tipe')
                    ->formatStateUsing(fn (string $state) => strtoupper($state))
                    ->badge()
                    ->colors([
                        'primary' => 'po',
                        'warning' => 'wo',
                    ]),
                TextColumn::make('date')->label('Tanggal')->date(),
                TextColumn::make('contract_sum')->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updater.name')
                    ->label('Diubah Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'po' => 'Purchase Order (PO)',
                        'wo' => 'Work Order (WO)',
                    ])
                    ->label('Filter Tipe')
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('approval.quotation', fn ($q) => $q->where('type', $data['value']));
                    }),
                \Filament\Tables\Filters\Filter::make('date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('date_from')->label('Tanggal Dari'),
                        \Filament\Forms\Components\DatePicker::make('date_to')->label('Tanggal Sampai'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when($data['date_from'] ?? null, fn ($q, $d) => $q->whereDate('date', '>=', $d))
                            ->when($data['date_to'] ?? null, fn ($q, $d) => $q->whereDate('date', '<=', $d));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('cetak_pdf')
                        ->label('Cetak PDF')
                        ->icon('heroicon-o-printer')
                        ->url(fn ($record) => route('invoice.pdf', $record->id))
                        ->openUrlInNewTab(),
                ]),
            ]);
    }
}
