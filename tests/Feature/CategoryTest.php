<?php

namespace Feature;


use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    public function test_categories_page_category_exists(): void
    {
        Category::factory()->create();

        $response = $this->get('/categories');

        $response->assertOk()->assertDontSee(__('Нэт категорий'));
    }
}
