<?php

return [
    'title'  => 'Categories',
    'single' => 'Category',
    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
    ],
    'actions' => [
        'create' => 'Create Category',
        'edit'   => 'Edit Category',
        'delete' => 'Delete Category',
    ],
    'messages' => [
        'created'        => 'Category created successfully.',
        'updated'        => 'Category updated successfully.',
        'deleted'        => 'Category deleted successfully.',
        'not_found'      => 'No categories found.',
        'confirm_delete' => 'Are you sure you want to delete this category? This action cannot be undone.',
    ],
    'filters' => [
        'filter' => 'Filter',
        'all'    => 'All',
        'reset'  => 'Reset',
    ],
];
