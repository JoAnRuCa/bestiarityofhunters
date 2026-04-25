<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Build;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function a_user_can_create_a_build()
    {
        $this->actingAs($this->user);

        $buildData = [
            'weapon1' => ['id' => 4],
            'head'    => ['id' => 11],
            'chest'   => ['id' => 17],
            'arms'    => ['id' => 18],
            'waist'   => ['id' => 19],
            'legs'    => ['id' => 20],
            'charm'   => ['id' => 4],
        ];

        $response = $this->post(route('builds.store'), [
            'titulo' => 'Greatsword of the Heavens',
            'playstyle' => 'Aggressive hunting with high mobility.',
            'build_data' => json_encode($buildData),
            'decorations_data' => json_encode([]),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('builds', [
            'titulo' => 'Greatsword of the Heavens',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function a_user_can_edit_their_own_build()
    {
        $this->actingAs($this->user);

        $build = Build::create([
            'titulo' => 'Old Build',
            'playstyle' => 'Old style',
            'user_id' => $this->user->id,
            'slug' => 'old-build'
        ]);

        $buildData = [
            'weapon1' => ['id' => 4],
            'head'    => ['id' => 11],
            'chest'   => ['id' => 17],
            'arms'    => ['id' => 18],
            'waist'   => ['id' => 19],
            'legs'    => ['id' => 20],
            'charm'   => ['id' => 4],
        ];

        $response = $this->put(route('builds.update', $build->slug), [
            'titulo' => 'Updated Build Title',
            'playstyle' => 'Updated playstyle',
            'build_data' => json_encode($buildData),
            'decorations_data' => json_encode([]),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('builds', [
            'id' => $build->id,
            'titulo' => 'Updated Build Title',
        ]);
    }

    /** @test */
    public function a_user_cannot_edit_someone_elses_build()
    {
        $otherUser = User::factory()->create();
        $build = Build::create([
            'titulo' => 'Other Hunter Build',
            'playstyle' => 'Private tactics',
            'user_id' => $otherUser->id,
            'slug' => 'other-build'
        ]);

        $this->actingAs($this->user);

        $buildData = [
            'weapon1' => ['id' => 4],
            'head'    => ['id' => 11],
            'chest'   => ['id' => 17],
            'arms'    => ['id' => 18],
            'waist'   => ['id' => 19],
            'legs'    => ['id' => 20],
            'charm'   => ['id' => 4],
        ];

        $response = $this->put(route('builds.update', $build->slug), [
            'titulo' => 'I try to hack this',
            'build_data' => json_encode($buildData),
            'decorations_data' => json_encode([]),
        ]);

        $response->assertStatus(403); // Forbidden
    }
}
