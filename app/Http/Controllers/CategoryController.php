<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Http\Requests\Category\StoreRequest;
use App\Models\Category;
use App\Services\FileService;
use App\Services\QuestionService;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{

    public function index(){
        $categories = Category::getCategoriesLevel0();
        return view('categories.index', compact('categories'));
    }

    public function detail($category){

        $category = Category::getElement($category);

        Event(new ViewEvent($category));

        $childs = self::getCurrCategoryChilds($category);
        $questions = QuestionService::getList(['active' => true, 'category_id' => $category->id]);

        return view('categories.detail', compact('category', 'childs', 'questions'));
    }

    public function add(){
        
        // cache and function resource activeWithParents
        $categories = Category::getDaughtersCategories();

        return view('categories.add', compact('categories'));
    }

    public function store(StoreRequest $request)
    {
        abort(403); //        off

        $data = $request->validated();

        try {
            if ($request->hasFile('img')){
                $img = $request->file('img');
                if ($img->isValid()){                    
                    $res = FileService::save($img,'categories');
                    $data['file_id'] = $res['id'];
                } else {
                    // return JsonResponse(['error' => 'Не валидный файл']);
                }
            }
            unset($data['img']);

            if ($data['parent_id'] && intval($data['parent_id']) > 0){
                $categoryParent = Category::query()->where('id', $data['parent_id'])->get(['level'])->first();
                $data['level'] = $categoryParent->level + 1;
            }

            $data['active'] = 1;
            
            $category = Category::firstOrCreate([ 'title' => $data['title'] ],$data);

            return redirect()->route('categories.index');
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function getCurrCategoryChilds(Category $category){

//        $category_childs = Cache::remember($category->id.'_childs', Category::$timeCache, function() use ($category){

        $category_childs =  Category::query()
                ->where('active', 1)
                ->where('parent_id', $category->id)
                ->get();
//        });

        return $category_childs;
    }
}
