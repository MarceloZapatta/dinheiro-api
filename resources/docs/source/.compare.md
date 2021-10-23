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
[Get Postman Collection](http://127.0.0.1:8000/docs/collection.json)

<!-- END_INFO -->

#Auth


Autenticação do usuário
<!-- START_d195aad6dfb644fed87c05a90de0b290 -->
## Login

> Example request:

```bash
curl -X POST \
    "http://127.0.0.1:8000/v1/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"email@usuario.com","senha":"senha123"}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/auth/login"
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
> Example response (401):

```json
{
    "sucesso": false,
    "mensagem": "Login ou senha inválidos",
    "status_codigo": 401
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
## Registro

> Example request:

```bash
curl -X POST \
    "http://127.0.0.1:8000/v1/auth/cadastrar" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"nome":"et","documento":"Formato: XX.XXX.XXX\/XXXX-XX","organizacao_tipo_id":29.263233,"email":"sit","senha":"hic","nome_fantasia":"ducimus","consultor":false,"consultor_resumo":"at"}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/auth/cadastrar"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nome": "et",
    "documento": "Formato: XX.XXX.XXX\/XXXX-XX",
    "organizacao_tipo_id": 29.263233,
    "email": "sit",
    "senha": "hic",
    "nome_fantasia": "ducimus",
    "consultor": false,
    "consultor_resumo": "at"
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

<!-- START_9b95ffe6419dcbddc61b09006a1ce7dd -->
## Verificar token de e-mail

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/auth/verificar-email" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"token":"ut"}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/auth/verificar-email"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "token": "ut"
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (422):

```json
{
    "sucesso": false,
    "mensagem": "Campos inválidos!",
    "status_codigo": 422,
    "erros": {
        "token": [
            "O token é inválido, tente novamente."
        ]
    }
}
```

### HTTP Request
`GET v1/auth/verificar-email`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `token` | string |  required  | Token recebido via e-mail
    
<!-- END_9b95ffe6419dcbddc61b09006a1ce7dd -->

<!-- START_ced05c33abee46f6b80af21ff3e07575 -->
## Desloga o usuário

> Example request:

```bash
curl -X POST \
    "http://127.0.0.1:8000/v1/auth/sair" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/auth/sair"
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
    "http://127.0.0.1:8000/v1/auth/atualizar" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/auth/atualizar"
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
    "http://127.0.0.1:8000/v1/auth/perfil" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/auth/perfil"
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

#Contas


Contas
<!-- START_02a1f108697b3e781184046e9d9420b5 -->
## Listagem

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/contas" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/contas"
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


> Example response (401):

```json
null
```

### HTTP Request
`GET v1/contas`


<!-- END_02a1f108697b3e781184046e9d9420b5 -->

<!-- START_2efa06b6ab0c4f7dac2ef31b835bd75b -->
## Gravar

> Example request:

```bash
curl -X POST \
    "http://127.0.0.1:8000/v1/contas" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"nome":"quisquam","icone":"consectetur","cor_id":6}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/contas"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nome": "quisquam",
    "icone": "consectetur",
    "cor_id": 6
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
`POST v1/contas`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `nome` | string |  required  | Nome da conta
        `icone` | string |  required  | Ícone da conta
        `cor_id` | integer |  required  | ID Cor da conta
    
<!-- END_2efa06b6ab0c4f7dac2ef31b835bd75b -->

<!-- START_3345da642f267368129aab5d2f6e3183 -->
## Visualizar

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/contas/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/contas/1"
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


> Example response (401):

```json
null
```

### HTTP Request
`GET v1/contas/{id}`


<!-- END_3345da642f267368129aab5d2f6e3183 -->

<!-- START_037eb65bbcef8cae6d3f5c9cf64d0de4 -->
## Atualizar

> Example request:

```bash
curl -X PUT \
    "http://127.0.0.1:8000/v1/contas/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"nome":"et","icone":"officiis","cor_id":9}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/contas/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nome": "et",
    "icone": "officiis",
    "cor_id": 9
}

fetch(url, {
    method: "PUT",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT v1/contas/{id}`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `nome` | string |  optional  | optional Nome da conta
        `icone` | string |  optional  | optional Ícone da conta
        `cor_id` | integer |  optional  | optional ID Cor da conta
    
<!-- END_037eb65bbcef8cae6d3f5c9cf64d0de4 -->

<!-- START_89873943f399de5dcdc5ffd4a8715835 -->
## Excluir

> Example request:

```bash
curl -X DELETE \
    "http://127.0.0.1:8000/v1/contas/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/contas/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE v1/contas/{id}`


<!-- END_89873943f399de5dcdc5ffd4a8715835 -->

#Organizações


Organizações são entidades que podem ser vinculadas as Pessoas.
<!-- START_a6b3852da31d727152512049d74d4461 -->
## Retorna as organizações do usuário

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/organizacoes" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/organizacoes"
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


> Example response (401):

```json
null
```

### HTTP Request
`GET v1/organizacoes`


<!-- END_a6b3852da31d727152512049d74d4461 -->

#general


<!-- START_b49197dda1e390d1c17aa2d177702247 -->
## Dump api-docs.json content endpoint.

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/docs" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/docs"
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
    -G "http://127.0.0.1:8000/api/documentation" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/documentation"
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
    -G "http://127.0.0.1:8000/swagger-ui-assets/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/swagger-ui-assets/1"
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
    -G "http://127.0.0.1:8000/api/oauth2-callback" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/api/oauth2-callback"
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


