<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QuestionCategoryResource\Pages;
use App\Filament\Admin\Resources\QuestionCategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\QuestionCategory;
use App\Services\FileService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class QuestionCategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('parent_id')
                    ->integer()
                    ->nullable()
                    ->default(null),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->string(),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->string(),
                Forms\Components\TextInput::make('level')
                    ->integer()
                    ->nullable()
                    ->default(0),
                Forms\Components\TextInput::make('sort')
                    ->integer()
                    ->nullable()
                    ->default(300),
                Forms\Components\Checkbox::make('active')
                    ->default(false)
                    ->nullable(),
//                todo save file
                Forms\Components\FileUpload::make('file_id')
                    ->image()
                    ->imageEditor()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) {
                        Log::debug('templ img -', [$file->path(), $file->getRealPath(), $file->getPath()]);
                        $file = FileService::save($file, 'categories');
                        return $file->id;
                    })
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('parent_id'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('sort'),
                Tables\Columns\TextColumn::make('active'),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('updated_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionCategories::route('/'),
            'create' => Pages\CreateQuestionCategory::route('/create'),
            'edit' => Pages\EditQuestionCategory::route('/{record}/edit'),
        ];
    }
}
