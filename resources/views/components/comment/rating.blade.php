@props(['comment'])
@php
    use App\Models\CommentVotes;

    $queryRating = $comment->getRating();
    
    $commentStatus = CommentVotes::getForCurrentUser($comment->id);
    
    $rating = intval($queryRating['rating']);
    $userRating = !empty($commentStatus) ? \App\Enums\Vote::tryFrom($commentStatus['status']) : null;
@endphp
@if(auth()->id())
    <form action="{{ route('comments.vote') }}" method="POST" class="action_rating" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="comment_id" value="{{$comment->id}}">
        <div class="icon icon-circle plus {{ $userRating?->isLike() ? 'active' : ''}}" data-action="{{ \App\Enums\Vote::LIKE }}">
            <span class="uk-icon" uk-icon="plus"></span>
        </div>
        <div class="rating">
            <b>{{ $rating }}</b>
        </div>
        <div class="icon icon-circle minus {{ $userRating?->isLike() ? '' : 'active'}}" data-action="{{ \App\Enums\Vote::DISLIKE }}">
            <span class="uk-icon" uk-icon="minus"></span>
        </div>
    </form>
@else
    <div class="action_rating">
        <div class="icon icon-circle plus">
            <span class="uk-icon" uk-icon="plus"></span>
        </div>
        <div class="rating">
            <b>{{ $rating }}</b>
        </div>
        <div class="icon icon-circle minus">
            <span class="uk-icon" uk-icon="minus"></span>
        </div>
    </div>
@endif