@props(['comment', 'voteCurrentUser'])
@php
    use App\Enums\Vote;
    use App\Models\Comment;

    /** @var Comment $comment */

    $userRating = !empty($voteCurrentUser) ? Vote::tryFrom($voteCurrentUser['vote']) : null;
@endphp
@if(auth()->id())
    <form action="{{ route('comments.vote') }}" method="POST" class="action_rating" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="comment_id" value="{{$comment->id}}">
        <div class="icon icon-circle plus {{ $userRating?->isLike() ? 'active' : ''}}"
             data-vote="{{ Vote::LIKE }}">
            <span class="uk-icon" uk-icon="plus"></span>
        </div>
        <div class="rating">
            <b>{{ $comment->votes_sum_vote ?? 0 }}</b>
        </div>
        <div class="icon icon-circle minus {{ $userRating?->isLike() ? '' : 'active'}}"
             data-vote="{{ Vote::DISLIKE }}">
            <span class="uk-icon" uk-icon="minus"></span>
        </div>
    </form>
@else
    <div class="action_rating">
        <div class="icon icon-circle plus">
            <span class="uk-icon" uk-icon="plus"></span>
        </div>
        <div class="rating">
            <b>{{ $comment->votes_sum_vote ?? 0 }}</b>
        </div>
        <div class="icon icon-circle minus">
            <span class="uk-icon" uk-icon="minus"></span>
        </div>
    </div>
@endif