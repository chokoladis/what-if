@extends('layouts.app')

@push('style')
    @vite(['resources/scss/questions.scss'])
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit-icons.min.js"></script>
@endpush

@php
    $sorts = \App\Services\QuestionService::SORTS;
    $currentSort = request('sort') ? request('sort') : array_key_first($sorts);

    $itemsTypeOut = \App\Services\QuestionService::ITEMS_TYPE_OUTPUT;
    $itemsTypeOutCookie = \Illuminate\Support\Facades\Cookie::get('items-type-output', 'simple');
    $currentItemsTypeOutput = in_array($itemsTypeOutCookie, $itemsTypeOut) ? $itemsTypeOutCookie : current($itemsTypeOut);
@endphp

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
            </div>

            <div class="btns hstack gap-2 col-md-5">
                <button class="btn btn-primary">{{ __('btn.apply') }}</button>
                <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">{{ __('btn.reset') }}</a>
            </div>
        </form>

        @if(!empty($questions) && !$questions->isEmpty())
            <div class="header-search">
                <b class="total">{{ __('questions.total_found').$questions->total() }}</b>
                <div class="sort">
                    {{--todo icons--}}
                    <select class="form-select form-select" name="sort">
                        @foreach($sorts as $key => $sortName)
                            <option value="{{ $key }}" @if($currentSort === $key) selected @endif>{{ __('system.sort.'.$sortName) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="items-type-output btn-group" role="group" aria-label="Переключение типа вывода элементов">
                    @foreach($itemsTypeOut as $type)
                        <input type="radio" class="btn-check" name="items-type-output" id="{{ $type }}" autocomplete="off"
                               @if($currentItemsTypeOutput === $type) checked @endif>
                        <label class="btn btn-outline-primary" for="{{ $type }}">
                            {!! __('system.items_type_out.'.$type) !!}</label>
                    @endforeach
                </div>
            </div>
            <div class="items">
                @foreach ($questions as $question)
                    <x-item :question="$question"></x-item>
                @endforeach
            </div>

            {{ $questions->links() }}
        @else
            <p>{{ __('questions.not_found') }}</p>
        @endif
    </div>
@endsection