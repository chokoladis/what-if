<?php
declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\Question\IndexRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class QuestionIndexService
{
    private const QUESTIONS_INDEX_CACHE_PREFIX = 'questions_index_v1_';

    private const QUESTIONS_INDEX_TTL_SECONDS = 3600;

    private TagRepository $tagRepository;

    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->tagRepository = new TagRepository();
        $this->categoryRepository = new CategoryRepository(Category::class, true);
    }

    /**
     * Полный набор данных для страницы списка вопросов: фильтры + пагинация (кеш списка отдельно).
     */
    public function getIndexPageData(IndexRequest $request): array
    {
        $sidebar = $this->getSidebarFilterData();

        return array_merge($sidebar, [
            'questions' => $this->getCachedPaginatedQuestions($request),
        ]);
    }

    /**
     * Теги и категории для фильтров (кешируются в репозиториях отдельно).
     */
    public function getSidebarFilterData(): array
    {
        return [
            'tags' => $this->tagRepository->getAll(),
            'categories' => $this->categoryRepository->getActive(),
        ];
    }

    /**
     * Кеш только результата пагинации по валидированным параметрам и номеру страницы.
     */
    private function getCachedPaginatedQuestions(IndexRequest $request): LengthAwarePaginator
    {
        $validated = $request->validated();
        $page = (int)($request->input('page', 1));

        $cacheKey = self::QUESTIONS_INDEX_CACHE_PREFIX . md5(
                json_encode($validated, JSON_THROW_ON_ERROR) . '|page|' . $page
            );

        return Cache::remember($cacheKey, self::QUESTIONS_INDEX_TTL_SECONDS, function () use ($request) {
            return QuestionService::paginateWithFilter($request);
        });
    }
}
