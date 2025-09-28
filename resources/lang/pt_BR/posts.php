<?php

return [
    'title'  => 'Publicações',
    'single' => 'Publicação',
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descrição',
        'slug'        => 'Slug',
        'category'    => 'Categoria',
        'author'      => 'Autor do Livro',
        'image'       => 'Imagem',
        'user'        => 'Usuário',
    ],
    'actions' => [
        'create' => 'Criar Publicação',
        'edit'   => 'Editar',
        'delete' => 'Excluir',
        'back'   => 'Voltar',
        'view'   => 'Ver',
    ],
    'intro' => [
        'create_hint' => 'Cadastre aqui o seu livro preferido e compartilhe com a comunidade.',
    ],
    'messages' => [
        'created'        => 'Publicação criada com sucesso.',
        'updated'        => 'Publicação atualizada com sucesso.',
        'deleted'        => 'Publicação excluída com sucesso.',
        'not_found'      => 'Nenhuma publicação encontrada.',
        'confirm_delete' => 'Tem certeza que deseja excluir esta publicação? Esta ação não pode ser desfeita.',
    ],
    'filters' => [
        'all'           => 'Todos',
        'filter'        => 'Filtrar',
        'reset'         => 'Limpar',
        'search_ph'     => 'Título ou autor do livro',
        'search'        => 'Busca',
        'author_ph'     => 'Nome do autor do livro',
        'remove_filter' => 'Remover filtro',
        'clear_all'     => 'Limpar todos',
    ],
    'meta' => [
        'info'    => 'Info',
        'created' => 'Criado',
        'updated' => 'Atualizado',
        'status'  => 'Status',
    ],
    'navigation' => [
        'previous' => 'Publicação Anterior',
        'next'     => 'Próxima Publicação',
    ],
    'related' => [
        'title' => 'Publicações Relacionadas',
        'none'  => 'Nenhuma publicação relacionada ainda.',
    ],
];
