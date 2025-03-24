@foreach($replies as $comment)
    @php
        $isRight = $question->right_comment_id === $comment->id;

        $parent = $comment->comment;
        $comment = $comment->reply;
        
        $text = '@'.$parent->user->name.' '.$comment->text;
    @endphp
    <div class="comment comment-reply {{ $isRight ? 'is-answer' : '' }}">

        <x-comment.rating :comment="$comment"></x-comment.rating>

        <div class="main">
            <div class="right_comment_description {{ !$isRight ? 'd-none' : '' }}">
                <i uk-icon="icon: check; ratio: 1.2"></i>
                <small>{{ __('Верный ответ') }}</small>
            </div>
            <p>{{ empty($comment) ? 'Удаленный комментарий' : $text }}</p>
            <div class="under">
                <div class="comment_actions">
                    <div class="btn btn-mini btn-link reply" data-comment="{{ $comment->id }}">{{ __('system.reply') }}</div>
                    @if($question->user == auth()->user())
                        <div class="btn btn-mini btn-outline-success right_answer" data-comment="{{ $comment->id }}">{{ __('system.questions.right_answer') }}</div>
                    @endif
                </div>
                <div class="additional_info">
                    <div class="user">
                        <i class="comment_id text-info">{{ '#'.$comment->id }}</i>
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
<form class="form-subcomments">
    <input type="hidden" name="offset" value="{{ count($replies) }}">

    <div class="js-subcomments-load">{{ __('Загрузить ещё') }}</div>
</form>