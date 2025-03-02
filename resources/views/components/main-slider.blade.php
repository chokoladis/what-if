@php
    use App\Models\Question;

    $slides = Question::getTopPopular();
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
            <a href="{{ route('questions.detail', $slide->code) }}">
                <div class="bg">
                    @php
                        $path = $slide->file ? Storage::url('questions/'.$slide->file->path) : Storage::url('main/nophoto.jpg');
                    @endphp
                    <img src="{{ $path }}" alt="" class="img-fluid rounded-start">
                </div>
                <div class="main">
                    <div class="user">
                        <img src="{{ $slide->user->profile_photo_path ? Storage::url($slide->user->profile_photo_path) : $SITE_NOPHOTO }}"
                             alt="Фото пользователя не найдено">
                        <p>{{ $slide->user->name }}</p>
                    </div>
                    <blockquote>{{ mb_strlen($slide->title) > 60 ? mb_substr($slide->title, 0, 60) : $slide->title }}</blockquote>
                    @if ($slide->right_comment_id)
                        <div class="answer">
                            <div class="user">
                                <img src="{{ __('empty') }}" alt="">
                                <p>{{ $slide->right_comment->user->name }}</p>
                            </div>
                            <b>{{ mb_strlen($slide->right_comment->text) > 60 ? mb_substr($slide->right_comment->text, 0, 60) : $slide->right_comment->text }}</b>
                        </div>
                    @endif
                </div>
            </a>
        </div>
    @endforeach
</div>