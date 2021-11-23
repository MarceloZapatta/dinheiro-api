@component('mail::message')
Olá {{ $nome }}!
<br>
Você solicitou a recuperação de senha.<br>
Clique no link para recupera senha:

@component('mail::button', ['url' => $url])
Recuperar a senha
@endcomponent

Obrigado!
@endcomponent