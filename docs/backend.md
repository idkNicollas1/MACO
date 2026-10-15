# Backend (API REST)

API REST do projeto MACO, feita em PHP com [Slim 4](https://www.slimframework.com/). Usa autenticação por token JWT (`Authorization: Bearer <token>`), banco MySQL/MariaDB, envio de e-mails (PHPMailer) e geração de documentos Word (PhpWord) e PDF (Dompdf).

> Ordem de subida do projeto completo: **banco de dados → backend → frontend**.
> Se ainda não subiu o banco, comece por [database.md](database.md).

## Requisitos

| Item | Versão |
|---|---|
| PHP | 8.2 ou superior (desenvolvido com 8.2.12) |
| Composer | 2.x |
| MySQL ou MariaDB | veja [database.md](database.md) |

Extensões PHP necessárias: `curl`, `dom`, `mbstring`, `mysqli`, `openssl`, `pdo_mysql`, `xml`, `xmlwriter` e `zip`.

Opcional: `gd`. Habilite se a geração de documentos com imagens PNG ou GIF falhar.

Para listar as extensões ativas, rode `php -m`. No XAMPP, habilite uma extensão removendo o `;` da linha correspondente em `C:\xampp\php\php.ini` e reinicie o servidor.

## Instalação

```bash
git clone <URL-DO-REPOSITORIO>
cd <PASTA-DO-PROJETO>
composer install
```

A dependência `simpledb/simpledb` (branch `dev-main`) é instalada a partir do GitHub (`JoaoCordeir0/simpledb`). Se o `composer install` falhar nela, verifique sua conexão e o acesso ao repositório.

## Configuração

### 1. Variáveis de ambiente (`.env`)

Crie o `.env` a partir do exemplo:

```powershell
Copy-Item .env.example .env     # Windows (PowerShell)
```

```bash
cp .env.example .env            # Linux / macOS
```

| Variável | Descrição |
|---|---|
| `TOKEN_SECRET` | Chave usada para assinar os tokens de autenticação. **Obrigatória.** |
| `EMAIL_HOST` | Servidor SMTP |
| `EMAIL_PORT` | Porta do servidor SMTP |
| `EMAIL_USER` | Usuário do SMTP |
| `EMAIL_PASSWORD` | Senha do SMTP |
| `EMAIL_FROM` | Endereço do remetente dos e-mails |
| `BACKEND_HOST` | URL em que o backend roda, com protocolo (ex.: `http://localhost:9090`) |
| `FRONTEND_HOST` | URL em que o frontend abre no navegador, com protocolo |

Para gerar um valor seguro para o `TOKEN_SECRET`:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

Observações:

- Escreva uma variável por linha, no formato `CHAVE=valor`, **sem aspas e sem espaços ao redor do `=`**.
- As variáveis de **e-mail** são usadas no **reset de senha** e nos **avisos de atualização de status do artigo** enviados aos alunos. Sem um SMTP configurado, esses envios não funcionam.
- `BACKEND_HOST` e `FRONTEND_HOST` são usadas pelo sistema para montar endereços (por exemplo, links nos e-mails). Preencha as duas: se ficarem vazias, esses endereços saem sem domínio.
- O `.env` contém segredos e **não deve ser commitado** (já está no `.gitignore`).

### 2. Conexão com o banco (`database.db`)

O arquivo `database.db` **não é um banco SQLite**. É o arquivo de configuração da conexão MySQL, lido pela biblioteca `simpledb`. Crie-o a partir do exemplo:

```powershell
Copy-Item database.db.example database.db     # Windows (PowerShell)
```

```bash
cp database.db.example database.db            # Linux / macOS
```

```
bank=mysql
host=localhost
db=simpledb
uid=root
pwd=
```

| Campo | Descrição |
|---|---|
| `bank` | Tipo do banco. Mantenha `mysql` (vale para MariaDB também) |
| `host` | Endereço do servidor de banco |
| `db` | Nome do banco criado em [database.md](database.md) |
| `uid` | Usuário do banco |
| `pwd` | Senha do usuário |

Os valores do exemplo (`root` sem senha) são o padrão do XAMPP e servem **apenas para ambiente local**. Em qualquer outro ambiente, use um usuário próprio com senha. O arquivo `database.db` também não deve ser commitado.

## Executando

Na raiz do backend:

```bash
php -S localhost:9090 -t public
```

- O `-t public` é obrigatório: `public/` precisa ser a raiz do servidor, porque os modelos de e-mail e de certificado e os arquivos gerados são acessados por caminho relativo a essa pasta.
- A porta `9090` pode ser trocada. Nesse caso, atualize o `BACKEND_HOST` no `.env` e o endereço da API configurado no frontend.

### Verificando

Abra no navegador `http://localhost:9090/ping`. A resposta esperada é:

```json
{
  "status": "ok"
}
```

Isso confirma que a API está respondendo. Para validar também a conexão com o banco, faça login com o usuário abaixo.

## Login inicial

| E-mail | Senha |
|---|---|
| `admin@email.com` | `123123123` |

Esse usuário vem no dump do banco e serve **somente para ambiente local**. Troque a senha em qualquer outro ambiente.

## Estrutura de pastas

```
public/                   raiz do servidor web (ponto de entrada da API)
  layout-email/           modelos de e-mail: reset de senha e atualização de status
  layout-certificate/     modelo HTML do certificado e imagem de fundo
  tmp/docx/               documentos Word gerados em execução (não versionado)
  tmp/pdf/                PDFs gerados em execução (não versionado)
src/                      código-fonte (namespace MacoBackend\)
  Controllers/  Services/  Helpers/  Models/
.env.example              modelo das variáveis de ambiente
database.db.example       modelo da configuração de conexão
```

Os arquivos de `layout-email/` e `layout-certificate/` fazem parte do sistema e são versionados. Os de `tmp/` são gerados em execução. As pastas existem no repositório apenas por causa de um arquivo `.gitkeep`, e o conteúdo gerado é ignorado pelo Git.

## CORS

Por padrão, a API aceita requisições de qualquer origem (`Access-Control-Allow-Origin: *`). Isso é aceitável em desenvolvimento, mas **antes de publicar em produção a origem deve ser restrita ao frontend**.

Localize o ponto marcado no código:

```bash
git grep "FLAG: ALTERAR"
```

Troque o `'*'` pela origem exata do frontend. Uma opção é ler do `.env`:

```php
->withHeader('Access-Control-Allow-Origin', SystemHelper::getFrontendHost())
```

Observações:

- A origem deve ser idêntica à exibida no navegador: protocolo, domínio e porta, **sem barra no final**.
- Se o frontend enviar cookies ou credenciais (`withCredentials`), o `*` não funciona. Use a origem exata e inclua `Access-Control-Allow-Credentials: true`.
- Para mais de uma origem, compare o cabeçalho `Origin` da requisição com uma lista permitida e devolva apenas a origem que constar nela, junto com `Vary: Origin`.
- Cabeçalhos personalizados enviados pelo frontend precisam estar listados em `Access-Control-Allow-Headers`.

## Problemas comuns

| Sintoma | Causa provável |
|---|---|
| `Class ... not found` ou erro de `vendor/autoload.php` | Faltou rodar `composer install` |
| Erro de conexão com o banco | MySQL parado, banco não criado ou dados do `database.db` incorretos |
| `composer install` falha ao baixar o `simpledb` | Sem acesso ao repositório `JoaoCordeir0/simpledb` |
| Reset de senha ou aviso de status não chega | SMTP do `.env` não configurado ou incorreto |
| Certificado ou e-mail não são gerados | Servidor iniciado sem `-t public` |
| Porta em uso | Troque a porta e atualize `BACKEND_HOST` e o frontend |
| Erros de CORS no navegador | Veja a seção [CORS](#cors) |

## Manutenção das dependências

Rode `composer audit` periodicamente. Em outubro de 2026, o relatório apontava avisos de segurança em três pacotes e o pacote `tuupola/slim-jwt-auth` estava abandonado (substituto sugerido: `jimtools/jwt-auth`). Atualize as dependências antes de qualquer uso em produção.