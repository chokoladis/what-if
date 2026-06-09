<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TempFile extends Model
{
    public $guarded = [];

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

    public function getFullUrlAttribute(): string
    {
        return asset('storage/temp/' . $this->path);
    }

    public function getShortOriginalName()
    {
        return mb_strlen($this->original_name) > 10
            ? mb_substr($this->original_name, 0, 10) . '...'
            : $this->original_name;
    }
}
