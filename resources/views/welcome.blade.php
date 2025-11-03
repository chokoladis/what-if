@extends('layouts.app')

@push('style')
    @vite(['resources/css/slick.css', 'resources/css/slick-theme.css'])
@endpush

@section('content')
    <div class="container">
        {{-- component - banner-slider --}}
        <x-main-slider/>
        <div id="banner" class="banner banner-centred banner-1">
            <img src="/storage/banner_1.png" alt="" class="bg">
            <h1 class="title">{{ __('system.banner.title') }}</h1>
            <p>{{ __('system.banner.subtitle') }}</p>
            <a href="{{ route('questions.add') }}" class="btn btn-purple">{{ __('system.banner.btn') }}</a>
        </div>
    </div>
@endsection