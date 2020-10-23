<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Post;
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
        // Like an client make a request to store
        $response = $this->json('POST', '/api/posts', [
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
        // Sending a request without title
        $response = $this->json('POST', '/api/posts', [
            'title' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_show()
    {
        // Creating a dummi post
        $post = Post::factory()->create();
        // Getting the dummi post
        $response = $this->json('GET', "/api/posts/$post->id");
        
        // Testing
        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200);
    }

    public function test_404_show()
    {
        // Getting the dummi post that does not exists
        $response = $this->json('GET', '/api/posts/1000');
        
        // Testing
        $response->assertStatus(404);
    }
}
