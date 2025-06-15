<?php

namespace App\Filament\Admin\Resources\QuestionCategoryResource\Pages;

use App\Filament\Admin\Resources\QuestionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionCategories extends ListRecords
{
    protected static string $resource = QuestionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
