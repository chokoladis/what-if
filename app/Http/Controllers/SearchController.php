<?php

namespace App\Http\Controllers;

use App\Http\Requests\Search\IndexRequest;
use App\Models\Category;
use App\Models\Question;
use App\Repositories\CategoryRepository;
use App\Repositories\QuestionRepository;

class SearchController extends Controller
{
    private QuestionRepository $questionRepository;
    private CategoryRepository $categoryRepository;

    function __construct()
    {
        $this->questionRepository = new QuestionRepository(Question::class);
        $this->categoryRepository = new CategoryRepository(Category::class);
    }

    public function index(IndexRequest $request)
    {
        $data = $request->validated();
        $filters = $data['filters'] ?? [];

        $questions = $this->questionRepository->searchWithPaginate($data, $filters);
        $categories = $this->categoryRepository->getSearchBuilder($data, $filters)->raw();

        return view('search.index', compact('questions', 'categories'));
    }

}
