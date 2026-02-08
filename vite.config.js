import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/scss/categories.scss',
                'resources/scss/questions.scss',
                'resources/scss/profile.scss',
                'resources/scss/components/question/slider-popular.scss',
                'resources/scss/components/item.scss',
                'resources/scss/components/popular-comment.scss',
                'resources/scss/components/right-answer.scss',
                'resources/scss/components/slider.scss',
                'resources/js/app.js',
                'resources/js/profile.js',
                'resources/js/question.js',
                'resources/js/components/slider.js',
            ],
            refresh: true,
        }),
    ],
});
