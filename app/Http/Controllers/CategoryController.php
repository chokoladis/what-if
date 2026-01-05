<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Models\Category;
use App\Services\QuestionService;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::getCategoriesLevel0();
        return view('categories.index', compact('categories'));
    }

    public function detail($category)
    {

        $category = Category::getElement($category);

        Event(new ViewEvent($category));

        $childs = self::getCurrCategoryChilds($category);
        $questions = QuestionService::getList(['active' => true, 'category_id' => $category->id]);

        return view('categories.detail', compact('category', 'childs', 'questions'));
    }

    public function add()
    {

        // cache and function resource activeWithParents
        $categories = Category::getDaughtersCategories();

        return view('categories.add', compact('categories'));
    }

    static function getCurrCategoryChilds(Category $category)
    {

//        $category_childs = Cache::remember($category->id.'_childs', Category::$timeCache, function() use ($category){

        $category_childs = Category::query()
            ->where('active', 1)
            ->where('parent_id', $category->id)
            ->get();
//        });

        return $category_childs;
    }
}
