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
    -d '{"nome":"consequatur","documento":"Formato: XX.XXX.XXX\/XXXX-XX","organizacao_tipo_id":498439479.9718,"email":"est","senha":"ipsa","nome_fantasia":"fugit","consultor":false,"consultor_resumo":"mollitia"}'

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
    "nome": "consequatur",
    "documento": "Formato: XX.XXX.XXX\/XXXX-XX",
    "organizacao_tipo_id": 498439479.9718,
    "email": "est",
    "senha": "ipsa",
    "nome_fantasia": "fugit",
    "consultor": false,
    "consultor_resumo": "mollitia"
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
    -d '{"token":"omnis"}'

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
    "token": "omnis"
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

#Categorias


Categorias
<!-- START_175bf203b9df603cc006247707c11a68 -->
## Listagem

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/categorias" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/categorias"
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
`GET v1/categorias`


<!-- END_175bf203b9df603cc006247707c11a68 -->

<!-- START_ecff4ac91e0d21e82b198a77f6460e0c -->
## Gravar

> Example request:

```bash
curl -X POST \
    "http://127.0.0.1:8000/v1/categorias" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"nome":"ipsam","icone":"in","cor_id":16}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/categorias"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nome": "ipsam",
    "icone": "in",
    "cor_id": 16
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
`POST v1/categorias`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `nome` | string |  required  | Nome da categoria
        `icone` | string |  required  | Ícone da categoria
        `cor_id` | integer |  required  | ID Cor da categoria
    
<!-- END_ecff4ac91e0d21e82b198a77f6460e0c -->

<!-- START_0b5ec410dad95f6fbdb559f106384452 -->
## Visualizar

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/categorias/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/categorias/1"
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
`GET v1/categorias/{id}`


<!-- END_0b5ec410dad95f6fbdb559f106384452 -->

<!-- START_08e8862177a9afb29f16f27072813143 -->
## Atualizar

> Example request:

```bash
curl -X PUT \
    "http://127.0.0.1:8000/v1/categorias/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"nome":"molestiae","icone":"asperiores","cor_id":14}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/categorias/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "nome": "molestiae",
    "icone": "asperiores",
    "cor_id": 14
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
`PUT v1/categorias/{id}`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `nome` | string |  optional  | optional Nome da categoria
        `icone` | string |  optional  | optional Ícone da categoria
        `cor_id` | integer |  optional  | optional ID Cor da categoria
    
<!-- END_08e8862177a9afb29f16f27072813143 -->

<!-- START_cabeb235aade0fba1ed1e866614530fd -->
## Excluir

> Example request:

```bash
curl -X DELETE \
    "http://127.0.0.1:8000/v1/categorias/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/categorias/1"
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
`DELETE v1/categorias/{id}`


<!-- END_cabeb235aade0fba1ed1e866614530fd -->

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
    -d '{"nome":"dolores","icone":"sit","cor_id":7,"saldo_inicial":42396918.41353864}'

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
    "nome": "dolores",
    "icone": "sit",
    "cor_id": 7,
    "saldo_inicial": 42396918.41353864
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
        `saldo_inicial` | float |  required  | Saldo inicial da conta
    
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
    -d '{"nome":"provident","icone":"est","cor_id":6}'

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
    "nome": "provident",
    "icone": "est",
    "cor_id": 6
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

#Cores


Cores
<!-- START_3e8a2cac5c9047b04721bbe977966bdc -->
## Listagem

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/cores" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/cores"
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
`GET v1/cores`


<!-- END_3e8a2cac5c9047b04721bbe977966bdc -->

#Movimentações


Movimentações
<!-- START_feae8727a1034f99ba3c3f62a267e358 -->
## Listagem

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/movimentacoes" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/movimentacoes"
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
`GET v1/movimentacoes`


<!-- END_feae8727a1034f99ba3c3f62a267e358 -->

<!-- START_0b3a36a0c1545f90f1ac597ee9d67197 -->
## Gravar

> Example request:

```bash
curl -X POST \
    "http://127.0.0.1:8000/v1/movimentacoes" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"descricao":"totam","observacoes":"rem","data_transacao":"autem","categoria_id":19,"conta_id":20,"valor":4712.198}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/movimentacoes"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "descricao": "totam",
    "observacoes": "rem",
    "data_transacao": "autem",
    "categoria_id": 19,
    "conta_id": 20,
    "valor": 4712.198
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
`POST v1/movimentacoes`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `descricao` | string |  required  | Descricao da movimentacao
        `observacoes` | string |  optional  | optional Observações da movimentação
        `data_transacao` | string |  required  | DAta em que ocorreu a movimentacao
        `categoria_id` | integer |  required  | ID Categoria da movimentacao
        `conta_id` | integer |  required  | ID Conta da movimentacao
        `valor` | float |  required  | Valor da movimentacao
    
<!-- END_0b3a36a0c1545f90f1ac597ee9d67197 -->

<!-- START_e502c34149708c406bd6590560ab69d6 -->
## Visualizar

> Example request:

```bash
curl -X GET \
    -G "http://127.0.0.1:8000/v1/movimentacoes/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/movimentacoes/1"
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
`GET v1/movimentacoes/{id}`


<!-- END_e502c34149708c406bd6590560ab69d6 -->

<!-- START_109d7bf5c3f5c705f2f54f54134804e1 -->
## Atualizar

> Example request:

```bash
curl -X PUT \
    "http://127.0.0.1:8000/v1/movimentacoes/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"descricao":"dolore","observacoes":"qui","data_transacao":"aut","categoria_id":8,"conta_id":6,"valor":2018.77312}'

```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/movimentacoes/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "descricao": "dolore",
    "observacoes": "qui",
    "data_transacao": "aut",
    "categoria_id": 8,
    "conta_id": 6,
    "valor": 2018.77312
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
`PUT v1/movimentacoes/{id}`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `descricao` | string |  required  | Descricao da movimentacao
        `observacoes` | string |  optional  | optional Observações da movimentação
        `data_transacao` | string |  required  | Data em que ocorreu a movimentacao
        `categoria_id` | integer |  required  | ID Categoria da movimentacao
        `conta_id` | integer |  required  | ID Conta da movimentacao
        `valor` | float |  required  | Valor da movimentacao
    
<!-- END_109d7bf5c3f5c705f2f54f54134804e1 -->

<!-- START_1c330e59db8d716675f8c0b1c3c715ee -->
## Excluir

> Example request:

```bash
curl -X DELETE \
    "http://127.0.0.1:8000/v1/movimentacoes/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://127.0.0.1:8000/v1/movimentacoes/1"
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
`DELETE v1/movimentacoes/{id}`


<!-- END_1c330e59db8d716675f8c0b1c3c715ee -->

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


