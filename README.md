# Amino 2.0

Um aplicativo inspirado no amino, uma rede social com comunidades, perfis, postagem de blogs/wikis/enquetes, chats e etc... Tentativa de replicar suas principais funcionalidades.

Tecnologias: html, css, js, mysql e php

## Migrations

O schema do banco e mantido em arquivos SQL dentro de `database/migrations/`.
Cada arquivo deve ter um nome ordenavel, por exemplo `002_create_categories.sql`,
e conter somente SQL. Nao renomeie nem edite uma migration que ja foi aplicada;
crie uma nova com o proximo numero para qualquer alteracao futura.

Para aplicar as migrations pendentes:

```bash
docker compose up -d
docker compose exec app php migrate.php
```

O comando cria a tabela `migrations`, executa os arquivos em ordem e registra
cada arquivo aplicado. Em execucoes seguintes, migrations ja registradas aparecem
como `SKIPPED`; se nenhuma estiver pendente, o comando informa isso. A tabela
tambem pode ser consultada diretamente:

```bash
docker compose exec db mysql -uamino -p amino2 -e "SELECT migration, applied_at FROM migrations ORDER BY id;"
```

Cada arquivo e executado dentro de uma transacao e so e registrado depois do
sucesso. O runner interrompe na primeira falha, informa o arquivo e retorna
codigo de erro. O MySQL faz commit implicito em algumas operacoes DDL, portanto
essas operacoes podem nao permitir rollback completo; nesse caso a migration nao
e registrada e o schema deve ser conferido antes de corrigir e executar novamente.

## Banco novo e `amino2.sql`

`001_initial.sql` reproduz o schema atual de `amino2.sql`: `users`,
`comunidades`, `membros_comunidade`, `posts`, `comentarios` e `curtidas`, com
seus indices e chaves estrangeiras. O dump atual nao possui `INSERT`, entao nao
ha dados de teste ou seeds para separar.

O arquivo `amino2.sql` foi mantido como referencia historica, mas deixou de ser
montado em `/docker-entrypoint-initdb.d/`; o MySQL nao executa migrations
automaticamente. O volume `db_data` preserva o banco existente. Em um volume
novo, suba os servicos e execute `docker compose exec app php migrate.php`.
Nao e necessario remover volumes para aplicar migrations e isso nunca e feito
automaticamente.
