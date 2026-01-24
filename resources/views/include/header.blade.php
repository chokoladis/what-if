<!doctype html>
@php
    $lang = \Illuminate\Support\Facades\Cookie::get('lang') ?? app()->getLocale();
    \Illuminate\Support\Facades\App::setLocale($lang);

    $flagSrc = \Illuminate\Support\Facades\Storage::url('main/flag-'.$lang.'.svg');
    $theme = \Illuminate\Support\Facades\Cookie::get('theme', 'dark');
@endphp
<html lang="{{ str_replace('_', '-', $lang) }}" data-bs-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ str_replace('_', ' ', config('app.name', 'Laravel')) }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/scss/app.scss'])
    @stack('style')

    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
</head>
<body>
<div id="app">
    <nav class="header navbar navbar-expand-md shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.logo') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('questions.index') }}">{{ __('menu.main.questions') }}</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false"
                        >{{ __('menu.main.categories') }}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('categories.index') }}">Все категории</a></li>
                            <li><hr class="dropdown-divider"></li>
                            {{--todo все остальные с подразделами--}}
                            @foreach(\App\Models\Category::getCategoriesLevel0() as $category)
                                @if(!$category->subcategories->isEmpty())
                                    {{--todo recurse--}}
                                    <li class="nav-item sub-dropdown">
                                        <a class="nav-link dropdown-item dropdown-toggle" href="#"
                                           role="button" data-bs-toggle="sub-dropdown" aria-expanded="false"
                                        >{{ $category->title }}</a>
                                        <ul class="dropdown-menu">
                                            @foreach($category->subcategories as $subcategory)
                                                <li><a class="dropdown-item" href="{{ route('categories.detail', $subcategory->code) }}">{{ $subcategory->title }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('categories.detail', $category->code) }}">{{ $category->title }}</a></li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-link" href="{{ route('questions.add') }}">{{ __('menu.main.ask') }}</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto search">
                    <form action="{{ route('search.index') }}" method="GET" class="d-flex">
                        <li>
                            <input class="form-control form-control-sm" type="search" name="q"
                                   placeholder="{{ __('menu.search')  }}"
                                   autocomplete="on" value="{{ request('q') }}">
                        </li>
                        <li>
                            <button type="button" class="btn btn-outline-info btn-sm">
                                <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"
                                     class="DocSearch-Search-Icon">
                                    <path d="M14.386 14.386l4.0877 4.0877-4.0877-4.0877c-2.9418 2.9419-7.7115 2.9419-10.6533 0-2.9419-2.9418-2.9419-7.7115 0-10.6533 2.9418-2.9419 7.7115-2.9419 10.6533 0 2.9419 2.9418 2.9419 7.7115 0 10.6533z"
                                          stroke="currentColor" fill="none" fill-rule="evenodd" stroke-linecap="round"
                                          stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </li>
                    </form>
                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('menu.main.login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('menu.main.register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile.index') }}">
                                    {{ __('menu.main.profile') }}
                                </a>
                                @can('isAdmin')
                                    <a class="dropdown-item" href="{{ route('filament.admin.pages.dashboard') }}">
                                        {{ __('menu.main.dashboard') }}
                                    </a>
                                @endcan
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                    {{ __('menu.main.logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                    <li class="nav-item">
                        <div class="js-change-theme" data-theme="{{ $theme }}">
                            <div class="img">
                                <img src="{{ Storage::url('main/moon.png') }}" alt="">
                                <img src="{{ Storage::url('main/sun.png') }}" alt="">
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown js-change-lang">
                        <a id="langDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ $flagSrc }}" alt="">
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <a class="dropdown-item" data-lang="ru">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url('main/flag-ru.svg') }}"
                                     alt="{{ __('flag-ru') }}" title="{{ __('ru') }}">
                            </a>
                            <a class="dropdown-item" data-lang="en">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url('main/flag-en.svg') }}"
                                     alt="{{ __('flag-en') }}" title="{{ __('en') }}">
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>