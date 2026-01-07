<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SettingResource\Pages;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $modelLabel = 'Настройки';
    protected static ?string $navigationLabel = 'Настройки';
    protected static ?string $pluralModelLabel = 'Настройки';
    protected static ?string $model = Setting::class;

    protected static null|string|BackedEnum $navigationIcon = Heroicon::WrenchScrewdriver;

    protected static null|string|BackedEnum $activeNavigationIcon = 'heroicon-o-document-text';

    const DIR = 'settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('area')
                    ->string()
                    ->label('Область применения')
                    ->default('main'),
                Forms\Components\TextInput::make('name')
                    ->label('Наименование')
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->label('Значение')
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('area'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('value'),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
