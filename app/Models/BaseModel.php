<?php

namespace App\Models;

use App\Interfaces\Models\SearchableInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BaseModel extends Model //implements SearchableInterface
{
    use HasFactory;

    static $timeCache = 43200;
    protected $searchFilter = [];

    public $guarded = [];

    public static function getByCode(?string $code){
        // use cache
        return static::class::where('code', $code)->first();
    }

    public function file() : HasOne {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

//    public function prepareSearchData(\Illuminate\Http\Client\Request $request)
//    {
//        $data = $request->validated();
//
//        if (isset($data['q'])){
//            $this->searchFilter = [
//                'title' => ['title', 'LIKE', '%' . $data['q'] . '%'],
//            ];
//        }
//
//        if (isset($data['limit'])) {
//            $limit = $data['limit'] > 0 && $data['limit'] < self::MAX_LIMIT ? $data['limit'] : self::DEFAULT_LIMIT;
//        }
//
//        if (isset($data['sort'])){
//            if ($data['sort'] === 'popular'){
//                $sortBy = 'statistics.views';
//                $order = 'desc';
//            } else {
//                [$sortBy, $order] = explode(',', $data['sort']);
//            }
//        }
//
//        $sortBy = $sortBy ?? 'id';
//        $order = $order ?? 'desc';
//        $limit = $limit ?? self::DEFAULT_LIMIT;
//        $filter = $filter ?? [];
////        submit on btn
//
//        return [$filter, [$sortBy, $order], $limit];
//    }
//
//    public function search()
//    {
//        prepareSearchData
//    }
}
