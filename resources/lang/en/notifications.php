<?php

return [
    'greeting'  => 'Hello :name',
    'thanks'    => 'Thank you for using our application!',
    'view'      => 'View',
    'view_post' => 'View post',
    'password'  => [
        'setup_subject' => 'Set up your account password',
        'setup_line'    => 'An administrator created an account for you. Click the button below to define your password and activate access.',
        'setup_action'  => 'Define Password',
        'ignore'        => 'If you did not expect this email, you can ignore it.',
    ],
    'comments' => [
        'new_subject' => 'New comment on your post',
        'new_line'    => 'Your post ":title" received a new comment.',
    ],
    'moderation' => [
        'changed_subject' => 'Post status changed from :from to :to',
        'changed_line'    => 'Your post status was updated from :from to :to.',
    ],
];
