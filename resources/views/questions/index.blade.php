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

        <form class="filters" action="" method="GET">

            @if($tags)
                <div class="tags">
                    <a class="btn btn-secondary" data-bs-toggle="collapse"
                       href="#collapseTags" role="button" aria-expanded="false" aria-controls="collapseTags">
                        {{ __('crud.questions.fields.tag') }}
                    </a>
                    <div class="collapse @if(request('tags')) show @endif" id="collapseTags">
                        <div class="card card-body checkboxes">
                            @foreach($tags as $tag)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $tag->name }}"
                                           name="tags[]" id="{{ $tag->name }}"
                                           @if(request('tags') && in_array($tag->name, request('tags'))) checked @endif>
                                    <label class="form-check-label" for="{{ $tag->name }}">{{ $tag->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endif

            <button class="btn btn-primary">{{ __('btn.apply') }}</button>
        </form>

        <div class="items">
            @foreach ($questions as $question)

                {{--            фильтры - --}}
                {{--            решенные--}}
                {{--            по категории--}}
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
                {{--total--}}
            @endforeach
        </div>

        @if(!$questions->isEmpty())
            {{ $questions->links() }}
        @else
            <p>{{ __('questions.not_found') }}</p>
        @endif
    </div>
@endsection