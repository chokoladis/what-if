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

            {{--            фильтры - --}}
            {{--            решенные--}}
            {{--            по категории--}}
            {{--            теги--}}
            @php
                $mainClass = $question->right_comment_id ? 'border-success' : '';
            @endphp

            <div class="item card mb-3 {{ $mainClass }} ">
                <a href="{{ route('questions.detail', $question->code) }}" class="row g-0">
                    <div class="img-col col-sm-4 col-md-3">
                        <img src="{{ \App\Services\FileService::getPhoto($question->file, 'questions/') }}" alt=""
                             class="img-fluid rounded-start">
                    </div>
                    <div class="col-sm-8 col-md-9">
                        <div class="card-body">
                            <h4 class="card-title fw-bold">{{ $question->title }}</h4>

                            @if ($question->right_comment_id)
                                <x-right-answer :comment="$question->right_comment"></x-right-answer>
                            @endif
                            @if ($question->getPopularComment())
                                <x-comment.popular-comment
                                        :comment="$question->getPopularComment()"></x-comment.popular-comment>
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
            {{ $questions->links() }}
            {{--            links
                    total
            --}}
        @endforeach

        @if ($questions->isEmpty())
            <p>{{ __('questions.not_found') }}</p>
        @endif
    </div>
@endsection