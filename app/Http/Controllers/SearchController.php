<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Http\Middleware\CaptchaMiddleware;
use App\Http\Requests\Question\RightCommentStoreRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Http\Requests\Search\IndexRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionUserStatus;
use App\Services\FileService;
use App\Services\QuestionService;
use App\Services\SearchService;
use App\View\Components\CommentReply;
use http\Env\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\EventListener\FragmentListener;

class SearchController extends Controller
{

    private SearchService $searchService;

    function __construct()
    {
        $this->searchService = new SearchService();
    }

    public function index(IndexRequest $request)
    {
        [$filter, $sort, $limit] = $this->searchService->get(new Question, $request);
//        [$filter, $sort, $limit] = $this->searchService->get($request);

        $questions = QuestionService::getList($filter, $sort, $limit);
//        todo    $categories = Category::

        return view('search.index', compact('questions'));
    }

}
