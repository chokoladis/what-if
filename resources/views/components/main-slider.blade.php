@php
    use App\Models\Question;use App\Services\FileService;

    $slides = \App\Services\QuestionService::getPopular();
@endphp
@push('style')
    @vite(['resources/scss/components/slider.scss'])
@endpush
@push('script')
    @vite(['resources/js/slick.min.js', 'resources/js/components/slider.js'])
@endpush

<div class="main_slider">
    @foreach ($slides as $slide)
        <div class="slide">
            @php
                //                $fileRawData = FileService::getFromRedis($slide->file, 'questions');
                //
                //                if ($fileRawData) {
                //                    $fileDataBase64 = sprintf('data:%s;base64,%s', 'image/'.$slide?->file?->expansion ?? 'jpg', base64_encode($fileRawData));
                //                } else {
                                    $fileSrc = FileService::getPhoto($slide->file, 'questions');
                //                }
            @endphp
            <a href="{{ route('questions.detail', $slide->code) }}" class="card">
                <div class="bg">
                    <img src="{{ $fileSrc }}" alt=""
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
                            <img src="{{ FileService::getPhoto($slide->right_comment->user->photo, 'users') }}" alt="">
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