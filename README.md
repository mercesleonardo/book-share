<div align="center">

# Book Share

Descubra, avalie e compartilhe ótimos livros com a comunidade.

</div>

## 🎯 Objetivo do Projeto

Este projeto foi criado como portfólio para demonstrar domínio prático de Laravel moderno (v12), boas práticas de arquitetura, front-end com Blade + Tailwind + Alpine, testes automatizados e uma UX cuidadosa. A aplicação simula uma plataforma de compartilhamento e curadoria de livros, com foco em qualidade de código, acessibilidade e experiência do usuário.

## 📚 Descrição Detalhada

O Book Share permite que usuários:

- Cadastrem postagens sobre livros (título, autor do livro, descrição, imagem, etc.).
- Organizem conteúdo por categorias.
- Recebam moderação (aprovado/rejeitado) antes da publicação pública.
- Avaliem os livros (ratings) e comentem nas postagens.
- Acompanhem atualizações e notificações relevantes.
- Acessem um painel administrativo protegido para gerenciar usuários, categorias, postagens, comentários e moderação.

Destaques de UX/Frontend:

- Componente de destaques na home com slides automáticos das 5 últimas postagens aprovadas, construído em Blade + Alpine.js, com autoplay, pausa no hover, navegação por teclado, indicadores, e atributos de acessibilidade (ARIA) – priorizando uma experiência fluida e inclusiva.
- Layout responsivo com Tailwind CSS (suporte a dark mode).
- Componentização de elementos de interface (ex.: cards de livros, botões, navegação).

Boas práticas e arquitetura:

- Estrutura do Laravel 12 (bootstrap/app.php centraliza config de middleware/rotas, sem Kernel tradicional).
- Eloquent e relacionamentos com escopos úteis (`approved`, `byAuthor`).
- Enum para status de moderação (`App\Enums\ModerationStatus`).
- Policies para autorização, Form Requests para validação e Resources quando necessário.
- Testes de feature para fluxos críticos (ex.: exibição de últimos posts no carousel, filtros por status).
- Cache pontual e estratégias leves de otimização (eager loading nas consultas de listagem).

## ✨ Principais Funcionalidades

- Autenticação e registro de usuários (Laravel Breeze).

- Social login (Google) — users can sign in or register using their Google account. The project includes controllers, routes, views and tests for the Google Socialite flow.
- CRUD de categorias e postagens (com slug único, geração automática e atualização reativa ao título).
- Moderação de postagens com histórico de logs.
- Comentários e avaliações (ratings) por postagem.
- Página inicial pública com grid de livros e um carousel de destaques das últimas postagens aprovadas.
- Painel administrativo (área `/admin`) protegido para gestão de conteúdo.
- Notificações por eventos importantes.

## 🧰 Tecnologias & Ferramentas

- Backend
	- PHP 8.3
	- Laravel 12
	- Eloquent ORM, Migrations, Seeders e Factories
	- Laravel Breeze (autenticação)
	- Policies, Gates e Form Requests

- Frontend
	- Blade
	- Tailwind CSS 3
	- Alpine.js 3
	- Vite

- Qualidade & DX
	- PHPUnit 11 (testes)
	- Laravel Pint (formatação de código)
	- Laravel Sail (opcional para ambiente Docker)

## 🏗️ Estrutura e Padrões

- `app/Models` – Modelos Eloquent (ex.: `Post`, `Category`, `Rating`, `Comment`).
- `app/Enums` – Enums de domínio (ex.: `ModerationStatus`).
- `app/Http/Controllers` – Controladores HTTP públicos e administrativos.
- `app/Policies` – Autorização por recurso.
- `resources/views` – Views Blade, componentes e layout.
- `database/factories` e `database/seeders` – Geração de dados consistente para desenvolvimento e testes.

## 🚀 Executando Localmente (resumo)

Pré-requisitos (sem Docker): PHP 8.3+, Composer, Node 18+ e um banco compatível (ex.: MySQL ou SQLite).

1. Instalar dependências PHP e JS.
2. Copiar `.env` e configurar banco/queue/mail.
3. Gerar key, migrar e (opcional) popular dados com seeders.
4. Subir o servidor e o build do front (dev ou build de produção).

## 🔐 Autenticação social (Google)

Esta versão adiciona suporte a login/registro via conta Google usando Socialite. A implementação inclui controladores, rotas, um componente de botão social e testes de integração.

Passos rápidos para usar localmente:

1. Adicione as variáveis ao seu arquivo `.env`:

```bash
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT=https://your-app.test/auth/google/callback
```

2. Verifique que `config/services.php` contém as configurações do Google (já incluídas no repositório).

3. Rode a migration que adiciona as colunas sociais na tabela `users`:

```bash
php artisan migrate
```

4. As rotas de autenticação social são registradas (ex.: `/auth/google/redirect` e `/auth/google/callback`). Verifique `routes/web.php` para os detalhes.

5. Testes: há testes de feature em `tests/Feature/Auth/` cobrindo o fluxo e casos de conflito. Rode somente os testes relacionados com:

```bash
php artisan test --filter=GoogleSocialite
```

6. Formatação: rode o Pint se desejar antes de commitar:

```bash
vendor/bin/pint
```

Alternativa com Docker: utilizar Laravel Sail para subir serviços e app de forma padronizada.

