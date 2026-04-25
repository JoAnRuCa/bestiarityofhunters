<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Build;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_access_the_admin_panel()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(302); // Should redirect to login or home
    }

    /** @test */
    public function a_regular_user_cannot_access_the_admin_panel()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Access denied');
    }

    /** @test */
    public function an_admin_can_access_the_admin_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function a_user_cannot_delete_someone_elses_build()
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        
        $build = Build::create([
            'titulo' => 'Other Hunter Build',
            'user_id' => $otherUser->id,
            'slug' => 'other-build'
        ]);

        $this->actingAs($user);

        $response = $this->delete(route('builds.destroy', $build->slug));
        $response->assertStatus(403);
    }

    /** @test */
    public function an_admin_can_delete_any_build()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        
        $build = Build::create([
            'titulo' => 'Hunter Build',
            'user_id' => $user->id,
            'slug' => 'hunter-build'
        ]);

        $this->actingAs($admin);

        $response = $this->delete(route('builds.destroy', $build->slug));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('builds', ['id' => $build->id]);
    }
}
