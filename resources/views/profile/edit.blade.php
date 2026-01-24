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
                        <form action="{{ route('profile.update') }}" class="info" method="POST" enctype="multipart/form-data">

                            @csrf

                            <div class="mb-3">
                                <label class="form-label">{{ __('crud.users.fields.email') }}</label>
                                <input type="text" name="email" class="form-control" value="{{$user->email}}">
                                @if ($errors->has('email'))
                                    @foreach ($errors->get('email') as $item)
                                        <p class="error">{{ $item  }}</p>
                                    @endforeach
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('crud.users.fields.name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{$user->name}}">
                                @if ($errors->has('name'))
                                    @foreach ($errors->get('name') as $item)
                                        <p class="error">{{ $item  }}</p>
                                    @endforeach
                                @endif
                            </div>

                            <button type="submit" class="btn btn-outline-success mb-3">{{ __('btn.change') }}</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                        <form action="{{ route('profile.tags') }}" class="info" method="POST" enctype="multipart/form-data">

                            @csrf

                            <div class="mb-3">
                                <label class="form-label">{{ __('crud.users.fields.email') }}</label>
                                <input type="text" name="email" class="form-control" value="{{$user->email}}">
                                @if ($errors->has('email'))
                                    @foreach ($errors->get('email') as $item)
                                        <p class="error">{{ $item  }}</p>
                                    @endforeach
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('crud.users.fields.name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{$user->name}}">
                                @if ($errors->has('name'))
                                    @foreach ($errors->get('name') as $item)
                                        <p class="error">{{ $item  }}</p>
                                    @endforeach
                                @endif
                            </div>

                            <button type="submit" class="btn btn-outline-success mb-3">{{ __('btn.change') }}</button>
                        </form>
                        @if(!$userTags->isEmpty())
                            @foreach($userTags as $tag)

                            @endforeach
                        @endif
                        <a class="btn btn-outline-primary">{{ __('Добавить теги') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection