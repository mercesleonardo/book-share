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
        // Chave legada mantida temporariamente
        'image_url' => 'URL da Imagem',
        'image'     => 'Imagem',
    ],
    'actions' => [
        'create' => 'Criar Post',
        'edit'   => 'Editar Post',
        'delete' => 'Excluir Post',
    ],
    'messages' => [
        'created'        => 'Post criado com sucesso.',
        'updated'        => 'Post atualizado com sucesso.',
        'deleted'        => 'Post excluído com sucesso.',
        'not_found'      => 'Nenhum post encontrado.',
        'confirm_delete' => 'Tem certeza?',
    ],
];
