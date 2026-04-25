<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Build;
use App\Models\BuildComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $build;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->build = Build::create([
            'titulo' => 'Test Build',
            'user_id' => $this->user->id,
            'slug' => 'test-build'
        ]);
    }

    /** @test */
    public function a_user_can_vote_on_a_build()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('votar'), [
            'id' => $this->build->id,
            'tipo' => 1,
            'model' => 'build'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['voto' => 1]);

        $this->assertDatabaseHas('builds_votes', [
            'user_id' => $this->user->id,
            'build_id' => $this->build->id,
            'tipo' => 1
        ]);
    }

    /** @test */
    public function a_user_can_comment_on_a_build()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('comments.store'), [
            'item_id' => $this->build->id,
            'comentario' => 'Excellent build, hunter!',
            'type' => 'build'
        ]);

        $response->assertStatus(302); // Redirect back
        $this->assertDatabaseHas('builds_comments', [
            'user_id' => $this->user->id,
            'build_id' => $this->build->id,
            'comentario' => 'Excellent build, hunter!'
        ]);
    }

    /** @test */
    public function a_user_can_delete_their_own_comment()
    {
        $comment = BuildComment::create([
            'user_id' => $this->user->id,
            'build_id' => $this->build->id,
            'comentario' => 'I will delete this'
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('comments.soft-delete', $comment->id), [
            'type' => 'build'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('builds_comments', [
            'id' => $comment->id,
            'comentario' => 'This text has been deleted'
        ]);
    }
}
