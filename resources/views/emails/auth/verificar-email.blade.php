@component('mail::message')
Olá {{ $nome }}!
<br>
Obrigado por criar uma conta conosco.<br>
Agora só falta realizar a verificação e começar a utilizar o <strong>{{ config('app.name') }}<strong>!

@component('mail::button', ['url' => $url])
Confirmar a conta
@endcomponent

Obrigado!
@endcomponent