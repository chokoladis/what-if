<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BaseModel extends Model
{

    static int $timeCache = 43200;
    public $guarded = [];

    public static function getByCode(?string $code): ?self
    {
//        cache ?
        return self::class::where('code', $code)->first();
    }

    public function file(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }
}
