<?php

return [
    'title'  => 'Posts',
    'single' => 'Post',
    'fields' => [
        'title'       => 'Title',
        'description' => 'Description',
        'slug'        => 'Slug',
        'category'    => 'Category',
        'author'      => 'Author',
        // Legacy key retained temporarily for backward compatibility
        'image_url' => 'Image URL',
        'image'     => 'Image',
    ],
    'actions' => [
        'create' => 'Create Post',
        'edit'   => 'Edit Post',
        'delete' => 'Delete Post',
    ],
    'messages' => [
        'created'        => 'Post created successfully.',
        'updated'        => 'Post updated successfully.',
        'deleted'        => 'Post deleted successfully.',
        'not_found'      => 'No posts found.',
        'confirm_delete' => 'Are you sure?',
    ],
];
