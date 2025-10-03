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

## 📄 Licença

Projeto desenvolvido para fins de portfólio. A licença poderá ser definida futuramente.

## 👤 Autor

Feito por Leonardo Merces como parte do portfólio profissional. Entre em contato para feedbacks, sugestões ou oportunidades.

