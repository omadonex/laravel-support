@component('mail::message')

@if ($headerImage)
![header][image]
[image]: {{ asset($headerImageSrc) }} "{{ $headerImageAlt }}"
@endif

@if ($greeting)
@if ($greetingText)
# {{ $greetingText }}
@elseif ($username)
# @lang('support::mail.greetingUser', ['username' => $username])
@else
# @lang('support::mail.greeting')
@endif
@endif

@foreach($content as $row)
@if ($row['type'] == 'line')
{{ $row['text'] }}
@elseif ($row['type'] == 'action')
@component('mail::button', ['url' => $row['url']])
{{ $row['text'] }}
@endcomponent
@endif
@endforeach

@lang('support::mail.regards'),<br>
[@lang('support::app.title')]({{ config('app.url') }})

@foreach($actions as $row)
@component('mail::subcopy')
@lang('support::mail.buttonTrouble', ['button' => $row['text']]): [{{ $row['url'] }}]({{ $row['url'] }})
@endcomponent
@endforeach

@endcomponent