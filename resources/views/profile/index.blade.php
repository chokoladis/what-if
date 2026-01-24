@extends('layouts.app')

@push('style')
    @vite(['resources/scss/profile.scss'])
@endpush

@push('script')
    @vite(['resources/js/profile.js'])
@endpush

@php
    $user = auth()->user();
    $photo = $user->photo;
@endphp
@section('content')
    <div class="profile-page container">
        <div class="main">
            <div class="card photo">
                <img src="{{ $photo ? Storage::url('users/'.$photo->path) : $SITE_NOPHOTO }}" class="card-img-top">
                <div class="card-body @if($errors->has('photo')) active @endif">
                    <form action="{{ route('profile.setPhoto') }}" class="update-photo" method="POST"
                          enctype="multipart/form-data">

                        @csrf

                        <input type="file" name="photo" class="btn btn-primary" value="{{ __('user.change_photo') }}">
                        @if ($errors->has('photo'))
                            @foreach ($errors->get('photo') as $item)
                                <p class="error">{{ $item }}</p>
                            @endforeach
                        @endif

                        <button type="submit" class="btn btn-success">{{ __('user.change_photo') }}</button>
                    </form>
                </div>
            </div>
            <div class="preview">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab"
                                data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                                type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">
                            {{ __('Профиль') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab"
                                data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                                type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
                            {{ __('Избранные теги') }}</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                        <div class="mt-3">
                            <span class="text-info">{{ __('crud.users.fields.email') }}</span>
                            <h5>{{ $user->email  }}</h5>
                        </div>

                        <div class="mt-3">
                            <span class="text-info">{{ __('crud.users.fields.name') }}</span>
                            <h5>{{ $user->name }}</h5>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">Редактировать</a>
                    </div>
                    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                        @if(!$userTags->isEmpty())
                            @foreach($userTags as $tag)

                            @endforeach
                        @else
                            <h5 class="mt-3 text-warning">{{ __('Не найдено') }}</h5>
                        @endif
                        <a class="btn btn-outline-primary">{{ __('Добавить теги') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection