<?php

namespace App\Filament\Resources\DeliveryNotes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeliveryNotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('approval.quotation', function ($q) {
                $q->whereNull('deleted_at')->where('type', 'po');
            }))
            ->columns([
                TextColumn::make('delivery_number')->label('No. Surat Jalan')->searchable()->copyable(),
                TextColumn::make('approval.quotation.reference_number')->label('No. Penawaran')->searchable()->copyable(),
                TextColumn::make('approval.quotation.client.name')->label('Klien')->searchable()->copyable(),
                TextColumn::make('approval.quotation.project_name')->label('Proyek')->searchable(),
                TextColumn::make('date')->label('Tanggal')->date(),
                TextColumn::make('driver_name')->label('Driver'),
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
                \Filament\Tables\Filters\Filter::make('date')
                    ->schema([
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
                        ->url(fn ($record) => route('delivery-note.pdf', $record->id))
                        ->openUrlInNewTab(),
                ]),
            ]);
    }
}
