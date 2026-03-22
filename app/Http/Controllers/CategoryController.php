<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Models\Category;
use App\Services\QuestionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index', ['categories' => Category::getCategoriesLevel0()]);
    }

    public function detail($category)
    {
        try {
            $category = Category::getByCode($category);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        Event(new ViewEvent($category));

        $childs = self::getCurrCategoryChilds($category);
        $questions = QuestionService::getList(['active' => true, 'category_id' => $category->id]);

        return view('categories.detail', compact('category', 'childs', 'questions'));
    }

    static function getCurrCategoryChilds(Category $category)
    {
        return Cache::remember('category_childs_'.$category->id, Category::$timeCache, function() use ($category){
            $categories = Category::query()
                ->where('active', 1)
                ->where('parent_id', $category->id)
                ->get();
            $categories->load('file');
            return $categories;
        });
    }
}
