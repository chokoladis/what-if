<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QuestionResource\Pages;
use App\Models\Category;
use App\Models\Comment;
use App\Models\File;
use App\Models\Question;
use App\Models\QuestionComments;
use App\Models\Tag;
use App\Models\User;
use Filament\Forms;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static string | UnitEnum | null $navigationGroup = 'Данные';
    protected static ?string $modelLabel = 'Вопросы';
    protected static ?string $navigationLabel = 'Вопросы';

    protected static ?string $pluralModelLabel = 'Вопросы';
    protected static ?string $model = Question::class;

    protected static null|string|\BackedEnum $navigationIcon = Heroicon::Megaphone;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('user_id')
                    ->options(User::query()->pluck('name', 'id'))
                    ->preload(),
                Forms\Components\Select::make('category_id')
                    ->options(Category::query()->pluck('title', 'id'))
                    ->preload()
                    ->nullable()
                    ->default(null),
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->disabled(),
                Forms\Components\Checkbox::make('active')
                    ->default(false),
                Forms\Components\Select::make('right_comment_id')
                    ->options(
                        Comment::query()
                            ->where('question_id', $schema->model->id)
                            ->pluck('text', 'id')
//                        todo обрезать могуть быть слишком длинные данные
                    ),
                Forms\Components\CheckboxList::make('tags')
                    ->relationship('tags', 'name') //todo в других местах
                    ->searchable()
                    ->columns(2),
                Forms\Components\TextInput::make('created_at')
                    ->disabled(),
                Forms\Components\TextInput::make('updated_at')
                    ->disabled(),
//            file_id
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable(),
                Tables\Columns\TextColumn::make('code')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->sortable(),
//                Tables\Columns\ImageColumn::make('file.full_url')
//                    ->label('Файл')
//                    ->url(fn ($record) => optional($record->file)->full_url),
                Tables\Columns\TextColumn::make('category_id')->sortable(),
                Tables\Columns\CheckboxColumn::make('active')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
