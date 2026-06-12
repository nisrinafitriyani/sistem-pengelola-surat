<?php

namespace App\Filament\Resources\DeliveryNotes;

use App\Filament\Resources\DeliveryNotes\Pages\EditDeliveryNote;
use App\Filament\Resources\DeliveryNotes\Pages\ListDeliveryNotes;
use App\Filament\Resources\DeliveryNotes\Schemas\DeliveryNoteForm;
use App\Filament\Resources\DeliveryNotes\Tables\DeliveryNotesTable;
use App\Models\DeliveryNote;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryNoteResource extends Resource
{
    protected static ?string $model = DeliveryNote::class;

    protected static ?string $modelLabel = 'Surat Jalan';
    protected static ?string $pluralModelLabel = 'Surat Jalan';
    protected static ?string $navigationLabel = 'Surat Jalan';

    public static function getNavigationGroup(): ?string
    {
        return 'Dokumen Final';
    }
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return DeliveryNoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryNotesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryNotes::route('/'),
            'edit' => EditDeliveryNote::route('/{record}/edit'),
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
