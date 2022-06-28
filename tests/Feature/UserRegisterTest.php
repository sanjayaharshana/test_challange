<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use WithFaker;

    public function test_user_register()
    {
        $response = $this->json('post', '/api/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'sfsdfwerwer'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
        ]);
    }

    public function test_user_dublicate_validation(){
        $this->json('post', '/api/register', [
            'name' => $this->faker->name,
            'email' => 'helloworld@yopmail.com',
            'password' => 'sfsdfwerwer'
        ]);
        $response = $this->json('post', '/api/register', [
            'name' => $this->faker->name,
            'email' => 'helloworld@yopmail.com',
            'password' => 'sfsdfwerwer'
        ]);
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'error',
            'message' => [
                'email'
            ],
        ]);
    }

    public function test_user_login_validation(){
        $this->json('post', '/api/register', [
            'name' => 'Sanjaya Senevirathne',
            'email' => 'helloworld@yopmail.com',
            'password' => 'sfsdfwerwer'
        ]);
        $response = $this->json('post', '/api/login', [
            'email' => 'helloworld@yopmail.com',
            'password' => 'sfsdfwerwer'
        ]);
        $response->assertJsonStructure([
            'access_token',
            'token_type'
        ]);
        $response->assertStatus(200);
    }

}
