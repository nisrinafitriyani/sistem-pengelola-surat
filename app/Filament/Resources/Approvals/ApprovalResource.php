<?php

namespace App\Filament\Resources\Approvals;

use App\Filament\Resources\Approvals\Pages\EditApproval;
use App\Filament\Resources\Approvals\Pages\ListApprovals;
use App\Filament\Resources\Approvals\Schemas\ApprovalForm;
use App\Filament\Resources\Approvals\Tables\ApprovalsTable;
use App\Models\Approval;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApprovalResource extends Resource
{
    protected static ?string $model = Approval::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Surat Persetujuan';


    protected static ?string $modelLabel = 'Surat Persetujuan';

    protected static ?string $pluralModelLabel = 'Surat Persetujuan';

    public static function getNavigationGroup(): ?string
    {
        return 'Operasional';
    }
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false; // Approval hanya dibuat otomatis via Observer
    }

    public static function form(Schema $schema): Schema
    {
        return ApprovalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApprovalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApprovals::route('/'),
            'edit' => EditApproval::route('/{record}/edit'),
            // No 'create' page — approval dibuat otomatis saat quotation di-approve
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
