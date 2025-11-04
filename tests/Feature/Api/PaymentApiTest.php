<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Member;
use App\Models\PaymentCapture;
use App\Models\ActiveLoans;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PaymentApiTest extends TestCase
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

    public function test_can_list_payments()
    {
        $member = Member::factory()->create();
        PaymentCapture::factory()->count(5)->create(['coopId' => $member->coopId]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta'
            ]);
    }

    public function test_can_create_payment_with_savings()
    {
        $member = Member::factory()->create();

        $paymentData = [
            'coopId' => $member->coopId,
            'savingAmount' => 5000,
            'shareAmount' => 2000,
            'paymentDate' => now()->format('Y-m-d'),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/payments', $paymentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ]);

        $this->assertDatabaseHas('payment_captures', [
            'coopId' => $member->coopId,
            'savingAmount' => 5000,
            'shareAmount' => 2000
        ]);
    }

    public function test_can_create_payment_with_loan_repayment()
    {
        $member = Member::factory()->create();
        
        // Create an active loan
        $activeLoan = ActiveLoans::create([
            'coopId' => $member->coopId,
            'loanAmount' => 50000,
            'loanPaid' => 0,
            'loanBalance' => 50000,
            'userId' => $this->admin->id,
            'loanDate' => now(),
            'repaymentDate' => now()->addDays(540),
            'lastPaymentDate' => now()
        ]);

        $paymentData = [
            'coopId' => $member->coopId,
            'loanAmount' => 10000,
            'savingAmount' => 2000,
            'paymentDate' => now()->format('Y-m-d'),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/payments', $paymentData);

        $response->assertStatus(201);

        // Check if active loan was updated
        $activeLoan->refresh();
        $this->assertEquals(10000, $activeLoan->loanPaid);
        $this->assertEquals(40000, $activeLoan->loanBalance);
    }

    public function test_prevents_payment_without_active_loan()
    {
        $member = Member::factory()->create();

        $paymentData = [
            'coopId' => $member->coopId,
            'loanAmount' => 10000,
            'paymentDate' => now()->format('Y-m-d'),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/payments', $paymentData);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'No active loan found for this member'
            ]);
    }

    public function test_can_update_payment()
    {
        $member = Member::factory()->create();
        $payment = PaymentCapture::factory()->create([
            'coopId' => $member->coopId,
            'savingAmount' => 5000
        ]);

        $updateData = [
            'savingAmount' => 7000,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/v1/payments/' . $payment->id, $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payment_captures', [
            'id' => $payment->id,
            'savingAmount' => 7000
        ]);
    }

    public function test_can_delete_payment()
    {
        $member = Member::factory()->create();
        $payment = PaymentCapture::factory()->create(['coopId' => $member->coopId]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson('/api/v1/payments/' . $payment->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('payment_captures', [
            'id' => $payment->id
        ]);
    }

    public function test_can_filter_payments_by_coop_id()
    {
        $member1 = Member::factory()->create(['coopId' => '100']);
        $member2 = Member::factory()->create(['coopId' => '200']);
        
        PaymentCapture::factory()->count(3)->create(['coopId' => $member1->coopId]);
        PaymentCapture::factory()->count(2)->create(['coopId' => $member2->coopId]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/payments?coop_id=' . $member1->coopId);

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }
}
