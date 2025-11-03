<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QuestionResource\Pages;
use App\Filament\Admin\Resources\QuestionResource\RelationManagers;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Question;
use App\Models\QuestionComments;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                        QuestionComments::query()
                            ->join('comments', 'comments.id', '=', 'question_comments.comment_id')
                            ->where('question_id', $form->model->id)
                            ->pluck('comments.text', 'comment_id')
//                        todo обрезать могуть быть слишком длинные данные
                    ),
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
