# Frontend

Interface web do projeto MACO, feita em **Vue 3** com TypeScript, [Vite](https://vitejs.dev/), Tailwind CSS, Vue Router, Axios, SweetAlert2 e Chart.js. Consome a API REST do backend.

> Ordem de subida do projeto completo: **banco de dados → backend → frontend**.
> O backend precisa estar rodando para o frontend funcionar. Veja a documentação do backend (`docs/backend.md`, no repositório do backend).

## Requisitos

| Item | Versão |
|---|---|
| Node.js | 22 (desenvolvido com 22.17.0) |
| npm | 10 (desenvolvido com 10.9.2) |
| Backend | rodando e acessível pelo navegador |

## Instalação

```bash
git clone <URL-DO-REPOSITORIO>
cd <PASTA-DO-PROJETO>
npm install
```

## Configuração

Crie o `.env` a partir do exemplo:

```powershell
Copy-Item .env.example .env     # Windows (PowerShell)
```

```bash
cp .env.example .env            # Linux / macOS
```

| Variável | Descrição |
|---|---|
| `VITE_URL_API` | URL do backend, com protocolo (ex.: `http://localhost:9090`) |

Observações:

- A URL deve ser a mesma em que o backend está rodando e precisa estar acessível **pelo navegador** de quem usa o sistema.
- O Vite lê o `.env` apenas ao iniciar. **Depois de alterá-lo, reinicie** o `npm run dev`.
- No build de produção, o valor é gravado dentro dos arquivos gerados. Para trocá-lo, rode o build de novo.
- O `.env` **não deve ser commitado** (já está no `.gitignore`).
- No `.env` do backend, o `FRONTEND_HOST` deve ser a URL em que o frontend abre no navegador, e o CORS do backend deve liberar essa origem. Veja a seção de CORS na documentação do backend.

## Executando (desenvolvimento)

```bash
npm run dev
```

O terminal mostra o endereço em que o frontend abriu (`Local: ...`). No Vite 2, o padrão é `http://localhost:3000`. Abra esse endereço no navegador e faça login.

### Login inicial

| E-mail | Senha |
|---|---|
| `admin@email.com` | `123123123` |

Esse usuário vem no dump do banco e serve **somente para ambiente local**.

## Scripts

| Comando | O que faz |
|---|---|
| `npm run dev` | Servidor de desenvolvimento com recarga automática |
| `npm run build` | Gera a versão de produção na pasta `dist/` |
| `npm run build:debug` | Mesmo que o `build`, com mensagens detalhadas, útil para investigar erros de build |

## Build de produção

```bash
npm run build
```

Os arquivos gerados ficam em `dist/` e podem ser servidos por qualquer servidor de arquivos estáticos (Nginx, Apache, Netlify, etc.). Para testar a build localmente:

```bash
npx vite preview
```

Pontos de atenção:

- O frontend é uma aplicação de página única (SPA) com Vue Router. O servidor deve devolver o `index.html` para endereços que não correspondam a um arquivo, senão recarregar a página em uma rota interna resulta em erro 404.
- Defina o `VITE_URL_API` no `.env` **antes** de rodar o build.
- O backend precisa liberar a origem do frontend no CORS. Não deixe `*` em produção.

## Estrutura de pastas

```
public/              arquivos estáticos
src/
  components/        componentes e layouts (DashboardLayout, EmptyLayout, ...)
  hooks/             chamadas à API (useAuth, useCourse, useEvent, useSchooling, ...)
  router/            rotas do frontend
  assets/            estilos (main.css)
  App.vue, main.ts   ponto de entrada da aplicação
.env.example         modelo das variáveis de ambiente
```

A URL da API é lida do `VITE_URL_API` em `src/hooks/useAuth.ts` e usada nas chamadas feitas com o Axios.

## Problemas comuns

| Sintoma | Causa provável |
|---|---|
| A tela abre, mas as requisições falham (`Network Error`) | Backend parado ou `VITE_URL_API` vazia ou incorreta. Confira a aba *Network* do navegador |
| Erro de CORS no console do navegador | A origem do frontend não está liberada no backend. Veja a seção de CORS na documentação do backend |
| Alterei o `.env` e nada mudou | O Vite só lê o `.env` ao iniciar. Reinicie o `npm run dev` |
| O frontend abriu em uma porta diferente da esperada | A porta padrão estava ocupada e o Vite escolheu outra. Atualize o `FRONTEND_HOST` e o CORS do backend |
| `npm install` falha | Confira a versão do Node (`node -v`) e apague a pasta `node_modules` antes de tentar de novo |
| Login retorna erro | Confirme que o banco foi importado e o backend responde em `/ping` |

## Manutenção das dependências

O projeto usa o **Vite 2**, que não recebe mais atualizações de segurança. Use o servidor de desenvolvimento apenas em ambiente local e não o exponha em redes públicas sem necessidade. Rode `npm audit` periodicamente e planeje a atualização do Vite e do `@vitejs/plugin-vue` antes de qualquer uso em produção.