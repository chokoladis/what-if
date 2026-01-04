<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QuestionCategoryResource\Pages;
use App\Models\Category;
use App\Models\File;
use App\Services\FileService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class QuestionCategoryResource extends Resource
{
    protected static ?string $modelLabel = 'Категории';
    protected static ?string $navigationLabel = 'Категории';
    protected static ?string $pluralModelLabel = 'Категории';
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $model = Category::class;

    protected static null | string | \BackedEnum $navigationIcon = 'heroicon-o-rectangle-stack';

    const DIR = 'categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
//                todo убрать самого себя
                Forms\Components\Select::make('parent_id')
                    ->options(Category::query()->pluck('title', 'id'))
                    ->preload()
                    ->nullable()
                    ->default(null)
                    ->label('Родитель'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->string()
                    ->label('Заголовок'),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->string()
                    ->label('Символьный код'),
                Forms\Components\TextInput::make('sort')
                    ->integer()
                    ->nullable()
                    ->default(300)
                    ->label('Сортировка'),
                Forms\Components\Checkbox::make('active')
                    ->default(false)
                    ->nullable()
                    ->label('Активность'),
//                todo save file (как вывести)?
                Forms\Components\FileUpload::make('file_id')
                    ->image()
                    ->previewable()
                    ->visibility('public')
                    ->loadStateFromRelationshipsUsing(function (Forms\Components\FileUpload $component, $record) {
                        if (!$record || !$record->file) {
                            return null;
                        }

                        /** @var File $file */
                        $file = $record->file;
                        $path = public_path('storage/categories/' . $file->path);

                        if (!file_exists($path)) {
                            return;
                        }
//
//                        // Создаём временную копию для предпросмотра
////                        $tempPath = tempnam(sys_get_temp_dir(), 'filament_preview_') . '.' . pathinfo($file->path, PATHINFO_EXTENSION);
////                        copy($path, $tempPath);
//
////                        $arPath = explode('/', $path);
////                        unset($arPath[array_key_last($arPath)]);
////                        $arPath = implode('/', $arPath).'/';
//
                        $component->state(new \Illuminate\Http\UploadedFile(
                            $path,
                            $file->name,
                        ));
                    })
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile|null $file) {
                        if ($file && $file->get()){
                            $file = FileService::save($file, self::DIR);
                            return $file->id;
                        }
                    })
//                    ->url(fn ($record) => optional($record->file)->full_url)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('parent_id')->sortable(),
                Tables\Columns\TextColumn::make('title')->sortable(),
                Tables\Columns\TextColumn::make('code')->sortable(),
                Tables\Columns\ImageColumn::make('file.full_url')
                    ->label('Файл')
                    ->url(fn ($record) => optional($record->file)->full_url)
                    ->width(120)
                    ->height(120),
                Tables\Columns\TextColumn::make('sort')->sortable(),
                Tables\Columns\TextColumn::make('active')->sortable(),
                Tables\Columns\TextColumn::make('level')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->sortable(),
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
