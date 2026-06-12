<?php

namespace App\Filament\Resources\Handovers;

use App\Filament\Resources\Handovers\Pages\EditHandover;
use App\Filament\Resources\Handovers\Pages\ListHandovers;
use App\Filament\Resources\Handovers\Schemas\HandoverForm;
use App\Filament\Resources\Handovers\Tables\HandoversTable;
use App\Models\Handover;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HandoverResource extends Resource
{
    protected static ?string $model = Handover::class;

    protected static ?string $modelLabel = 'Berita Acara';
    protected static ?string $pluralModelLabel = 'Berita Acara';
    protected static ?string $navigationLabel = 'Berita Acara';

    public static function getNavigationGroup(): ?string
    {
        return 'Dokumen Final';
    }
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    public static function canCreate(): bool
    {
        return false; // Auto-created via ApprovalObserver
    }

    public static function form(Schema $schema): Schema
    {
        return HandoverForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HandoversTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHandovers::route('/'),
            'edit' => EditHandover::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
