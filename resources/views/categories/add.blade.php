@extends('layouts.app')

@php
    foreach ($categories as $level => $categoryArr) {

        $line = str_repeat('-', $level);
        // $styleBg = 'background: rgba(0,0,0, '.($level * 0.1) .')';

        foreach ($categoryArr as $id => $arr) {

            $main = $arr['category'];
            $childs = $arr['items'];

            $selected = old('parent_id') == $main['id'] ? 'selected' : '';

            $html = '<option value="'.$main['id'].'" '.$selected.'>'.$line.$main['title'].'</option>';

            if (!empty($childs)){
                foreach ($childs as $child) {
                    
                    $styleBg = 'background: rgba(0,0,0, '.$child['level'] * 0.1 .')';

                    if (isset($categories[$child['level']][$child['id']]['html'])){
                        $html .= $categories[$child['level']][$child['id']]['html'];
                    } else {
                        $selected .= old('parent_id') == $child['id'] ? 'selected' : $selected;
                        $html = '<option value="'.$child['id'].'" '.$selected.'>'.$line.$child['title'].'</option>';
                    }
                }
            }

            $categories[$level][$main->id]['html'] = $html;
        }
    }
@endphp

@section('content')
    <div class="container">
        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="mb-3">
                <label class="form-label">{{ __('crud.categories.fields.parent_id') }}</label>
                <select name="parent_id" class="form-select">
                    <option value="0" selected>{{ __('Без категории') }}</option>
                    @if (!empty($categories))
                        @foreach ($categories[0] as $arr)
                            {!! $arr['html'] !!}
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('crud.categories.fields.title') }}</label>
                <input type="text" name="title" class="form-control"
                       placeholder="Название, например: Аниме" value="{{ old('title') }}">
                @if ($errors->has('title'))
                    @foreach ($errors->get('title') as $item)
                        <p class="error">{{ $item  }}</p>
                    @endforeach
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('crud.categories.fields.img') }}</label>
                <input type="file" name="img" class="form-control">
                @if ($errors->has('img'))
                    @foreach ($errors->get('img') as $item)
                        <p class="error">{{ $item  }}</p>
                    @endforeach
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('crud.categories.fields.sort') }}</label>
                <input type="number" name="sort" class="form-control"
                       placeholder="100" value="{{ old('sort') }}">
                @if ($errors->has('sort'))
                    @foreach ($errors->get('sort') as $item)
                        <p class="error">{{ $item  }}</p>
                    @endforeach
                @endif
            </div>

            <button type="submit" class="btn btn-primary mb-3">{{ __('system.add') }}</button>

        </form>
    </div>
@endsection