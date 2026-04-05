<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Models\Category;
use App\Services\QuestionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::getCategoriesLevel0();
        if ($categories->isEmpty()) {
            return view('errors.404', ['error' => __('categories.not_found')]);
        }

        return view('categories.index', compact('categories'));
    }

    public function detail(string $category): RedirectResponse|View
    {
        $category = Category::getByCode($category);
        if (!$category) {
            return redirect()->back()->with('error', 'Category not found');
        }

        Event(new ViewEvent($category));

        $children = self::getCurrCategoryChilds($category);
        $questions = QuestionService::getList(['active' => true, 'category_id' => $category->id]);

        return view('categories.detail', compact('category', 'children', 'questions'));
    }

    static function getCurrCategoryChilds(Category $category): Collection
    {
        return Cache::remember('category_childs_' . $category->id, Category::$timeCache, function () use ($category) {
            return Category::query()
                ->where('active', 1)
                ->where('parent_id', $category->id)
                ->with(['file'])
                ->get();
        });
    }
}
