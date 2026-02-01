<?php

namespace App\Filament\Admin\Resources\Comments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Textarea::make('text')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('active'),
            ]);
    }
}
