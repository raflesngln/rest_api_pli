<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class UserApiTest extends TestCase
{
    var $url='localhost:8000';
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }



//     public function testGetAllUsers()
// {
//     $response = $this->get('/api/users');

//     $response->assertStatus(200);
//     $response->assertJsonStructure([
//         '*' => ['id', 'name', 'email']
//     ]);
// }

// public function testGetSingleUser()
// {
//     $user = factory(User::class)->create();

//     $response = $this->get('/api/users/' . $user->id);

//     $response->assertStatus(200);
//     $response->assertJson([
//         'id' => $user->id,
//         'name' => $user->name,
//         'email' => $user->email
//     ]);
// }


}
