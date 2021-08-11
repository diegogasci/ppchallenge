# PP Challenge

A aplicação PP Challenge foi desenvolvida utilizando o micro-framework PHP Lumen, visto que é um framework leve, otimizado para o desenvolvimento de APIs e micro-serviços, mas que conta com a confiança já criada com o projeto "irmão maior", Laravel.

## Setup 
Para o setup inicial é necessário clonar este repositório em sua máquina e realizar a instalação das dependências: 
```bash
git clone git@github.com:diegogasci/ppchallenge.git
cd ppchallenge/api
composer install
```

Após instalado, podemos popular o banco a partir das migrations e seeds criadas com os comandos abaixo a parti da pasta `api`:

```bash
php artisan migrate
php artisan db:seed
```

A aplicação conta com uma estrutura em Docker responsável pelo serviço de banco de dados (MySQL), webserver (nginx) e PHP (7.4). Para iniciá-la, basta executar:

```bash
docker-compose up
```

Feito isso a aplicação estará disponível em localhost, porta 8000 : <http://localhost:8000>.

## Tests

Foram criados testes de integração cobrindo as rotinas disponíveis nos controllers de usuários (UserController) e transações (TransactionController):

```bash
cd api
vendor/bin/phpunit
```

# Postman collection

Para facilitar a visualização da API foi criada uma collection via Postman, disponível no link abaixo:

<https://www.postman.com/collections/407c203f1e2f4a0ce684>


# Endpoints

## Usuário

### - Create
**Endpoint:** /user
**Método:** POST 
**Payload:**
name: (string) Nome do usuário a ser criado.
email: (string) Email do usuário a ser criado.
document_type: (0/1) Identificador do tipo de usuário (0 para usuário comum, 1 para lojista).
document: (string) Documento de identificação a ser criado.
password: (string) Senha do usuário a ser criado.
**Responses**
200 - SUCESSO.
401/422 - ERRO. Descrição disponível em campo *message*.

### - Show
**Endpoint:** /user/*{userId}*
**Método:** GET 
**Payload:** -
**Responses**
200 - SUCESSO.
401/422 - ERRO. Descrição disponível em campo *message*.

### - Update
Atualiza uma ou mais informação do usuário, de acordo com o payload
**Endpoint:** /user/*{userId}*
**Método:** PATCH 
**Payload:**
name: (string) Nome do usuário a ser criado.
email: (string) Email do usuário a ser criado.
document_type: (0/1) Identificador do tipo de usuário (0 para usuário comum, 1 para lojista).
document: (string) Documento de identificação a ser criado.
password: (string) Senha do usuário a ser criado.
**Responses**
200 - SUCESSO.
401/422 - ERRO. Descrição disponível em campo *message*.

### - Delete
**Endpoint:** /user/*{userId}*
**Método:** DELETE 
**Payload:** -
**Responses**
204 - SUCESSO.
401/422 - ERRO. Descrição disponível em campo *message*.

### - Show wallet
Exibe a carteira de um usuário específico
**Endpoint:** /user/*{userId}*/wallet
**Método:** GET 
**Payload:** -
**Responses**
200 - SUCESSO.
401/422 - ERRO. Descrição disponível em campo *message*.


## Transação

### - Create
**Endpoint:** /transaction
**Método:** POST 
**Payload:**
payer_id: Id de usuário pagador
payee_id: Id de usuário recebedor
amout: Valor da transação em dinheiro
**Responses**
200 - SUCESSO.
401/422 - ERRO. Descrição disponível em campo *message*.

## Helper

### - list all users, with wallet
Endpoint criado para facilitar visualização dos usuários e suas carteiras conforme são realizados os testes via Postman.
**Endpoint:** /user/list/all
**Método:** GET 
**Payload:** -
**Responses**
200 - SUCESSO.