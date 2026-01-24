@push('style')
    @vite(['resources/scss/components/slider.scss'])
@endpush
@push('script')
    @vite(['resources/js/slick.min.js', 'resources/js/components/slider.js'])
@endpush

<div class="category_slider">
    @foreach ($childs as $item)
        <div class="card">
            <img src="{{ $item->file && $item->file->path ? Storage::url('categories/'.$item->file->path) : $SITE_NOPHOTO }}"
                 class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title">{{ $item->title }}</h5>
                <a href="{{ route('categories.detail', $item->code) }}" class="btn btn-primary">link</a>
            </div>
        </div>
    @endforeach
</div>