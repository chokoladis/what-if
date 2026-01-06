@php
    use App\Services\FileService;
    use Carbon\Carbon;
@endphp
@extends('layouts.app')

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit-icons.min.js"></script>
@endpush

@php
    $collectionCategory = $categories['hits'];
    $collectionQuestion = current($questions->getCollection())['hits'];
@endphp
@section('content')
    <div class="search-page container">
        <div class="header">
            <h1>{{ __('system.search.title') }}</h1>

            <form action="{{ route('search.index') }}" method="GET" class="d-flex" id="search-page-form">
                {{--                todo liwewire--}}
                <input type="hidden" name="query" value="{{ request('q') }}">
                <div>
                    <select class="form-select form-select-sm" name="sort">
                        <option value="id,desc" selected>{{ __('system.sort.new') }}</option>
                        <option value="id,asc">{{ __('system.sort.old') }}</option>
                        <option value="popular">{{ __('system.sort.popular') }}</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-outline-info btn-sm">
                        <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"
                             class="DocSearch-Search-Icon">
                            <path d="M14.386 14.386l4.0877 4.0877-4.0877-4.0877c-2.9418 2.9419-7.7115 2.9419-10.6533 0-2.9419-2.9418-2.9419-7.7115 0-10.6533 2.9418-2.9419 7.7115-2.9419 10.6533 0 2.9419 2.9418 2.9419 7.7115 0 10.6533z"
                                  stroke="currentColor" fill="none" fill-rule="evenodd" stroke-linecap="round"
                                  stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        @if(!$categories['nbHits'] && !$questions->total())
            <div class="title error">
                <div class="description">
                    <img src="{{ Storage::url('404.gif') }}">
                    <h3 class="text-danger mt-2 text-center">{{ __('system.search.not_found') }}</h3>
                </div>
            </div>
        @endif

        @if($categories['nbHits'])
            @foreach($collectionCategory as $category)
                {{--todo slider--}}
                <button type="button" class="btn btn-primary">
                    {{ $category['title'] }} <span
                            class="badge text-bg-secondary">{{ $category['count_question'] }}</span>
                </button>
            @endforeach
        @endif

        @foreach ($collectionQuestion as $question)

            @php
                $mainClass = !empty($question['right_comment']) ? 'border-success' : '';
            @endphp

            <div class="item card mb-3 {{ $mainClass }} ">
                <a href="{{ route('questions.detail', $question['code']) }}" class="row g-0">
                    <div class="img-col col-sm-4 col-md-3">
                        <img src="{{ FileService::getPhotoFromIndex($question['file'], 'questions/') }}" alt=""
                             class="img-fluid rounded-start">
                    </div>
                    <div class="col-sm-8 col-md-9">
                        <div class="card-body">
                            <h4 class="card-title">{{ $question['title'] }}</h4>

                            @if (!empty($question['right_comment']))
                                <x-right-answer :comment="$question['right_comment']"></x-right-answer>
                            @endif
                            @if (!empty($question['popular_comment']))
                                <x-comment.popular-comment
                                        :comment="$question['popular_comment']"></x-comment.popular-comment>
                            @endif

                            <div class="date">
                                <p class="card-text">
                                    <i uk-icon="calendar"></i>
                                    <small class="text-body-secondary">{{
                                        Carbon::parse($question['created_at'])->diffForHumans()
                                        }}</small>
                                </p>
                                @if ($question['created_at'] != $question['updated_at'])
                                    <p class="card-text">
                                        <i uk-icon="pencil"></i>
                                        <small class="text-body-secondary">{{
                                            Carbon::parse($question['updated_at'])->diffForHumans()
                                            }}</small>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            {{ $questions->links() }}
            {{--            total --}}
        @endforeach

        @if ($questions->isEmpty())
            <p>{{ __('questions.not_found') }}</p>
        @endif
    </div>
@endsection