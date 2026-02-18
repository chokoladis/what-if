<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    public $guarded = [];

    // public function question() : BelongsTo {
    //     return $this->belongsTo(Question::class);
    // }


    public function getFullUrlAttribute(): string
    {
        return asset('storage/' . $this->relation . '/' . $this->path);
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
            if ($disk->exists($file->relation . '/' . $file->path))
                $disk->delete($file->relation . '/' . $file->path);
        });

    }
}
