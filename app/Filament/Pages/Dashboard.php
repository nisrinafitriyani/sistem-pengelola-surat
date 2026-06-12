<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
// use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('date_from')
                    ->label('Tanggal Dari')
                    ->default(now()->startOfMonth()->toDateString()),
                DatePicker::make('date_to')
                    ->label('Tanggal Sampai')
                    ->default(now()->endOfMonth()->toDateString()),
            ])
            ->columns(2);
    }
}
