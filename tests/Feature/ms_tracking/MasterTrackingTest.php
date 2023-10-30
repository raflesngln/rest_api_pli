<?php

namespace Tests\Feature\ms_tracking;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\MsDriver;

class MasterTrackingTest extends TestCase
{
    // use DatabaseMigrations, DatabaseTransactions;
    var $url='localhost:8000';
    var $id='10';
    var $sorting='10';
    protected $token;
    public function setUp(): void
    {
        parent::setUp();

        // Login first with another user account to grant access create new drivers
        $login =$this->loginUserDriver('mahmud@gmail.com','123456');
        $dataLogin= $login->json();
        $this->token=$dataLogin['token'];
    }


    public function test_get_all_master_tracking_has_created(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/ms_tracking', [
            'page' => 1,
            'per_page' => 10,
            'order_by' => 'id',
            'order_direction' => 'asc',
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'sorting',
                        'title',
                        'description',
                    ],
                ],
            ],
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
        // public function test_finally_delete_data_created_testing_for_clean_db(): void
        // {
        //     $driver_no=$this->id_driver;
        //     $response = $this->withHeaders([
        //                 'Authorization' => 'Bearer ' . $this->token,
        //             ])->delete('/api/v1/ms_driver/'.$driver_no);

        //     DB::statement("ALTER TABLE ms_drivers AUTO_INCREMENT = 1");  // reset column autoincrement to 1
        //     $response->assertStatus(204);
        // }


}
