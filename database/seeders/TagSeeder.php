<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collection = ['Аниме', 'Фильмы', 'Сериалы', 'Природа', 'Горы',
            'Компьютерный игры', 'Игры', 'Недвижимость', 'Инвестиции', 'Техника', 'Политика'
        ];

        foreach ($collection as $tag) {
            Tag::create([ 'name' => $tag ]);
        }
    }
}
