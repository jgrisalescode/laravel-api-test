<?php

namespace Tests\Feature\Http\Controllers\Api;

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
    public function testStore()
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
}
