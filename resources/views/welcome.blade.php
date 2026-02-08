@extends('layouts.app')

@push('style')
    @vite(['resources/scss/components/slider.scss'])
@endpush

@section('content')
    <div class="container">
        <x-question.slider-popular/>
        <div id="banner" class="banner banner-centred banner-1">
            <img src="/storage/banner_1.png" alt="" class="bg">
            <h1 class="title">{{ __('system.banner.title') }}</h1>
            <p>{{ __('system.banner.subtitle') }}</p>
            <a href="{{ route('questions.add') }}" class="btn btn-purple">{{ __('system.banner.btn') }}</a>
        </div>
    </div>
@endsection