<?php

return [
    'title'  => 'Categorias',
    'single' => 'Categoria',
    'fields' => [
        'name' => 'Nome',
        'slug' => 'Slug',
    ],
    'actions' => [
        'create' => 'Criar Categoria',
        'edit'   => 'Editar Categoria',
        'delete' => 'Excluir Categoria',
    ],
    'messages' => [
        'created'        => 'Categoria criada com sucesso.',
        'updated'        => 'Categoria atualizada com sucesso.',
        'deleted'        => 'Categoria excluída com sucesso.',
        'not_found'      => 'Nenhuma categoria encontrada.',
        'confirm_delete' => 'Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita.',
    ]
];
