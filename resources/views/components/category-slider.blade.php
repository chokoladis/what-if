@php use App\Services\FileService; @endphp
@push('style')
    @vite(['resources/scss/components/slider.scss'])
@endpush
@push('script')
    @vite(['resources/js/components/slider.js'])
@endpush

<div class="category_slider">
    @foreach ($children as $item)
        <div class="card">
            <img src="{{ FileService::getPhoto($item->file) }}"
                 class="card-img-top" alt="...">
            <div class="card-body">
                <a href="{{ route('categories.detail', $item->code) }}"
                   class="card-title h5 link-info">{{ $item->title }}</a>
            </div>
        </div>
    @endforeach
</div>