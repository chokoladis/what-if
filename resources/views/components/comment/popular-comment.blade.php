@once
    @push('style')
        @vite(['resources/scss/components/popular-comment.scss'])
    @endpush
@endonce

@if($compact)
    <div class="popular-comment alert alert-warning" role="alert">
        <i uk-icon="bolt"></i>
    </div>
@else
    <div class="popular-comment alert alert-warning" role="alert">
        <i uk-icon="bolt"></i>
        <div class="content">
            <div class="user">
                <p>{{ '@'.$userName }}</p>
            </div>
            <p class="mb-0 fst-italic">{{ $text }}</p>
        </div>
    </div>
@endif
