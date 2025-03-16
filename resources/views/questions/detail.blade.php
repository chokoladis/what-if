@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/css/uikit.min.css" />
    @vite(['resources/scss/questions.scss'])
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit-icons.min.js"></script>
    @vite(['resources/js/question.js'])
@endpush

@section('content')
    <div class="question-page container" data-question_id="{{ $question->id }}">
        @if($error)
            <div class="title error">
                <div class="description">
                    <img src="{{ Storage::url('404.gif') }}">
                    <div class="shadow"></div>
                    <h1>{{ $error }}</h1>
                </div>
            </div>
        @else
            <div class="title">
                <div class="question-actions">
                    @php
                        $currentReaction = !empty($questionUserStatus) ? $questionUserStatus['status'] : '';
                    @endphp
                    @if(auth()->id())
{{--                        todo boostrap icons ?--}}
                        <input type="hidden" id="question_id" value="{{ $question->id }}">
                        <div class="icon like btn {{ $currentReaction === 'like' ? 'btn-success' : 'btn-outline-success' }}" data-action="like">
                            <span class="uk-icon" uk-icon="chevron-up"></span>
                            <b>{{ $arStatuses['likes'] ?? 0 }}</b>
                        </div>
                        <div class="icon dislike btn {{ $currentReaction === 'dislike' ? 'btn-danger' : 'btn-outline-danger' }}" data-action="dislike">
                            <span class="uk-icon" uk-icon="chevron-down"></span>
                            <b>{{ $arStatuses['dislikes'] ?? 0 }}</b>
                        </div>
                    @else
                        <div class="icon like btn btn-success">
                            <span class="uk-icon" uk-icon="chevron-up"></span>
                            <b>{{ $arStatuses['likes'] ?? 0 }}</b>
                        </div>
                        <div class="icon dislike btn btn-danger">
                            <span class="uk-icon" uk-icon="chevron-down"></span>
                            <b>{{ $arStatuses['dislikes'] ?? 0 }}</b>
                        </div>
                    @endif
{{--                    текущий юзер -статус --}}

                </div>
                <div class="description">
                    <img src="{{ $question->file && $question->file->path ? Storage::url('questions/'.$question->file->path) : $SITE_NOPHOTO }}"
                         alt="...">
                    {{--во весь экран --}}
                    <div class="shadow"></div>
                    <h1>{{ $question->title }}</h1>
                </div>
            </div>
            <div class="info">
                <div class="author">
                    <span uk-icon="microphone"></span>
                    <i>{{ '@'.$question->user->name }}</i>
                </div>
                <div class="date">
                    <div class="create">
                        <span uk-icon="calendar"></span>
                        <i>{{ $question->created_at }}</i>
                    </div>

                    @if($question->created_at != $question->updated_at)
                        <div class="update">
                            <span uk-icon="pencil"></span>
                            <i>{{ $question->updated_at }}</i>
                        </div>
                    @endif
                </div>
                <div class="views">
                    <span uk-icon="eye"></span>
                    <i>{{ $question->statistics->views }}</i>
                </div>
            </div>
            <div class="comments">
                @if($arComments)
                    @foreach ($arComments as $arComment)
                        @php
                            $comment = $arComment['comment'];
                            $countChilds = $arComment['count_childs'];

                            $isRight = $question->right_comment_id === $comment->id;

                            $text = mb_strlen($comment->text) > 60 ? mb_substr($comment->text, 0, 60) : $comment->text;
                        @endphp
                        <div class="comment {{ $isRight ? 'is-answer' : '' }}" data-comment-id="{{ $comment->id }}">

                            <x-comment.rating :comment="$comment"></x-comment.rating>

                            <div class="main">
                                <div class="right_comment_description {{ !$isRight ? 'd-none' : '' }}">
                                    <i uk-icon="icon: check; ratio: 1.2"></i>
                                    <small>{{ __('Верный ответ') }}</small>
                                </div>
                                <p>{{ empty($comment) ? 'Удаленный комментарий' : $text }}</p>
                                <div class="under">
                                    @if ($countChilds)
                                        <a class="js-load-subcomments">
                                            <span class="uk-icon" uk-icon="icon:commenting; ratio:0.6"></span>
                                            <i>{{ $countChilds }}</i>
                                        </a>
                                    @endif
                                    <div class="comment_actions">
                                        <div class="btn btn-mini btn-link reply" data-comment="{{ $comment->id }}">{{ __('system.reply') }}</div>
                                        @if($question->user == auth()->user() && !$isRight)
                                            <div class="btn btn-mini btn-outline-success right_answer" data-comment="{{ $comment->id }}">{{ __('system.questions.right_answer') }}</div>
                                        @endif
                                    </div>
                                    <div class="additional_info">
                                        <div class="user">
                                            <i class="comment_id text-info">{{ '#'.$comment->id }}</i>
                                            <div class="icon">
                                                <img src="{{ getPhoto($comment->user_comment->user->photo, 'users') }}" alt="">
                                            </div>
                                            <b>{{ $comment->user_comment->user->name }}</b>
                                        </div>
                                        <div class="date">
                                            {{  $comment->created_at->format('d M Y, H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            @if (auth()->user())
                <form action="{{ route('comments.store') }}" method="post" enctype="multipart/form-data">

                    <h4>Оставить комментарий</h4>

                    @csrf

                    <div class="mt-4 mb-2">
                        <input type="text" name="text" class="form-control" placeholder="Не правильно ебаные волки, широкую на широкую!">
                        @if ($errors->has('text'))
                            @foreach ($errors->get('text') as $item)
                                <p class="error">{{ $item  }}</p>
                            @endforeach
                        @endif
                    </div>

                    <div class="input-reply mb-2">

                        <div class="description badge bg-secondary">
                            <label class="form-label">{{ __('crud.comments.fields.comment_reply') }}</label>
                            <p><i class="comment_id text-info"></i><b></b></p>
                        </div>

                        <input type="hidden" name="comment_main_id" value="">
                        @if ($errors->has('comment_main_id'))
                            @foreach ($errors->get('comment_main_id') as $item)
                                <p class="error">{{ $item  }}</p>
                            @endforeach
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary mb-3">{{ __('system.reply') }}</button>
                </form>
            @endif
        @endif
        <div class="category">
            <a href="{{route('categories.detail', $question->category->code)}}" class="btn btn-outline-primary">Все категории {{$question->category->title}}</a>
        </div>
    </div>
@endsection