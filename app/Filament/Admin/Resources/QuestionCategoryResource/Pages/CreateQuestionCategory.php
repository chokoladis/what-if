<?php

namespace App\Filament\Admin\Resources\QuestionCategoryResource\Pages;

use App\Filament\Admin\Resources\QuestionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestionCategory extends CreateRecord
{
    protected static ?string $title = 'Создать категорию';
    protected static string $resource = QuestionCategoryResource::class;
}
