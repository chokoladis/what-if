<?php

namespace App\Interfaces\Models;

use Illuminate\Pagination\LengthAwarePaginator;
use \Illuminate\Http\Client\Request;

interface SearchableInterface
{
    public function search() : LengthAwarePaginator | false ;
    public function prepareSearchData(Request $request) : array;
}