<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\MsDriver;

class AutheticationTest extends TestCase
{
    // use DatabaseMigrations, DatabaseTransactions;
    var $url='localhost:8000';
    var $id_driver='DRVR11223344556677889900';
    var $email='mawar@gmail.com';
    var $password='123456';
    protected $token;
    public function setUp(): void
    {
        parent::setUp();

        // Login first with another user account to grant access create new drivers
        $login =$this->loginUserDriver('mahmud@gmail.com','123456');
        $dataLogin= $login->json();
        $this->token=$dataLogin['token'];

        // For example, you can set up database transactions
        // $this->beginDatabaseTransaction();
    }

         public function test_create_new_driver_users()
        {
        // Create a new user through the REST API
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->post('/api/v1/ms_driver', [
                'driver_no' => $this->id_driver,
                'driver_contact_number1' => '',
                'driver_contact_number2' => '',
                'driver_name' => 'Mawar',
                'is_active' => 1,
                'is_deleted' => 0,
                'id' => '',
                'email' => $this->email,
                'create_by' => '',
                'vendor_id' => '',
                'password' => $this->password,
            ]);


            // echo json_encode($response);
            $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data'=>[
                    'driver' => [
                        'id',
                        'driver_no',
                        'driver_name',
                        'email',
                    ],
                    'message'
                ]]);
        }

        public function test_login_new_driver_created_mustbe_succeess(): void
        {
            $response = $this->loginUserDriver($this->email,$this->password);
            $response
            ->assertStatus(200)
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

    public function test_login_must_be_successfull(): void
    {
        // Attempt to log in
        $response = $this->loginUserDriver($this->email,$this->password);
        // Assert that the login was successful
        $response
            ->assertStatus(200)
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

    public function test_login_driver_must_be_not_valid(): void
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
    public function test_access_profile_has_logged(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/profile');

        $response = $this->get('/api/v1/profile');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'driver_no',
                    'driver_name',
                    'email',
                ],
            ]);
    }
    public function test_get_detail_driver_has_created(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/ms_driver/'.$this->id_driver);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'driver_no',
                    'driver_name',
                    'email',
                ],
            ]);
    }
    public function test_update_driver_has_created(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->put('/api/v1/ms_driver/'.$this->id_driver,[
            'driver_no' => $this->id_driver,
            'driver_contact_number1' => '',
            'driver_contact_number2' => '',
            'driver_name' => 'Mawar melati',
            'is_active' => 1,
            'is_deleted' => 0,
            'id' => '',
            'email' => $this->email,
            'create_by' => '',
            'vendor_id' => '',
            'password' =>'1234567890',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'driver_no',
                    'driver_name',
                    'email',
                ]
            ])
            ->assertJson([
                'message' => 'success update data'
            ]);
    }

    public function test_access_profile_with_error_token_invalid(): void
    {
        $response = $this->get('/api/v1/profile');
        $response
            ->assertStatus(200)
            ->assertJson([
                'user' =>null
            ]);
    }


    function loginUserDriver($username,$password){
        // Attempt to log in
        $response = $this->post('/api/v1/login', [
            'email' => $username,
            'password' => $password,
        ]);
        return $response;
    }

    // finally delete the user has created for clean database testing
        public function test_finally_delete_data_created_testing_for_clean_db(): void
    {
        $driver_no=$this->id_driver;
        $response = $this->withHeaders([
                     'Authorization' => 'Bearer ' . $this->token,
                ])->delete('/api/v1/ms_driver/'.$driver_no);

        DB::statement("ALTER TABLE ms_drivers AUTO_INCREMENT = 1");  // reset column autoincrement to 1
        $response->assertStatus(204);
    }

    public function tearDown(): void
    {
        // Optionally, you can perform cleanup after each test
        // parent::tearDown();
    }


}
