<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Member;
use App\Models\LoanCapture;
use App\Models\ActiveLoans;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class LoanApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password')
        ]);
        
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'testadmin',
            'password' => 'password'
        ]);
        
        $this->token = $response->json('access_token');
    }

    public function test_can_list_loans()
    {
        $member = Member::factory()->create();
        LoanCapture::factory()->count(3)->create(['coopId' => $member->coopId]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/loans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta'
            ]);
    }

    public function test_can_create_loan()
    {
        $member = Member::factory()->create();

        $loanData = [
            'coopId' => $member->coopId,
            'loanAmount' => 50000,
            'loanDate' => now()->format('Y-m-d'),
            'guarantor1' => $member->coopId,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/loans', $loanData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'coopId',
                    'loanAmount'
                ]
            ]);

        $this->assertDatabaseHas('loan_captures', [
            'coopId' => $member->coopId,
            'loanAmount' => 50000
        ]);

        // Check if active loan was created
        $this->assertDatabaseHas('active_loans', [
            'coopId' => $member->coopId
        ]);
    }

    public function test_can_update_loan()
    {
        $member = Member::factory()->create();
        $loan = LoanCapture::factory()->create(['coopId' => $member->coopId]);
        ActiveLoans::create([
            'coopId' => $member->coopId,
            'loanAmount' => $loan->loanAmount,
            'loanPaid' => 0,
            'loanBalance' => $loan->loanAmount,
            'userId' => $this->admin->id,
            'loanDate' => $loan->loanDate,
            'repaymentDate' => $loan->repaymentDate,
            'lastPaymentDate' => now()
        ]);

        $updateData = [
            'loanAmount' => 75000,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/v1/loans/' . $loan->id, $updateData);

        $response->assertStatus(200);
    }

    public function test_can_get_active_loans()
    {
        $member = Member::factory()->create();
        ActiveLoans::create([
            'coopId' => $member->coopId,
            'loanAmount' => 50000,
            'loanPaid' => 10000,
            'loanBalance' => 40000,
            'userId' => $this->admin->id,
            'loanDate' => now(),
            'repaymentDate' => now()->addDays(540),
            'lastPaymentDate' => now()
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/active-loans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ]);
    }

    public function test_prevents_multiple_active_loans()
    {
        $member = Member::factory()->create();
        
        // Create first loan
        ActiveLoans::create([
            'coopId' => $member->coopId,
            'loanAmount' => 50000,
            'loanPaid' => 0,
            'loanBalance' => 50000,
            'userId' => $this->admin->id,
            'loanDate' => now(),
            'repaymentDate' => now()->addDays(540),
            'lastPaymentDate' => now()
        ]);

        // Try to create second loan
        $loanData = [
            'coopId' => $member->coopId,
            'loanAmount' => 30000,
            'loanDate' => now()->format('Y-m-d'),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/loans', $loanData);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Member already has an active loan'
            ]);
    }
}
