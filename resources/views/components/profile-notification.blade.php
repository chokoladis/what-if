@if(!$notifications || $notifications->isEmpty())
    {{ __('Нету уведомлений') }}
@else
    @php
        $isFirst = true;
    @endphp
    {{--todo пометить прочитанным/непрочитанным --}}
    @foreach($notifications as $notification)
        @php
            $arNotifyData = \App\Services\NotificationService::toMessage($notification);
            if (empty($arNotifyData)){
                continue;
            }
        @endphp

        <div class="card {{ $notification->read_at ? '' : 'text-bg-info' }} {{ $isFirst ? '' : 'mt-3' }}">
            <div class="card-header">
                {{ $arNotifyData['title'] }}
            </div>
            <div class="card-body">
{{--                <h5 class="card-title">Особое обращение с заголовком</h5>--}}
                <p class="card-text">{!! $arNotifyData['text'] !!}</p>
            </div>
        </div>
        @php($isFirst = $isFirst === true ? null : $isFirst)
    @endforeach
@endif