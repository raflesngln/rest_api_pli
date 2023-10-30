<?php

namespace Tests\Feature\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    var $url='localhost:8000';
    /**
     * A basic feature test example.
     */
    public function test_login_must_be_successfull(): void
    {
        // Attempt to log in
        $response = $this->post('/api/v1/login', [
            'email' => 'mahmud@gmail.com',
            'password' => '123456',
        ]);
        // Assert that the login was successful
        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'driver_no',
                    'driver_name',
                    'email',
                ],
                'token'
            ]);
    }
    public function test_login_must_be_not_valid(): void
    {
        $response = $this->post('/api/v1/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ]);

        // Assert that the login failed
        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Not Authenticated' // Match the actual response case
            ]);
    }


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

// Add more test methods for other API endpoints like create, update, delete, etc.

}



// beforeEach(function () {
//     // This function will be executed before each test.
//     // You can use it to set up your testing environment.
//     // For example, migrate your database.
//     // Use RefreshDatabase to automatically reset the database after each test.
//     useRefreshDatabase();
// });


// it('can log in with valid credentials', function () {
//     // Create a user with valid credentials
//     // $user = User::factory()->create([
//     //     'email' => 'mahmud@gmail.com',
//     //     'password' => bcrypt('123456'),
//     // ]);

//     // Attempt to log in
//     $response = $this->post('/api/v1/login', [
//         'email' => 'mahmud@gmail.com',
//         'password' => '123456',
//     ]);

//     // Assert that the login was successful
//     $response
//         ->assertStatus(201);

// });

// it('cannot log in with invalid credentials', function () {
//     // Attempt to log in with invalid credentials
//     $response = $this->post('/api/v1/login', [
//         'email' => 'invalid@example.com',
//         'password' => 'invalidpassword',
//     ]);

//     // Assert that the login failed
//     $response
//         ->assertStatus(401)
//         ->assertJson([
//             'message' => 'Not Authenticated' // Match the actual response case
//         ]);
// });
