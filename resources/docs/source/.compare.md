---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://localhost/docs/collection.json)

<!-- END_INFO -->

#Auth


Autenticação do usuário
<!-- START_d195aad6dfb644fed87c05a90de0b290 -->
## Recebe o token JWT.

> Example request:

```bash
curl -X POST \
    "http://localhost/v1/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"email@usuario.com","senha":"senha123"}'

```

```javascript
const url = new URL(
    "http://localhost/v1/auth/login"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "email@usuario.com",
    "senha": "senha123"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "sucesso": true,
    "status_codigo": 200,
    "access_token": "BEARER_TOKEN",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### HTTP Request
`POST v1/auth/login`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `email` | string |  required  | O e-mail do usuário.
        `senha` | string |  required  | Senha do usuário
    
<!-- END_d195aad6dfb644fed87c05a90de0b290 -->

<!-- START_884ef4592d2d8f3e4c08ec76a05b42e6 -->
## Realiza o cadastro no sistema

> Example request:

```bash
curl -X POST \
    "http://localhost/v1/auth/cadastrar" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"nome":"recusandae","documento":"Formato: XX.XXX.XXX\/XXXX-XX","organizacao_tipo_id":104.5,"email":"est","senha":"vel","nome_fantasia":"temporibus","consultor":true,"consultor_resumo":"iusto"}'

```

```javascript
const url = new URL(
    "http://localhost/v1/auth/cadastrar"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nome": "recusandae",
    "documento": "Formato: XX.XXX.XXX\/XXXX-XX",
    "organizacao_tipo_id": 104.5,
    "email": "est",
    "senha": "vel",
    "nome_fantasia": "temporibus",
    "consultor": true,
    "consultor_resumo": "iusto"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST v1/auth/cadastrar`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `nome` | string |  required  | Nome da pessoa
        `documento` | string |  optional  | obrigatório se Pessoa Júridica
        `organizacao_tipo_id` | number |  required  | Indica se é PJ ou PF
        `email` | string |  required  | E-mail da pessoa
        `senha` | string |  required  | Senha da pessoa
        `nome_fantasia` | string |  optional  | optional Obrigatório quando organizacao_tipo_id = 2 (PJ)
        `consultor` | boolean |  required  | Indica se será um cadastro de consultor
        `consultor_resumo` | string |  optional  | optional Obrigatório se for cadastro de consultor
    
<!-- END_884ef4592d2d8f3e4c08ec76a05b42e6 -->

<!-- START_709912c07d76504649a939f9e4f54252 -->
## v1/auth/verificar-email
> Example request:

```bash
curl -X POST \
    "http://localhost/v1/auth/verificar-email" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/v1/auth/verificar-email"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST v1/auth/verificar-email`


<!-- END_709912c07d76504649a939f9e4f54252 -->

<!-- START_ced05c33abee46f6b80af21ff3e07575 -->
## Desloga o usuário

> Example request:

```bash
curl -X POST \
    "http://localhost/v1/auth/sair" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/v1/auth/sair"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST v1/auth/sair`


<!-- END_ced05c33abee46f6b80af21ff3e07575 -->

<!-- START_ded2d637acbca76829f9a9820cadf1da -->
## v1/auth/atualizar
> Example request:

```bash
curl -X POST \
    "http://localhost/v1/auth/atualizar" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/v1/auth/atualizar"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST v1/auth/atualizar`


<!-- END_ded2d637acbca76829f9a9820cadf1da -->

<!-- START_e100403a484df32714df676195d59a67 -->
## v1/auth/perfil
> Example request:

```bash
curl -X POST \
    "http://localhost/v1/auth/perfil" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/v1/auth/perfil"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST v1/auth/perfil`


<!-- END_e100403a484df32714df676195d59a67 -->

#general


<!-- START_b49197dda1e390d1c17aa2d177702247 -->
## Dump api-docs.json content endpoint.

> Example request:

```bash
curl -X GET \
    -G "http://localhost/docs" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/docs"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
null
```

### HTTP Request
`GET docs`


<!-- END_b49197dda1e390d1c17aa2d177702247 -->

<!-- START_f7b7ea397f8939c8bb93e6cab64603ce -->
## Display Swagger API page.

> Example request:

```bash
curl -X GET \
    -G "http://localhost/api/documentation" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/api/documentation"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET api/documentation`


<!-- END_f7b7ea397f8939c8bb93e6cab64603ce -->

<!-- START_98d225804634be7700d8fd9cb9b24265 -->
## swagger-ui-assets/{asset}
> Example request:

```bash
curl -X GET \
    -G "http://localhost/swagger-ui-assets/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/swagger-ui-assets/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
null
```

### HTTP Request
`GET swagger-ui-assets/{asset}`


<!-- END_98d225804634be7700d8fd9cb9b24265 -->

<!-- START_a2c4ea37605c6d2e3c93b7269030af0a -->
## Display Oauth2 callback pages.

> Example request:

```bash
curl -X GET \
    -G "http://localhost/api/oauth2-callback" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/api/oauth2-callback"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET api/oauth2-callback`


<!-- END_a2c4ea37605c6d2e3c93b7269030af0a -->


