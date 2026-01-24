@extends('layouts.app')

@push('style')
    @vite(['resources/scss/questions.scss'])
@endpush
@push('script')
    @vite(['resources/js/question.js'])
@endpush

@php
    foreach ($categories as $level => $categoryArr) {

        $line = str_repeat('-', $level);
        // $styleBg = 'background: rgba(0,0,0, '.($level * 0.1) .')';

        foreach ($categoryArr as $id => $arr) {

            $main = $arr['category'];
            $childs = $arr['items'];

            $selected = old('category') == $main['id'] ? 'selected' : '';

            $html = '<option value="'.$main['code'].'" '.$selected.'>'.$line.$main['title'].'</option>';

            if (!empty($childs)){
                foreach ($childs as $child) {
                    
                    $styleBg = 'background: rgba(0,0,0, '.$child['level'] * 0.1 .')';

                    if (isset($categories[$child['level']][$child['id']]['html'])){
                        $html .= $categories[$child['level']][$child['id']]['html'];
                    } else {
                        $selected .= old('category') == $child['id'] ? 'selected' : $selected;
                        $html = '<option value="'.$child['code'].'" '.$selected.'>'.$line.$child['title'].'</option>';
                    }
                }
            }

            $categories[$level][$main->id]['html'] = $html;
        }
    }
@endphp
@section('content')
    <div class="question-page-create container">
        <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data">

            <h1>Мой вопрос - <b>...</b></h1>

            @csrf

            <div class="mt-5 mb-3">
                <label class="form-label">{{ __('crud.questions.fields.category') }}</label>
                <select name="category" class="form-select">
                    <option value="0" selected>{{ __('Без категории') }}</option>
                    @if (!empty($categories))
                        @foreach ($categories[0] as $arr)
                            {!! $arr['html'] !!}
                        @endforeach
                    @endif
                </select>
            </div>
            @if(!empty($tags))
                <div class="tags mb-3">
                    <label class="form-label">{{ __('crud.questions.fields.tag') }}</label>
                    <div class="checkboxes">
                        @foreach($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $tag->name }}" name="tags[]" id="{{ $tag->name }}">
                                <label class="form-check-label" for="{{ $tag->name }}">
                                    {{ $tag->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if ($errors->has('tags'))
                        @foreach ($errors->get('tags') as $item)
                            <p class="error">{{ $item  }}</p>
                        @endforeach
                    @endif
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">{{ __('crud.questions.fields.title') }}</label>
                <input type="text" name="title" class="form-control"
                       placeholder="{{ __('crud.questions.placeholders.title') }}" autocomplete="search">
                @if ($errors->has('title'))
                    @foreach ($errors->get('title') as $item)
                        <p class="error">{{ $item  }}</p>
                    @endforeach
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('crud.questions.fields.img') }}</label>
                <input type="file" name="img" class="form-control">
                @if ($errors->has('img'))
                    @foreach ($errors->get('img') as $item)
                        <p class="error">{{ $item  }}</p>
                    @endforeach
                @endif
            </div>

            @if(\App\Services\SettingService::isCaptchaSetOn()))
                <div class="h-captcha" data-sitekey="{{ config('services.h_captcha.sitekey') }}"></div>
            @endif

            <button type="submit" class="btn btn-primary mb-3">{{ __('btn.add') }}</button>

        </form>
    </div>
@endsection