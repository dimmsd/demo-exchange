@component('mail::message')

{{-- Greeting --}}
# Здравствуйте!

{{-- Intro Lines --}}
Вы получили это письмо потому что мы получили запрос на сброс пароля для вашего аккаунта

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
Если вы не отправляли этот запрос, просто проигнорируйте это письмо.

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
C наилучшими пожеланиями, команда сайта {{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
Если у Вас возникли проблемы с переходом по нажатию кнопки  "{{ $actionText }}", скопируйте и вставьте ссылку ниже
в ваш браузер: [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endisset
@endcomponent
