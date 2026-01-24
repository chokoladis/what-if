@php
    use App\Services\FileService;
@endphp
@push('style')
    @vite(['resources/scss/components/question/slider-popular.scss'])
@endpush
@push('script')
    @vite(['resources/js/components/slider.js'])
@endpush

<div class="slider-popular">
    @foreach ($slides as $slide)
        <div class="slide">
            <a href="{{ route('questions.detail', $slide->code) }}" class="card">
                <div class="bg">
                    <img src="{{ FileService::getPhoto($slide->file, 'questions') }}" alt=""
                         class="img-fluid rounded-start">
                </div>
                <div class="main">
                    <div class="question">
                        <img src="{{ FileService::getPhoto($slide->user->photo, 'users') }}" alt="Фото пользователя">
                        <div class="content">
                            <p class="user-name">{{ '@'.$slide->user->name }}</p>
                            <blockquote
                                    class="text">{{ mb_strlen($slide->title) > 60 ? mb_substr($slide->title, 0, 60) : $slide->title }}</blockquote>
                        </div>
                    </div>
                    @if ($slide->right_comment_id)
                        <div class="answer alert alert-success">
                            <div class="content">
                                <p class="user-name">{{ '@'.$slide->right_comment->user->name }}</p>
                                <b class="comment-text">{{ mb_strlen($slide->right_comment->text) > 60 ? mb_substr($slide->right_comment->text, 0, 60) : $slide->right_comment->text }}</b>
                            </div>
                        </div>
                    @endif
                </div>
            </a>
        </div>
    @endforeach
</div>