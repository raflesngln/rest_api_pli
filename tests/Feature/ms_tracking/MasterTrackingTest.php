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
    var $id='20';
    var $sorting='20';
    protected $token;
    public function setUp(): void
    {
        parent::setUp();

        // Login first with another user account to grant access create new drivers
        $login =$this->loginUserDriver('mahmud@gmail.com','123456');
        $dataLogin= $login->json();
        $this->token=$dataLogin['token'];
    }


    public function test_create_new_master_users()
    {
    // Create a new user through the REST API
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post('/api/v1/ms_tracking', [
            'sorting' =>  $this->sorting,
            'title' => 'SELESAI PEKERJAAN',
            'description' => 'Selesai Pekerjaan Trucking',
            'is_active' => 1,
            'is_deleted' => 0
        ]);


        // echo json_encode($response);
        $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'ms_tracking' => [
                'sorting',
                'title',
                'description',
            ],
            'message'
        ]);
    }


    public function test_get_detail_driver_has_created(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/ms_tracking/'.$this->id);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'sorting',
                    'title',
                    'description'
                ]
            ]);
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

        $response->assertStatus(200) // Expect a 200 status code
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'sorting',
                    'title',
                    'description',
                    'is_active',
                    'is_deleted',
                    'created_at',
                    'updated_at',
                ],
            ],
            'page',
            'per_page',
        ]);
    }


    public function test_update_master_has_created(): void
    {

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->put('/api/v1/ms_tracking/' . $this->id, [
            'sorting' => $this->sorting,
            'title' => 'SELESAI PEKERJAANSELESAI PEKERJAAN',
            'description' => 'SELESAI PEKERJAANSELESAI PEKERJAAN',
        ]);

        // $response->assertStatus(200);
        // echo json_decode($response->getContent());
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'sorting',
                    'title',
                    'description',
                ]
            ])
            ->assertJson([
                'message' => 'success update data'
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
            $response = $this->withHeaders([
                        'Authorization' => 'Bearer ' . $this->token,
                    ])->delete('/api/v1/ms_tracking/'.$this->sorting);

            DB::statement("ALTER TABLE ms_tracking_trucks AUTO_INCREMENT = 1");  // reset column autoincrement to 1
            $response->assertStatus(200);
        }


}
