<?php

return [
    'title'  => 'Publicações',
    'single' => 'Publicação',
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descrição',
        'slug'        => 'Slug',
        'category'    => 'Categoria',
        'author'      => 'Autor',
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
    'messages' => [
        'created'        => 'Publicação criada com sucesso.',
        'updated'        => 'Publicação atualizada com sucesso.',
        'deleted'        => 'Publicação excluída com sucesso.',
        'not_found'      => 'Nenhuma publicação encontrada.',
        'confirm_delete' => 'Tem certeza que deseja excluir esta publicação? Esta ação não pode ser desfeita.',
    ],
    'filters' => [
        'all'       => 'Todos',
        'filter'    => 'Filtrar',
        'reset'     => 'Limpar',
        'search_ph' => 'Título ou autor',
        'search'    => 'Busca',
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
