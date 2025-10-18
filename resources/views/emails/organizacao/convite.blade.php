@component('mail::message')
Olá!
<br>
Você foi convidado a se juntar a uma organização no <strong>{{ config('app.name') }}</strong>!<br>
Para aceitar o convite pressione o botão abaixo:

@component('mail::button', ['url' => config('app.front_url') . '/organizacoes/convite/' . urlencode($token)])
Aceitar convite
@endcomponent

Obrigado!
@endcomponent