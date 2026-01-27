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

    $isActiveQuestions = request()->get('page');
@endphp
@section('content')
    <div class="profile-page container">
        <div class="main row">
            <div class="card photo col-lg-4 col-md-3 col-12">
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
            <div class="preview col-lg-8 col-md-9 col-12 mt-sm-5 mt-md-0">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $isActiveQuestions ? '' : 'active' }}" id="home-tab"
                                data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                                type="button" role="tab" aria-controls="home-tab-pane" aria-selected="{{ $isActiveQuestions ? 'true' : 'false' }}">
                            {{ __('Профиль') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $isActiveQuestions ? 'active' : '' }}" id="my-questions-tab"
                                data-bs-toggle="tab" data-bs-target="#my-questions-tab-pane"
                                type="button" role="tab" aria-controls="my-questions-tab-pane" aria-selected="{{ $isActiveQuestions ? 'true' : 'false' }}">
                            {{ __('Мои вопросы') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab"
                                data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                                type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
                            {{ __('Избранные теги') }}</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade {{ $isActiveQuestions ? '' : 'show active' }}" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                        <div class="content-padding">

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
                    </div>
                    <div class="tab-pane fade {{ $isActiveQuestions ? 'show active' : '' }}" id="my-questions-tab-pane" role="tabpanel" aria-labelledby="my-questions-tab" tabindex="0">
                        <div class="content-padding questions-table">
                            {{-- кнопки редактировать (текст, картинку), деактивировать, удалить --}}
                            @php
                                $questions = $user->getQuestionsWithPages(request());
                            @endphp
                            @if($questions && !$questions->isEmpty())
                                <table class="table table-dark table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Заголовок</th>
                                            <th scope="col">Категория</th>
                                            <th scope="col">Превью</th>
                                            <th scope="col">Теги</th>
                                            <th scope="col">Дата создания</th>
                                            <th scope="col">Дата изменения</th>
                                            <th scope="col">Лайки/дизлайки</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questions as $question)
                                            @php
                                                $votes = \App\Services\QuestionService::getVotes($question->id);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <b>{{ $question->title }}</b>
                                                </td>
                                                <td>
                                                    <i>{{ $question->category?->title ?? '' }}</i>
                                                </td>
                                                <td>
                                                    <img src="{{ \App\Services\FileService::getPhoto($question->file, 'questions') }}" alt="" width="200px">
                                                </td>
                                                <td>
                                                    <div class="tags">
                                                        @if(!$question->tags->isEmpty())
                                                            @foreach($question->tags as $tag)
                                                                <span>{{ '#'.$tag->name }}</span>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <i>{{ $question->created_at }}</i>
                                                </td>
                                                <td>
                                                    <i>{{ $question->updated_at }}</i>
                                                </td>
                                                <td>
                                                    <i>{{ $votes['likes'] ?? 0 }} / {{ $votes['dislikes'] ?? 0 }}</i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $questions->links() }}
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                        <div class="content-padding">
                            <form action="{{ route('profile.tags.set') }}" method="POST" enctype="multipart/form-data" class="user-tags">

                                @csrf

                                <div class="user-tags-items">
                                    {{--todo cтили под кнопки в линию--}}
                                    @if(!$userTags->isEmpty())
                                        @foreach($userTags as $tag)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="tags[]" value="{{$tag->id}}" id="tag-{{$tag->id}}" checked>
                                                <label class="form-check-label" for="tag-{{$tag->id}}">
                                                    {{ '#'.$tag->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif

                                    @foreach($tags as $tag)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tags[]" value="{{$tag->id}}" id="tag-{{$tag->id}}">
                                            <label class="form-check-label" for="tag-{{$tag->id}}">
                                                {{ '#'.$tag->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="submit" class="btn btn-outline-primary">{{ __('Применить') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection