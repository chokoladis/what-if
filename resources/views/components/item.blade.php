@once
    @push('style')
        @vite(['resources/scss/components/item.scss'])
    @endpush
@endonce
@php
    /**
    * @var \App\Models\Question $question
     */

    $itemsTypeOut = \App\Services\QuestionService::ITEMS_TYPE_OUTPUT;
    $itemsTypeOutCookie = \Illuminate\Support\Facades\Cookie::get('items-type-output', 'simple');
    $currentItemsTypeOutput = in_array($itemsTypeOutCookie, $itemsTypeOut) ? $itemsTypeOutCookie : current($itemsTypeOut);

    $res = \App\Services\QuestionService::getVotes($question->id);
    $mainClass = $question->right_comment_id ? 'border-success' : '';

@endphp

<div class="item card mb-3 {{ $mainClass }}">
    <div class="row g-0">
    @if($currentItemsTypeOutput === 'simple')
        <a href="{{ route('questions.detail', $question->code) }}" class="img-col col-sm-4 col-md-3">
            <img src="{{ \App\Services\FileService::getPhoto($question->file, 'questions/') }}" alt=""
                 class="img-fluid rounded-start">
        </a>
        <div class="col-sm-8 col-md-9">
            <div class="card-body">
                <div class="votes">
                    <div class="icon like btn btn-success">
                        <b>{{ $res->likes ?? 0 }}</b>
                    </div>
                    <div class="icon dislike btn btn-danger">
                        <b>{{ $res->dislikes ?? 0 }}</b>
                    </div>
                </div>
                <a href="{{ route('questions.detail', $question->code) }}" class="card-title">{{ $question->title }}</a>

                <div class="category">
                    @if($question->category)
                        <a href="{{ route('categories.detail', $question->category->code) }}" class="title">
                            <i uk-icon="folder"></i>
                            <span>{{ $question->category?->title }}</span>
                        </a>
                    @endif
                    @if($question->tags)
                        <div class="tags">
                            @foreach($question->tags as $tag)
                                <a href="{{ route('questions.index', [ 'tags[]' => $tag->name ]) }}" class="link-success link-underline-opacity-25">{{ '#'.$tag->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($question->right_comment_id)
                    <x-right-answer :comment="$question->right_comment"></x-right-answer>
                @endif
                @if ($question->getPopularComment())
                    <x-comment.popular-comment
                            :comment="$question->getPopularComment()"></x-comment.popular-comment>
                @endif

                <div class="date">
                    <div class="author">
                        <i uk-icon="microphone"></i>
                        <span>{{ '@'.$question->user->getShortName() }}</span>
                    </div>
                    @if($question->statistics)
                        <div class="views">
                            <i uk-icon="eye"></i>
                            <span>{{ $question->statistics->views }}</span>
                        </div>
                    @endif
                    <p class="card-text">
                        <i uk-icon="question"></i>
                        <small class="text-body-secondary">{{ $question->created_at->diffForHumans() }}</small>
                    </p>
                    @if ($question->created_at != $question->updated_at)
                        <p class="card-text">
                            <i uk-icon="pencil"></i>
                            <small class="text-body-secondary">{{ $question->updated_at->diffForHumans() }}</small>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="card-body">
            <div class="item-header">
                <div class="votes">
                    <div class="icon like btn btn-success">
                        <b>{{ $res->likes ?? 0 }}</b>
                    </div>
                    <div class="icon dislike btn btn-danger">
                        <b>{{ $res->dislikes ?? 0 }}</b>
                    </div>
                    @if ($question->right_comment_id)
                        <x-right-answer :comment="$question->right_comment" :compact="true"></x-right-answer>
                    @endif
                    @if ($question->getPopularComment())
                        <x-comment.popular-comment
                                :comment="$question->getPopularComment()" :compact="true"></x-comment.popular-comment>
                    @endif
                </div>

                <div class="date">
                    <div class="author">
                        <i uk-icon="microphone"></i>
                        <span>{{ '@'.$question->user->getShortName() }}</span>
                    </div>
                    @if($question->statistics)
                        <div class="views">
                            <i uk-icon="eye"></i>
                            <span>{{ $question->statistics->views }}</span>
                        </div>
                    @endif
                    <p class="card-text">
                        <i uk-icon="question"></i>
                        <small class="text-body-secondary">{{ $question->created_at->diffForHumans() }}</small>
                    </p>
                    @if ($question->created_at != $question->updated_at)
                        <p class="card-text">
                            <i uk-icon="pencil"></i>
                            <small class="text-body-secondary">{{ $question->updated_at->diffForHumans() }}</small>
                        </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('questions.detail', $question->code) }}" class="card-title">{{ $question->title }}</a>

            <div class="category">
                @if($question->category)
                    <a href="{{ route('categories.detail', $question->category->code) }}" class="title">
                        <i uk-icon="folder"></i>
                        <span>{{ $question->category?->title }}</span>
                    </a>
                @endif
                @if($question->tags)
                    <div class="tags">
                        @foreach($question->tags as $tag)
                            <a href="{{ route('questions.index', [ 'tags[]' => $tag->name ]) }}" class="link-success link-underline-opacity-25">{{ '#'.$tag->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
    </div>
</div>