<?php

namespace App\Filament\Resources\Handovers\Tables;

use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HandoversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('approval.quotation', function ($q) {
                $q->whereNull('deleted_at')->where('type', 'wo');
            }))
            ->columns([
                TextColumn::make('reference_number')->label('No. Berita Acara')->searchable()->copyable(),
                TextColumn::make('approval.quotation.reference_number')->label('No. Surat Penawaran')->searchable()->copyable(),
                TextColumn::make('approval.quotation.client.name')->label('Klien')->searchable(),
                TextColumn::make('approval.quotation.project_name')->label('Proyek')->searchable(),
                TextColumn::make('date')->label('Tanggal')->date(),
                TextColumn::make('attachment_path')
                    ->label('Lampiran')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'Belum Ada';
                        $ext = pathinfo($state, PATHINFO_EXTENSION);
                        return strtoupper($ext);
                    })
                    ->badge()
                    ->color(fn ($state) => match (strtolower(pathinfo($state ?? '', PATHINFO_EXTENSION))) {
                        'pdf' => 'info',
                        'jpg', 'jpeg', 'png', 'webp' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn ($state) => match (strtolower(pathinfo($state ?? '', PATHINFO_EXTENSION))) {
                        'pdf' => 'heroicon-o-document-text',
                        'jpg', 'jpeg', 'png', 'webp' => 'heroicon-o-camera',
                        default => 'heroicon-o-paper-clip',
                    })
                    ->url(fn ($record) => $record->attachment_path ? route('attachment.serve', ['path' => $record->attachment_path]) : null)
                    ->openUrlInNewTab(),
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
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
