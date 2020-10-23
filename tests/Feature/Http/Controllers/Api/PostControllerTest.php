<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_store()
    {
        $user = User::factory()->create();
        // Like an client make a request to store
        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => 'Test title'
        ]);
        // We want that our systems returns this kind of JsonStructure
        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'Test title'])
            ->assertStatus(201); // OK and Created
        // We want to know if in the database we have saved the request
        $this->assertDatabaseHas('posts', ['title' => 'Test title']);
    }

    public function test_validate_title()
    {
        $user = User::factory()->create();
        // Sending a request without title
        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_show()
    {
        $user = User::factory()->create();
        // Creating a dummi post
        $post = Post::factory()->create();
        // Getting the dummi post
        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id");
        
        // Testing
        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200);
    }

    public function test_404_show()
    {
        $user = User::factory()->create();
        // Getting the dummi post that does not exists
        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts/1000');
        
        // Testing
        $response->assertStatus(404);
    }

    public function test_update()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user, 'api')->json('PUT', "/api/posts/$post->id", [
            'title' => 'Title updated'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'Title updated'])
            ->assertStatus(200);
        
        $this->assertDatabaseHas('posts', ['title' => 'Title updated']);
    }

    public function test_delete()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/posts/$post->id");

        $response->assertSee(null)
            ->assertStatus(204);
        
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {
        $user = User::factory()->create();
        $posts = Post::factory(5)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at']
            ]
            ])->assertStatus(200);
    }

    public function test_guess()
    {
        $response = $this->json('GET', '/api/posts')->assertStatus(401);
        $response = $this->json('POST', '/api/posts')->assertStatus(401);
        $response = $this->json('GET', '/api/posts/1000')->assertStatus(401);
        $response = $this->json('PUT', '/api/posts/1000')->assertStatus(401);
        $response = $this->json('DELETE', '/api/posts/1000')->assertStatus(401);
    }
}
