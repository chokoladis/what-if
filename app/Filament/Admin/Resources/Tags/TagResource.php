<?php

namespace App\Filament\Admin\Resources\Tags;

use App\Filament\Admin\Resources\Tags\Pages\CreateTag;
use App\Filament\Admin\Resources\Tags\Pages\EditTag;
use App\Filament\Admin\Resources\Tags\Pages\ListTags;
use App\Filament\Admin\Resources\Tags\Pages\ViewTag;
use App\Filament\Admin\Resources\Tags\Schemas\TagForm;
use App\Filament\Admin\Resources\Tags\Schemas\TagInfolist;
use App\Filament\Admin\Resources\Tags\Tables\TagsTable;
use App\Models\Question;
use App\Models\Tag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TagResource extends Resource
{
    protected static string | UnitEnum | null $navigationGroup = 'Данные';
    protected static ?string $modelLabel = 'Теги';
    protected static ?string $navigationLabel = 'Теги';
    protected static ?string $pluralModelLabel = 'Теги';

    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Hashtag;

    protected static ?string $recordTitleAttribute = 'Теги';

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TagInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
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
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'view' => ViewTag::route('/{record}'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
