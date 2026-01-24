@once
    @push('style')
        @vite(['resources/scss/components/right-answer.scss'])
    @endpush
@endonce

@if($compact)
    <div class="right-answer alert alert-success">
        <i uk-icon="check"></i>
    </div>
@else
    <div class="right-answer card-text alert alert-success">
        <i uk-icon="check"></i>
        <div class="content">
            <div class="user">
                <p>{{ '@'.$userName }}</p>
            </div>
            <b class="text-success">{{ $text }}</b>
        </div>
    </div>
@endif