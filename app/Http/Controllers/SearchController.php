<?php

namespace App\Http\Controllers;

use App\Events\ViewEvent;
use App\Http\Middleware\CaptchaMiddleware;
use App\Http\Requests\Question\RightCommentStoreRequest;
use App\Http\Requests\Question\StoreRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionUserStatus;
use App\Services\FileService;
use App\Services\QuestionService;
use App\View\Components\CommentReply;
use http\Env\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\EventListener\FragmentListener;

class SearchController extends Controller
{

    public function index(\Illuminate\Http\Request $request)
    {
//        Typesense
        [$filter, $sort, $limit] = $this->prepareData($request);

        $questions = QuestionService::getList($filter, $sort, $limit);
//        $categories = Category::

        return view('search.index', compact('questions'));
    }

    private function prepareData(\Illuminate\Http\Request $request)
    {
        $q = $request->get('q');

        $filter = [
            'title' => ['title', 'LIKE', '%' . $q . '%'],
        ];

        $limit = $request->get('limit') ?? QuestionService::DEFAULT_LIMIT;

//        todo sort
//        submit on btn

        return [$filter, ['id', 'desc'], $limit];
    }
}
