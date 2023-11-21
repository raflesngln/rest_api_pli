<?php

namespace Tests\Feature\job_dispatch;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\JobsDispacth;

class JobDispatchTest extends TestCase
{

    protected $token;

    public function setUp(): void
    {
        parent::setUp();
        // Login first with another user account to grant access create new drivers
        $login =$this->loginUserDriver('mahmud@gmail.com','123456');
        $dataLogin= $login->json();
        $this->token=$dataLogin['token'];
    }

    public function test_get_all_dispatch_fcl_paging(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('api/v1/job_dispatch_fcl?page=1&per_page=10&order_by=id&order_direction=asc');

        // echo json_encode($response);
        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'page',
            'per_page',
        ]);
    }
    public function test_get_all_dispatch_lcl_paging(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('api/v1/job_dispatch_lcl?page=1&per_page=10&order_by=id&order_direction=asc');

        // echo json_encode($response);
        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'page',
            'per_page',
        ]);
    }
    public function test_get_dispatch_fcl_detail(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('api/v1/job_dispatch_fcl/10');

        // echo json_encode($response);
        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data',
        ]);
    }
    public function test_get_dispatch_lcl_detail(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('api/v1/job_dispatch_lcl/28');

        // echo json_encode($response);
        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data',
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
}
