# Banco de dados

O projeto usa **MySQL ou MariaDB** (desenvolvido com o que acompanha o XAMPP). O arquivo `database/database.sql` contém a estrutura completa das tabelas e dados iniciais **fictícios**, incluindo o usuário administrador.

> Ordem de subida do projeto completo: **banco de dados → backend → frontend**.
> Depois deste passo, siga para [backend.md](backend.md).

## Requisitos

- MySQL ou MariaDB em execução (no XAMPP: *XAMPP Control Panel → MySQL → Start*).
- Cliente para executar o SQL: o terminal (`mysql`), o DataGrip ou o phpMyAdmin.

## 1. Criar o banco

```sql
CREATE DATABASE simpledb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Pode ser executado em qualquer cliente, ou pelo terminal:

```bash
mysql -u root -p -e "CREATE DATABASE simpledb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

`simpledb` é o nome que vem no `database.db.example` do backend. Se usar outro nome, altere o campo `db=` no `database.db`.

## 2. Importar o dump

O arquivo `.sql` deve ser **executado**, e não importado como dados.

**Terminal (Linux, macOS, Prompt de Comando do Windows):**

```bash
mysql -u root -p simpledb < database/database.sql
```

**PowerShell** (o operador `<` não funciona nele):

```powershell
mysql -u root -p simpledb -e "source database/database.sql"
```

Use barras normais (`/`) no caminho. Se o comando `mysql` não for reconhecido no XAMPP, use o caminho completo: `C:\xampp\mysql\bin\mysql.exe`. Se o `root` não tem senha (padrão do XAMPP), pressione Enter quando ela for pedida.

**DataGrip:** clique com o botão direito no schema `simpledb`, vá em *SQL Scripts → Run SQL Script...* e escolha o `database.sql`.

> Não use *Import Data from File*. Esse recurso é para CSV e cria uma tabela de colunas aleatórias em vez de executar os comandos do arquivo.

**phpMyAdmin:** selecione o banco `simpledb`, abra a aba *Importar* e envie o arquivo.

## 3. Verificar

```sql
USE simpledb;
SHOW TABLES;
```

Devem aparecer as tabelas do sistema. A verificação completa é fazer login depois que o backend e o frontend estiverem no ar.

## 4. Configurar a conexão

Informe os dados do banco no arquivo `database.db` do backend. Veja [backend.md](backend.md#2-conexão-com-o-banco-databasedb).

## Usuário inicial

| E-mail | Senha |
|---|---|
| `admin@email.com` | `123123123` |

Todos os dados do dump são fictícios. Esse usuário serve **somente para ambiente local**. Troque a senha em qualquer outro ambiente.

## Recriar o banco do zero

```sql
DROP DATABASE simpledb;
```

Isso **apaga todos os dados**. Depois repita os passos 1 a 3.

## Problemas comuns

| Sintoma | Causa provável |
|---|---|
| `Unknown database 'simpledb'` | O banco não foi criado (passo 1) |
| `Access denied for user` | Usuário ou senha incorretos |
| Uma tabela com dezenas de colunas e nenhuma tabela do sistema | O arquivo foi importado como CSV. Execute-o como script (passo 2) |
| Acentos quebrados | O arquivo precisa estar em UTF-8 e o banco em `utf8mb4` |
| `Table ... already exists` | O banco já tinha dados. Recrie-o do zero (seção acima) |

## Para quem mantém o projeto: gerar um novo dump

No PowerShell, em uma única linha:

```powershell
& "C:\xampp\mysql\bin\mysqldump.exe" -u root -p --routines --triggers --complete-insert --skip-comments --default-character-set=utf8mb4 --result-file=database/database.sql simpledb
```

- Use `--result-file` em vez de `>`: no Windows PowerShell 5.1 o `>` grava o arquivo em UTF-16, e a importação depois quebra os acentos.
- Se houver views, triggers ou procedures, remova as cláusulas `DEFINER=` do arquivo gerado. Elas impedem a importação em máquinas onde esse usuário não existe.
- Antes de commitar, confira que o arquivo **não contém dados reais** (nomes, CPFs, e-mails, senhas), por causa da LGPD. Esvazie também as tabelas de log, mantendo só a estrutura.
- Teste a importação em um banco vazio antes de commitar.