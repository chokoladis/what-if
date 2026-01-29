<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Filament\Widgets\CacheControlWidget;
use Filament\Pages\Page;

class Commands extends Page
{

    protected string $view = 'filament.admin.pages.settings.commands';
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Команды';

    protected function getHeaderWidgets(): array
    {
//        todo
        return [
//            Commands::class,
        ];
    }
}