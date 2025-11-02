<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_categories_empty(): void
    {
        $response = $this->get('/categories');

        $response->assertStatus(200);
        $response->assertSee(__('Нэт категорий'));
    }
}
