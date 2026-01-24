@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('user.login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-end">{{ __('user.auth.email') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-end">{{ __('user.auth.password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember"
                                               id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('user.auth.remember') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{--                                    todo --}}
                                        {{ __('btn.login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('user.forgot_password') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @php
                                $gData = array(
                                    'client_id'     => config('auth.socials.google.client_id'),
                                    'redirect_uri'  => config('auth.socials.google.redirect_uri'),
                                    'response_type' => 'code',
                                    'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                                );

                                $gUrl = 'https://accounts.google.com/o/oauth2/auth?' . urldecode(http_build_query($gData));

                                $yData = array(
                                    'client_id'     => config('auth.socials.yandex.client_id'),
                                    'redirect_uri'  => config('auth.socials.yandex.redirect_uri'),
                                    'response_type' => 'code',
                                );

                                $yUrl = 'https://oauth.yandex.ru/authorize?' . urldecode(http_build_query($yData));
                            @endphp

                            <div class="row services_auth">
                                <p class="col-md-4">{{ __('user.auth_throw_service')}}</p>
                                <div class="col-md-6 services">
                                    <a href="{{ $gUrl }}">
                                        <img src="/storage/main/google_icon_min.png" alt="google">
                                    </a>
                                    <a href="{{ $yUrl }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none"
                                             viewBox="0 0 26 26">
                                            <path fill="#FC3F1D"
                                                  d="M26 13c0-7.18-5.82-13-13-13S0 5.82 0 13s5.82 13 13 13 13-5.82 13-13Z"></path>
                                            <path fill="#fff"
                                                  d="M17.55 20.822h-2.622V7.28h-1.321c-2.254 0-3.38 1.127-3.38 2.817 0 1.885.758 2.816 2.448 3.943l1.322.932-3.749 5.828H7.237l3.575-5.265c-2.059-1.495-3.185-2.817-3.185-5.265 0-3.012 2.058-5.07 6.023-5.07h3.9v15.622Z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
