<?php

namespace App\Filament\Resources\Approvals\Tables;

use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Approval;

class ApprovalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('quotation', function ($q) {
                // Only show approvals whose quotation still exists (not soft-deleted) and is approved
                $q->whereNull('deleted_at')->where('status', 'approve');
            }))
            ->columns([
                TextColumn::make('reference_number')->label('No. Persetujuan')->searchable()->copyable(),
                TextColumn::make('quotation.reference_number')->label('No. Surat Penawaran')->searchable()->copyable(),
                TextColumn::make('quotation.client.name')->label('Klien')->searchable()->copyable(),
                TextColumn::make('quotation.project_name')->label('Proyek')->copyable(),
                TextColumn::make('quotation.type')->label('Tipe')
                    ->formatStateUsing(fn (string $state) => strtoupper($state))
                    ->badge()
                    ->colors([
                        'primary' => 'po',
                        'warning' => 'wo',
                    ]),
                TextColumn::make('approval_date')->label('Tgl Persetujuan')->date(),
                TextColumn::make('client_pic_name')->label('PIC Klien'),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Berjalan',
                        'completed' => 'Selesai',
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                    ]),
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
                            ->when(
                                $data['date_from'] ?? null,
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('approval_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'] ?? null,
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('approval_date', '<=', $date),
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
                    ->label('Filter Tipe')
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        if (empty($data['value'])) return $query;
                        return $query->whereHas('quotation', fn ($q) => $q->where('type', $data['value']));
                    }),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Proyek Berjalan',
                        'completed' => 'Proyek Selesai',
                    ])
                    ->label('Filter Status'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('completed')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Proyek Selesai?')
                        ->modalDescription('Dokumen turunan (Berita Acara / Surat Jalan + Invoice) akan dibuat otomatis.')
                        ->visible(function (Approval $record) {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $record->status !== 'completed' && $user?->role === 'admin';
                        })
                        ->action(function (Approval $record) {
                            $record->update(['status' => 'completed']);
                        }),
                    Action::make('pending')
                        ->label('Kembalikan ke Berjalan')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Kembalikan ke Berjalan?')
                        ->modalDescription('Dokumen turunan akan disembunyikan (file upload tetap aman).')
                        ->visible(function (Approval $record) {
                            /** @var \App\Models\User|null $user */
                            $user = \Illuminate\Support\Facades\Auth::user();
                            return $record->status === 'completed' && $user?->role === 'admin';
                        })
                        ->action(function (Approval $record) {
                            $record->update(['status' => 'pending']);
                        }),
                ]),
            ]);
    }
}
