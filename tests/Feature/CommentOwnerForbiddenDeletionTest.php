<?php

namespace Tests\Feature;

use App\Models\{Comment, Post, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentOwnerForbiddenDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_owner_cannot_delete_foreign_comment_and_gets_403(): void
    {
        $owner   = User::factory()->create();
        $other   = User::factory()->create();
        $post    = Post::factory()->for($owner)->create();
        $comment = Comment::factory()->for($post)->for($other)->create();

        /** @var User $owner */
        $response = $this->actingAs($owner)->delete(route('admin.comments.destroy', $comment));
        $response->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }
}
