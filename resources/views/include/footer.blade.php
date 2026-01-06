@php
    use App\Models\Feedback;

    $lang = app()->getLocale();
    $const = 'SUBJECTS_'.strtoupper($lang);
    if (defined('App\\Models\\Feedback::' . $const)) {
        $constValue = constant('App\\Models\\Feedback::'.$const);
    } else {
        $constValue = Feedback::SUBJECTS_RU;
    }
@endphp
<footer>
    <div class="main">
        <div class="bg"></div>
        <div class="container">
            <div class="row">
                <button type="button" class="btn btn-primary col-2" data-bs-toggle="modal"
                        data-bs-target="#modal-feedback">
                    {{ __('btn.callback') }}
                </button>
            </div>
            <div class="row mt-5">
                <ul class="col-12">
                    <li><a target="_blank" href="https://icons8.com/icon/p4rXi9HURgXT/help">иконки</a> от <a
                                target="_blank" href="https://icons8.com">Icons8</a></li>
                    <li><a href="https://www.flaticon.com/free-icons/moon" title="moon icons">Moon icons created by Good
                            Ware - Flaticon</a></li>
                    <li><a href="https://www.flaticon.com/free-icons/sun" title="sun icons">Sun icons created by Freepik
                            - Flaticon</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright text-center">{{ __('system.developed') }}<a
                    href="https://github.com/chokoladis">{{ config('app.develop.name') }}</a>{{ __(' / 2024-2026') }}
        </div>
    </div>
</footer>
</div>


<!-- Modal -->
{{-- do like component --}}
<div class="modal fade" id="modal-feedback" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="modalFeedbackTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFeedbackTitle">{{ __('crud.feedback.title_modal') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('crud.feedback.fields.email') }}</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="example@mail.ru" required>
                        @if ($errors->has('email'))
                            @foreach ($errors->get('email') as $item)
                                <p class="error">{{ $item  }}</p>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('crud.feedback.fields.phone') }}</label>
                        {{-- js-phone --}}
                        <input type="tel" name="phone"
                               class="form-control @error('phone') is-invalid @enderror js-phone-mask"
                               placeholder="7 901 234 5678" value="{{ old('phone') }}">
                        @if ($errors->has('phone'))
                            @foreach ($errors->get('phone') as $item)
                                <p class="error">{{ $item }}</p>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('crud.feedback.fields.subject') }}</label>
                        <select name="subject" class="form-select @error('subject') is-invalid @enderror" required>
                            @foreach($constValue as $subject)
                                <option>{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('crud.feedback.fields.comment') }}</label>
                        <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" cols="40"
                                  rows="3" required></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('btn.close') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('btn.add') }}</button>
            </div>
        </div>
    </div>
</div>


@if (\Session::has('message'))
    <div id="system-alert" class="alert alert-info" role="alert">
        {!! \Session::get('message') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        (function () {
            setTimeout(function () {
                $('#system-alert').alert('close');
            }, 5000);
        });
    </script>
@endif
@if (\Session::has('error'))
    <div id="system-alert-error" class="alert alert-danger" role="alert">
        {!! \Session::get('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        (function () {
            setTimeout(function () {
                $('#system-alert-error').alert('close');
            }, 5000);
        });
    </script>
    @endif

    @vite(['resources/js/app.js'])
    @stack('script')
    </body>
    </html>