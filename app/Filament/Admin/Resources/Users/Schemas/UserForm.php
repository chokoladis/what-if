<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\File;
use App\Services\FileService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UserForm
{
    const string DIR_NAME = 'users';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Имя'),
                Select::make('role')
                    ->required()
                    ->options([
                        'admin' => 'admin',
                        'user' => 'user',
                    ])
                    ->default('user')
                    ->label('Роль'),
                Toggle::make('active')
                    ->label('Активность')
                    ->default(false),
                FileUpload::make('photo_id')
                    ->image()
                    ->previewable()
                    ->visibility('public')
                    ->loadStateFromRelationshipsUsing(function (FileUpload $component, $record) {
                        if (!$record || !$record->photo) {
                            return null;
                        }

                        /** @var File $file */
                        $file = $record->photo;

                        $disk = Storage::disk('public');

                        if (!$disk->exists(sprintf('/%s/%s',self::DIR_NAME, $file->path))) {
                            return;
                        }

                        $component->state(new UploadedFile(
                            $disk->path(sprintf('/%s/%s',self::DIR_NAME, $file->path)),
                            $file->name,
                        ));
                    })
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile|null $file) {
                        if ($file && $file->get()) {
                            $file = FileService::save($file, self::DIR_NAME);
                            return $file->id;
                        }
                        return null;
                    })
                    ->nullable()
                    ->label('Аватарка'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->label('Пароль'),
            ]);
    }
}
