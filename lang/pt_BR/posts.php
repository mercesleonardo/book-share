<?php

return [
    'title'  => 'Posts',
    'single' => 'Post',
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
        'create' => 'Criar Post',
        'edit'   => 'Editar Post',
        'delete' => 'Excluir Post',
        'back'   => 'Voltar',
        'view'   => 'Ver Post',
    ],
    'messages' => [
        'created'        => 'Post criado com sucesso.',
        'updated'        => 'Post atualizado com sucesso.',
        'deleted'        => 'Post excluído com sucesso.',
        'not_found'      => 'Nenhum post encontrado.',
        'confirm_delete' => 'Tem certeza que deseja excluir este post? Esta ação não pode ser desfeita.',
    ],
    'filters' => [
        'all'       => 'Todos',
        'filter'    => 'Filtrar',
        'reset'     => 'Limpar',
        'search_ph' => 'Título ou autor',
        'search'    => 'Busca',
    ],
];
