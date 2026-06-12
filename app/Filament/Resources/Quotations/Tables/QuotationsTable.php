<?php

namespace App\Filament\Resources\Quotations\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\Quotation;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')->label('No. Surat')->searchable()->copyable()->sortable(),
                TextColumn::make('client.name')->label('Klien')->searchable()->copyable(),
                TextColumn::make('date')->label('Tanggal')->date()->sortable(),
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
                TextColumn::make('total_amount')->label('Total')->money('IDR')->sortable(),
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updater.name')
                    ->label('Diubah Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleter.name')
                    ->label('Dihapus Oleh')
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
                TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                \Filament\Tables\Filters\Filter::make('date')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('date_from')->label('Tanggal Dari'),
                        \Filament\Forms\Components\DatePicker::make('date_to')->label('Tanggal Sampai'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'] ?? null,
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Dari ' . \Carbon\Carbon::parse($data['date_from'])->toFormattedDateString();
                        }
                        if ($data['date_to'] ?? null) {
                            $indicators['date_to'] = 'Sampai ' . \Carbon\Carbon::parse($data['date_to'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'po' => 'Purchase Order (PO)',
                        'wo' => 'Work Order (WO)',
                    ])
                    ->label('Filter Tipe'),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'approve' => 'Diterima',
                        'reject' => 'Ditolak',
                    ])
                    ->label('Filter Status'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('approve')
                        ->label('Diterima')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Terima Penawaran?')
                        ->modalDescription('Mengubah status ke Diterima akan membuat data Persetujuan secara otomatis.')
                        ->visible(function (Quotation $record) {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return !$record->trashed() && $record->status !== 'approve' && $user?->role === 'admin';
                        })
                        ->action(function (Quotation $record) {
                            $record->update(['status' => 'approve']);
                        }),
                    Action::make('draft')
                        ->label('Kembalikan ke Draft')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Kembalikan ke Draft?')
                        ->modalDescription('Data Persetujuan yang sudah ada akan disembunyikan (file tetap aman).')
                        ->visible(function (Quotation $record) {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return !$record->trashed() && $record->status !== 'draft' && $user?->role === 'admin';
                        })
                        ->action(function (Quotation $record) {
                            $record->update(['status' => 'draft']);
                        }),
                    Action::make('reject')
                        ->label('Ditolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Tolak Penawaran?')
                        ->modalDescription('Penawaran akan ditandai sebagai ditolak.')
                        ->visible(function (Quotation $record) {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return !$record->trashed() && $record->status !== 'reject' && $user?->role === 'admin';
                        })
                        ->action(function (Quotation $record) {
                            $record->update(['status' => 'reject']);
                        }),
                    Action::make('cetak_pdf')
                        ->label('Cetak PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->visible(fn (Quotation $record) => !$record->trashed())
                        ->url(fn (Quotation $record): string => route('quotation.pdf.download', $record))
                        ->openUrlInNewTab(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
