<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QuestionCategoryResource\Pages;
use App\Models\Category;
use App\Models\File;
use App\Services\FileService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class QuestionCategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    const DIR = 'categories';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->options(Category::query()->pluck('title', 'id'))
                    ->preload()
                    ->nullable()
                    ->default(null),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->string(),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->string(),
                Forms\Components\TextInput::make('sort')
                    ->integer()
                    ->nullable()
                    ->default(300),
                Forms\Components\Checkbox::make('active')
                    ->default(false)
                    ->nullable(),
//                todo save file (как вывести)?
                Forms\Components\FileUpload::make('file_id')
                    ->image()
                    ->previewable()
                    ->visibility('public')
                    ->loadStateFromRelationshipsUsing(function (Forms\Components\FileUpload $component, $record) {
                        if (!$record || !$record->file) {
                            return null;
                        }

                        /**
                         * @var File $file
                         */
                        $file = $record->file;

                        $data = [
//                            'file_id' => [
//                                'name' => $file->name,
//                                'size' => 11111, // можно подставить реальный размер из $record->file->size
//                                'type' => 'image/'.$file->expansion, // или другой, если знаешь
//                                'url' => $file->full_url,
//                                'path' => $file->full_url,
//                                'tmp_name' => $file->full_url,
//                                'uploaded' => true,
//                            ]
//                            'tmp_name' => public_path('storage/categories/' . $record->file->path),
//                            'url' => $file->full_url
                        ];

//                        Log::debug('FILE PREVIEW:', [
//                            'url' => $record->file->full_url ?? null,
//                            'exists' => file_exists(public_path('storage/categories/' . $record->file->path ?? ''))
//                        ]);

//                        $component->state(1);
                    })
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile|null $file) {
                        if ($file && $file->get()){
                            $file = FileService::save($file, self::DIR);
                            return $file->id;
                        }
                    })
//                    ->default(fn ($record) => $record?->file?->full_url)
//                    ->url(fn ($record) => optional($record->file)->full_url)
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
                Tables\Columns\ImageColumn::make('file.full_url')
                    ->label('Файл')
                    ->url(fn ($record) => optional($record->file)->full_url),
                Tables\Columns\TextColumn::make('sort'),
                Tables\Columns\TextColumn::make('active'),
                Tables\Columns\TextColumn::make('level'),
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
