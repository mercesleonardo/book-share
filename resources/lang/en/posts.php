<?php

return [
    'title'  => 'Posts',
    'single' => 'Post',
    'fields' => [
        'title'        => 'Title',
        'description'  => 'Description',
        'slug'         => 'Slug',
        'category'     => 'Category',
        'author'       => 'Author (user)', // legacy internal (if still referenced)
        'book_author'  => 'Book Author',
        'image'        => 'Image',
        'user'         => 'User',
    ],
    'actions' => [
        'create' => 'Create Post',
        'edit'   => 'Edit Post',
        'delete' => 'Delete Post',
        'back'   => 'Back',
        'view'   => 'View Post',
    ],
    'messages' => [
        'created'        => 'Post created successfully.',
        'updated'        => 'Post updated successfully.',
        'deleted'        => 'Post deleted successfully.',
        'not_found'      => 'No posts found.',
        'confirm_delete' => 'Are you sure you want to delete this post? This action cannot be undone.',
    ],
    'filters' => [
        'all'           => 'All',
        'filter'        => 'Filter',
        'reset'         => 'Reset',
        'search_ph'     => 'Title or book author',
        'search'        => 'Search',
        'author_ph'     => 'Book author name',
        'remove_filter' => 'Remove filter',
        'clear_all'     => 'Clear all',
    ],
    'meta' => [
        'info'    => 'Info',
        'created' => 'Created',
        'updated' => 'Updated',
        'status'  => 'Status',
    ],
    'navigation' => [
        'previous' => 'Previous Post',
        'next'     => 'Next Post',
    ],
    'related' => [
        'title' => 'Related Posts',
        'none'  => 'No related posts yet.',
    ],
];
