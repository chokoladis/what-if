<?php

namespace App\Filament\Admin\Widgets;

use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Facades\Artisan;

class CacheControlWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            StatsOverviewWidget\Stat::make('Общий кэш', 'Очистить')
                ->description('Config, Routes, Views')
                ->descriptionIcon('heroicon-m-trash')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => 'clearCache',
                    'style' => 'cursor:pointer',
                ]),

//            StatsOverviewWidget\Stat::make('Общий кэш', 'Создать')
        ];
    }

    public function clearCache()
    {
        Artisan::call('optimize:clear');

        Notification::make()
            ->title('Кэш очищен')
            ->success()
            ->send();
    }
}
