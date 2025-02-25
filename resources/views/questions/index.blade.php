@extends('layouts.app')

@push('style')
    @vite(['resources/scss/questions.scss'])
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit-icons.min.js"></script>
@endpush

@section('content')
    <div class="index-page container">
        @foreach ($questions as $question)

            @php
                $mainClass = $question->right_comment_id ? 'border-success' : '';
            @endphp

            <div class="item card mb-3 {{ $mainClass }} ">
                <a href="{{ route('questions.detail', $question->code) }}" class="row g-0">
                    <div class="img-col col-sm-4 col-md-3">
                        <img src="{{ Storage::url('questions/'.$question->file?->path) }}" alt="" class="img-fluid rounded-start">
                    </div>
                    <div class="col-sm-8 col-md-9">
                        <div class="card-body">
                            <h4 class="card-title">{{ $question->title }}</h4>

                            @if ($question->right_comment_id)
{{--                                todo alert-success--}}
                                <div class="answer card-text">
                                    <div class="user">
                                        <img src="{{ __('empty') }}" alt="">
                                        <p>{{ $question?->answer?->user->name }}</p>
                                    </div>
                                    <b class="text-success">{{ mb_strlen($question?->answer->text) > 60 ? mb_substr($question->answer->text, 0, 60) : $question?->answer->text }}</b>
                                    <p class="card-text"><small class="text-body-secondary">{{ $question->answer->created_at->diffForHumans() }}</small></p>
                                </div>
                            @endif
                            @if ($popularComment = $question->getPopularComment())
                                <div class="popular-answer alert alert-warning" role="alert">
                                    <i uk-icon="bolt"></i>
                                    <div class="content">
                                        <h5>{{ $popularComment->user->name }}</h5>
                                        <p class="mb-0">{{ $popularComment->text }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <p class="card-text"><small class="text-body-secondary">{{ $question->created_at->diffForHumans() }}</small></p>
                            @if ($question->created_at != $question->updated_at)
                                <p class="card-text"><small class="text-body-secondary">{{ __('Изменен ') . $question->updated_at->diffForHumans() }}</small></p>
                            @endif
                            
                        </div>
                    </div>
                </a>
            </div>
        @endforeach

        @if ($questions->isEmpty())
            <p>Вопросов пока никто не задавал -_-</p>
        @endif
    </div>
@endsection