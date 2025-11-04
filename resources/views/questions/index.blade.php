@extends('layouts.app')

@push('style')
    @vite(['resources/scss/questions.scss'])
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit-icons.min.js"></script>
@endpush

@section('content')
    <div class="questions-page container">
        @foreach ($questions as $question)

            @php
                $mainClass = $question->right_comment_id ? 'border-success' : '';
            @endphp

            <div class="item card mb-3 {{ $mainClass }} ">
                <a href="{{ route('questions.detail', $question->code) }}" class="row g-0">
                    <div class="img-col col-sm-4 col-md-3">
                        <img src="{{ \App\Services\FileService::getPhoto($question->file, 'questions/') }}" alt="" class="img-fluid rounded-start">
                    </div>
                    <div class="col-sm-8 col-md-9">
                        <div class="card-body">
                            <h4 class="card-title">{{ $question->title }}</h4>

                            @if ($question->right_comment_id)
                                <div class="right-answer card-text alert alert-success">
                                    <i uk-icon="check"></i>
                                    <div class="content">
                                        <div class="user">
                                            <img src="{{ \App\Services\FileService::getPhoto($question->right_comment->user->file,'users') }}" alt="">
                                            <p>{{ $question->right_comment->user->name }}</p>
                                        </div>
                                        <b class="text-success">{{ mb_strlen($question->right_comment->text) > 60 ? mb_substr($question->right_comment->text, 0, 60) : $question->right_comment->text }}</b>
                                    </div>
                                </div>
                            @endif
                            @if ($popularComment = $question->getPopularComment() && !$question->right_comment_id)
                                <div class="popular-answer alert alert-warning" role="alert">
                                    <i uk-icon="bolt"></i>
                                    <div class="content">
                                        <div class="user">
                                            <img src="{{ \App\Services\FileService::getPhoto($popularComment->user->file, 'users') }}" alt="">
                                            <p>{{ $popularComment->user->name }}</p>
                                        </div>
                                        <p class="mb-0">{{ $popularComment->text }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="date">
                                <p class="card-text">
                                    <i uk-icon="question"></i>
                                    <small class="text-body-secondary">{{ $question->created_at->diffForHumans() }}</small>
                                </p>
                                @if ($question->created_at != $question->updated_at)
                                    <p class="card-text">
                                        <i uk-icon="pencil"></i>
                                        <small class="text-body-secondary">{{ $question->updated_at->diffForHumans() }}</small>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach

        @if ($questions->isEmpty())
            <p>{{ __('questions.not_found') }}</p>
        @endif
    </div>
@endsection