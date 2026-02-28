<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TempFile extends Model
{
    public $guarded = [];

    public function getFullUrlAttribute(): string
    {
        return asset('storage/temp/' . $this->path);
    }

    public static function boot()
    {

        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::created(function ($item) {

            // $item->path_thumbnail = FileService::createThumbWebp($item->path);

        });

        static::deleting(function ($file) {
            $disk = Storage::disk('public');
            if ($disk->exists('temp/' . $file->path))
                $disk->delete('temp/' . $file->path);
        });

    }
}