> Observação: caso os assets não apareçam no navegador, rode o processo do Vite para compilar Tailwind/JS.

## 🧪 Testes

- Testes focados em features principais (ex.: exibição das 5 últimas postagens aprovadas na home).
- Utilize filtros para rodar somente os testes relevantes durante o desenvolvimento.

## 🧹 Qualidade de Código

- Padronização com Laravel Pint. Recomenda-se rodar o formatador antes de commitar alterações.

## 🗺️ Roadmap (ideias futuras)

- Busca avançada por título/autor/categoria.
- Perfis públicos de usuários e página de autor com suas postagens.
- Upload/transformação de imagens com otimização (thumbs/responsive).
- Filtragem/ordenação na home e paginação infinita.
- A/B testing de layout do carousel e métricas de engajamento.

## � Moderação automática (Jobs & OpenAI)

Este projeto inclui uma implementação de moderação automática de descrições de postagens usando um Job enfileirado que chama a API de moderação da OpenAI.

Resumo (contrato):

- Entrada: uma instância de `App\Models\Post` (normalmente com `description` e `user`).
- Saída: atualização do campo `moderation_status` do Post para `Approved` ou `Rejected`, criação de um registro em `moderation_logs` e notificação ao autor quando o status mudar.
- Erros: falhas na chamada à API são registradas em logs e o Job é re-tentado de acordo com o `backoff()` definido.

Arquivos principais:

- `app/Jobs/ModeratePostJob.php` — Job que implementa `ShouldQueue`, chama o serviço de moderação, persiste o resultado e notifica o autor quando houver mudança de status.
- `app/Services/Moderation/OpenAIModerationService.php` — Serviço que faz a chamada HTTP para o endpoint de moderação da OpenAI (`/v1/moderations`, modelo `omni-moderation-latest`).
- `app/Services/Moderation/ModerationService.php` — Serviço auxiliar usado para mudanças manuais de status (persistência do `ModerationLog`, notificação e limpeza de cache).
- `app/Notifications/PostModerationStatusChanged.php` — Notificação enviada ao autor quando o status muda.

Fluxo (alto nível):

1. Quando uma postagem precisa ser moderada, o Job `ModeratePostJob` é despachado com a instância do `Post`.
2. O Job chama `OpenAIModerationService::moderate($post->description)`.
3. Se a resposta indicar conteúdo seguro, o status é definido como `Approved`, caso contrário `Rejected`.
4. O Post é atualizado, um registro em `moderation_logs` é criado (quando aplicável) e o autor é notificado via `PostModerationStatusChanged` quando o status muda.

Observações técnicas relevantes:

- A verificação de segurança usa o campo `results[0].flagged` retornado pela API da OpenAI — se `flagged` for true, a postagem é considerada insegura.
- O `ModeratePostJob` define `backoff()` com os valores `[10, 30, 60, 120, 300]`, então a fila respeitará essas pausas entre tentativas em caso de exceção.
- Em falhas de comunicação o serviço registra no log (`Log::error(...)`) e o Job lança para acionar o mecanismo de retry do Laravel.

Variáveis de ambiente e configuração:

- Defina a chave da OpenAI em `.env`:

```bash
OPENAI_API_KEY=sk_xxx
```

- Verifique que `config/services.php` possui a entrada `openai.key` (o projeto já inclui essa chave mapeada para `env('OPENAI_API_KEY')`).
- Para execução de filas em desenvolvimento/produção, configure `QUEUE_CONNECTION` no `.env` (ex.: `database`, `redis`, `sync` para execução síncrona).

Como executar localmente / exemplos:

- Exemplo de despacho do Job em código (controller, observer ou event listener):

```php
use App\Jobs\ModeratePostJob;

// ao criar/atualizar a postagem
ModeratePostJob::dispatch($post);
```

- Rodar um worker de fila localmente (ex.: driver `database` ou `redis`):

```bash
php artisan queue:work --tries=3 --sleep=3
```

- Para testes rápidos sem worker, use o driver `sync` (executa o Job imediatamente).

Boas práticas e recomendações de testes:

- Nos testes, isole a chamada à OpenAI: utilize injeção de dependência e faça binding de um mock para `App\Services\Moderation\OpenAIModerationService` ou use `Http::fake()` para simular a resposta da API.
- Utilize `Notification::fake()` para assertar que `PostModerationStatusChanged` foi (ou não) enviada.
- Escreva ao menos um teste de integração do Job cobrindo ambos os cenários (aprovado/rejeitado) e um teste que garante que `ModerationLog` é criado quando o status é alterado manualmente via `ModerationService`.

Notas operacionais:

- Se preferir que a moderação ocorra imediatamente durante criação em ambientes de desenvolvimento, mantenha `QUEUE_CONNECTION=sync` — porém em produção recomenda-se usar `database`/`redis` e um worker dedicado.
- Caso precise de maior controle, crie um listener/observer que despache `ModeratePostJob` somente para posts com `moderation_status = Pending`.

## �📄 Licença

Projeto desenvolvido para fins de portfólio. A licença poderá ser definida futuramente.

## 👤 Autor

Feito por Leonardo Merces como parte do portfólio profissional. Entre em contato para feedbacks, sugestões ou oportunidades.

