<?php

namespace Feature;


use App\Models\Category;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    public function test_listing_page_is_empty(): void
    {
        Category::factory()->create([
            'active' => false
        ]);

        $response = $this->get('/categories');

        /** @var Collection $categories */
        $categories = $response->viewData('categories');

        if ($categories->isEmpty()) {
            $response->assertViewIs('errors.404');
        } else {
            $response->assertViewIs('categories.index');
        }
    }

    public function test_listing_page_not_empty(): void
    {
        Category::factory()->create();

        $response = $this->get('/categories');

        $response->assertViewIs('categories.index');
    }
}
