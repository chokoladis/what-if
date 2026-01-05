<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BaseModel extends Model
{
    use HasFactory;

    static $timeCache = 43200;
    protected $searchFilter = [];

    public $guarded = [];

    public static function getByCode(?string $code)
    {
        // use cache
        return static::class::where('code', $code)->first();
    }

    public function file(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }
}
