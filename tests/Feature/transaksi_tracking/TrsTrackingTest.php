<?php

namespace Tests\Feature\transaksi_tracking;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\MsDriver;

class TrsTrackingTest extends TestCase
{
    // use DatabaseMigrations, DatabaseTransactions;
    var $url='localhost:8000';
    var $id='';
    var $id_dispatch='K-08202210030001';
    protected $token;
    public function setUp(): void
    {
        parent::setUp();

        // Login first with another user account to grant access create new drivers
        $login =$this->loginUserDriver('mahmud@gmail.com','123456');
        $dataLogin= $login->json();
        $this->token=$dataLogin['token'];

        DB::statement("ALTER TABLE trs_tracking_trucks AUTO_INCREMENT = 1"); // reset auto increment
        $lastId = DB::table('trs_tracking_trucks')->max('id');
        $this->id=$lastId;
    }


    public function test_create_data_tracking()
    {
    // Create a new user through the REST API
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post('/api/v1/trs_truck_tracking', [
            'id_dispatch' => $this->id_dispatch,
            'id_tracking' =>  '2',
            'tracking_date' => '2023-01-01 12:00:00',
            'title' => 'Selesai Pekerjaan Trucking',
            'description' => 'lorem ipsum',
            'attachment' => 'lorem ipsum.jpg',
            'is_done' => 1,
            'is_active' => 1,
        ]);
        // echo json_encode($response);
        $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id_dispatch',
                'id_tracking',
                'tracking_date',
            ],
            'message'
        ]);

    }


    public function test_get_detail_tracking_has_created(): void
    {
        $id= (int)$this->id +1;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/trs_truck_tracking/1');

        $response->assertStatus(200) // Expect a 201 status code
        ->assertJsonStructure([
            'data' => [
                'id',
                'id_dispatch',
                'id_tracking',
                'tracking_date'
            ],
        ]);
    }

    public function test_get_all_master_tracking_has_created(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/trs_truck_tracking?page=1&per_page=10&order_by=id&order_direction=asc');

        $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'id_dispatch',
                    'id_tracking',
                    'tracking_date',
                    'title',
                    'description',
                    'attachment',
                    'id_done',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ],
            'page',
            'per_page'
        ]);
    }


    public function test_update_tracking_has_created(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->put('/api/v1/trs_truck_tracking/1', [
            'id_dispatch' => $this->id_dispatch,
            'id_tracking' => 2,
            'tracking_date' => '2023-01-01 12:00:00',
            'title' => 'melakukan update Selesai Pekerjaan Trucking',
            'description' => 'lorem ipsum',
            'attachment' => 'lorem ipsum.jpg',
            'is_done' => 1,
            'is_active' =>1,
        ]);

    //    echo json_encode($response, JSON_PRETTY_PRINT);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'id_dispatch',
                    'id_tracking',
                    'title',
                ],
                'message'
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
        public function test_finally_delete_data_created_testing_for_clean_table_trs(): void
        {

            $lastId = DB::table('trs_tracking_trucks')->max('id');

            $response = $this->withHeaders([
                        'Authorization' => 'Bearer ' . $this->token,
                    ])->delete('/api/v1/trs_truck_tracking/'.$lastId);

            $response->assertStatus(200);
        }


}
