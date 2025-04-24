<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Classes;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Section;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', Student::count())
                ->color('success')
                ->icon('heroicon-o-users'),
            Stat::make('Total Classes', Classes::count())
                ->color('warning')
                ->icon('heroicon-o-rectangle-stack'),
            Stat::make('Total Sections', Section::count())
                ->color('primary')
                ->icon('heroicon-o-rectangle-group'),
        ];
    }
}
