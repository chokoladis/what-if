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
            <div class="filter-items">
                @if($tags)
                    <div class="tags">
                        <a class="btn btn-outline-secondary" data-bs-toggle="collapse"
                           href="#collapseTags" role="button" aria-expanded="false" aria-controls="collapseTags">
                            {{ '# '.__('crud.questions.fields.tag') }}
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

                @if($categories)
                    <div class="categories">
                        <a class="btn btn-outline-secondary icon-link" data-bs-toggle="collapse"
                           href="#collapseCategories" role="button" aria-expanded="false" aria-controls="collapseCategories">
                            <span uk-icon="icon: settings; ratio:0.8"></span>
                            {{ __('categories.categories') }}
                        </a>
                        <div class="collapse @if(request('categories')) show @endif" id="collapseCategories">
                            <div class="card card-body checkboxes">
                                @foreach($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $category->title }}"
                                               name="categories[]" id="{{ $category->title }}"
                                               @if(request('categories') && in_array($category->title, request('categories'))) checked @endif>
                                        <label class="form-check-label" for="{{ $category->title }}">{{ $category->title }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="resolved">
                    <a class="btn btn-outline-secondary" data-bs-toggle="collapse"
                       href="#collapseResolve" role="button" aria-expanded="false" aria-controls="collapseResolve">
                        <span uk-icon="icon: settings; ratio:0.8"></span>
                        {{ __('questions.resolved') }}
                    </a>
                    <div class="collapse @if(request('resolve')) show @endif" id="collapseResolve">
                        <div class="card card-body checkboxes">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ __('questions.resolved') }}"
                                       name="resolved" id="resolved"
                                       @if(request('resolved')) checked @endif>
                                <label class="form-check-label" for="resolved">{{ __('questions.resolved') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sort">
                    @php
                        $sorts = \App\Services\QuestionService::SORTS;
                        $currentSort = request('sort') ? request('sort') : array_key_first($sorts);
                    @endphp
                    <select class="form-select form-select" name="sort">
                        @foreach($sorts as $key => $sortName)
                            <option value="{{ $key }}" @if($currentSort === $key) selected @endif>{{ __('system.sort.'.$sortName) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="btns hstack gap-2 col-md-5">
                <button class="btn btn-primary">{{ __('btn.apply') }}</button>
                <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">{{ __('btn.reset') }}</a>
            </div>
        </form>

        @if(!$questions->isEmpty())
            <br>
            <div class="search-info">
                {{ __('Всего найдено - ').$questions->total() }}
            </div>
            <div class="items">
                @foreach ($questions as $question)
                    @php
                        $res = \App\Models\QuestionVotes::GetById((int)$question->id);
                        $mainClass = $question->right_comment_id ? 'border-success' : '';
                    @endphp

                    <div class="item card mb-3 {{ $mainClass }} ">
                        <div class="row g-0">
                            <a href="{{ route('questions.detail', $question->code) }}" class="img-col col-sm-4 col-md-3">
                                <img src="{{ \App\Services\FileService::getPhoto($question->file, 'questions/') }}" alt=""
                                     class="img-fluid rounded-start">
                            </a>
                            <div class="col-sm-8 col-md-9">
                                <div class="card-body">
                                    <div class="votes">
                                        <div class="icon like btn btn-success">
                                            <b>{{ $res->likes ?? 0 }}</b>
                                        </div>
                                        <div class="icon dislike btn btn-danger">
                                            <b>{{ $res->dislikes ?? 0 }}</b>
                                        </div>
                                    </div>
                                    <a href="{{ route('questions.detail', $question->code) }}" class="card-title">{{ $question->title }}</a>

                                    <div class="category">
                                        @if($question->category)
                                            <a href="{{ route('categories.detail', $question->category->code) }}" class="title">
                                                <i uk-icon="folder"></i>
                                                <span>{{ $question->category?->title }}</span>
                                            </a>
                                        @endif
                                        @if($question->tags)
                                            <div class="tags">
                                                @foreach($question->tags as $tag)
                                                    <a href="{{ route('questions.index', [ 'tags[]' => $tag->name ]) }}" class="link-success link-underline-opacity-25">{{ '#'.$tag->name }}</a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    @if ($question->right_comment_id)
                                        <x-right-answer :comment="$question->right_comment"></x-right-answer>
                                    @endif
                                    @if ($question->getPopularComment())
                                        <x-comment.popular-comment
                                                :comment="$question->getPopularComment()"></x-comment.popular-comment>
                                    @endif

                                    <div class="date">
                                        @if($question->statistics)
                                            <div class="views">
                                                <i uk-icon="eye"></i>
                                                <span>{{ $question->statistics->views }}</span>
                                            </div>
                                        @endif
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
                        </div>
                    </div>
                @endforeach
            </div>

            {{ $questions->links() }}
        @else
            <p>{{ __('questions.not_found') }}</p>
        @endif
    </div>
@endsection