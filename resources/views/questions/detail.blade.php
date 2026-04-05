@php
    use App\Services\FileService;

    $votes = $question->votes->groupBy('vote')->map->count()->toArray();
    $like = \App\Enums\Vote::LIKE->value;
    $dislike = \App\Enums\Vote::DISLIKE->value;

    $currentVote = !empty($questionCurrentUserVote) ? $questionCurrentUserVote['vote'] : 0;
@endphp
@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/css/uikit.min.css"/>
    @vite(['resources/scss/questions.scss'])
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.0/dist/js/uikit-icons.min.js"></script>
    @vite(['resources/js/question.js'])
@endpush

@section('content')
    <div class="question-page container" data-question-id="{{ $question?->id ?? 0 }}">
        <div class="title">
            <div class="actions">
                @if(auth()->id())
                    <input type="hidden" id="question_id" value="{{ $question->id }}">
                    <div class="icon like btn {{ $currentVote === $like ? 'btn-success' : 'btn-outline-success' }}"
                         data-vote="{{$like}}">
                        <span class="uk-icon" uk-icon="chevron-up"></span>
                        <b>{{ $votes[$like] ?? 0 }}</b>
                    </div>
                    <div class="icon dislike btn {{ $currentVote === $dislike ? 'btn-danger' : 'btn-outline-danger' }}"
                         data-vote="{{$dislike}}">
                        <span class="uk-icon" uk-icon="chevron-down"></span>
                        <b>{{ $votes[$dislike] ?? 0 }}</b>
                    </div>
                @else
                    <div class="icon like btn btn-success">
                        <span class="uk-icon" uk-icon="chevron-up"></span>
                        <b>{{ $votes[$like] ?? 0 }}</b>
                    </div>
                    <div class="icon dislike btn btn-danger">
                        <span class="uk-icon" uk-icon="chevron-down"></span>
                        <b>{{ $votes[$like] ?? 0 }}</b>
                    </div>
                @endif
            </div>
            <div class="description">
                <img src="{{ FileService::getPhoto($question->file) }}" alt="...">
                {{--во весь экран --}}
                <div class="shadow"></div>
                <div class="bottom">
                    <h1 class="h1">{{ $question->getShortTitle(70) }}</h1>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#question-img-popup">
                        <span uk-icon="image"></span>
                    </button>
                </div>
            </div>
        </div>
        <div id="question-img-popup" class="modal fade modal-xl" tabindex="-1" role="dialog" data-bs-keyboard="false" tabindex="-2"
             aria-labelledby="imgModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <img src="{{ FileService::getPhoto($question->file) }}" class="img-responsive">
                    </div>
                </div>
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
                    <i>{{ $question->created_at->format('d M Y, H:i:s') }}</i>
                </div>

                @if($question->created_at != $question->updated_at)
                    <div class="update">
                        <span uk-icon="pencil"></span>
                        <i>{{ $question->updated_at->format('d M Y, H:i:s') }}</i>
                    </div>
                @endif
            </div>
            <div class="views">
                <span uk-icon="eye"></span>
                <i>{{ $question->statistics->views }}</i>
            </div>
        </div>
        @if(mb_strlen($question->title) > 70)
            <h3 class="h3 mt-4">{{ $question->title }}</h3>
        @endif
        <div class="comments">
            @if($arComments)
                @foreach ($arComments as $arComment)
                    @php
                        $comment = $arComment['comment'];
                        $countChilds = $arComment['count_childs'];

                        $isRight = $question->right_comment_id === $comment->id;
                    @endphp
                    <div class="comment {{ $isRight ? 'is-answer' : '' }}" data-comment-id="{{ $comment->id }}">

                        <x-comment.rating :comment="$comment"></x-comment.rating>

                        <div class="main">
                            <div class="right_comment_description {{ !$isRight ? 'd-none' : '' }}">
                                <i uk-icon="icon: check; ratio: 1.2"></i>
                                <small>{{ __('comment.is_answer') }}</small>
                            </div>
                            <p>{{ empty($comment) ? 'Удаленный комментарий' : $comment->text }}</p>
                            <div class="under">
                                @if ($countChilds)
                                    <div class="js-load-subcomments">
                                        <span class="uk-icon" uk-icon="icon:commenting; ratio:0.6"></span>
                                        <i>{{ $countChilds }}</i>
                                    </div>
                                @endif
                                @if (auth()->user())
                                    <div class="comment_actions">
                                        <div class="btn btn-mini btn-link reply"
                                             data-comment="{{ $comment->id }}">{{ __('btn.reply') }}</div>
                                        @if($question->user == auth()->user() && !$isRight)
                                            <div class="btn btn-mini btn-outline-success right_answer"
                                                 data-comment="{{ $comment->id }}">{{ __('system.questions.right_answer') }}</div>
                                        @endif
                                    </div>
                                @endif
                                <div class="additional_info">
                                    <div class="user">
                                        <i class="comment_id text-info">{{ '#'.$comment->id }}</i>
                                        <div class="icon">
                                            <img src="{{ FileService::getPhoto($comment->user->photo) }}"
                                                 alt="">
                                        </div>
                                        <b>{{ $comment->user->name }}</b>
                                    </div>
                                    <div class="date">
                                        {{  $comment->created_at->format('d M Y, H:i:s') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="subcomments-container" class="d-none"></div>
                    </div>
                @endforeach
            @endif
        </div>

        @auth
            <form action="{{ route('comments.store') }}" method="post" enctype="multipart/form-data">

                <h4>{{ __('comment.left_comment') }}</h4>

                @csrf

                <div class="mt-4 mb-2">
                    <input type="text" name="text" class="form-control"
                           placeholder="{{ __('comment.placeholder') }}">
                    @if ($errors->has('text'))
                        @foreach ($errors->get('text') as $item)
                            <p class="error">{{ $item }}</p>
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

                <button type="submit" class="btn btn-primary mb-3">{{ __('btn.reply') }}</button>
            </form>
        @endauth()
        @if($question->category)
            <div class="category">
                <a href="{{ route('categories.detail', $question->category->code)}}"
                   class="btn btn-outline-primary">
                    {{ __('questions.all_questions') . ' "'. $question->category->title . '"'}}
                </a>
            </div>
        @endif
    </div>
@endsection