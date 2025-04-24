<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Student;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStudents extends BaseWidget
{
    // protected static string $view = 'filament.widgets.latest-students';
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Latest Students';
    protected static ?string $pollingInterval = '5s';
    protected  int | string | array $columnSpan = 'full';
     

    protected function getTableHeading(): string
    {
        return 'Latest Students';
    }
    public function getTableQuery(): Builder
    {
        return Student::query()
            ->latest()
            ->take(5);
    }
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Student Name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('class.name')
                ->label('Class Name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('section.name')
                ->label('Section Name')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
