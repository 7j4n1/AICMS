<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MemberApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test admin user and get JWT token
        $this->admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password')
        ]);
        
        // Get JWT token
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'testadmin',
            'password' => 'password'
        ]);
        
        $this->token = $response->json('access_token');
    }

    public function test_can_list_members()
    {
        Member::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/members');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data',
                    'pagination'
                ]
            ]);
    }

    public function test_can_create_member()
    {
        $memberData = [
            'coopId' => '12345',
            'surname' => 'Doe',
            'otherNames' => 'John',
            'occupation' => 'Engineer',
            'gender' => 'Male',
            'phoneNumber' => '1234567890'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/members', $memberData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'coopId',
                    'surname',
                    'otherNames'
                ]
            ]);

        $this->assertDatabaseHas('members', [
            'coopId' => '12345',
            'surname' => 'Doe'
        ]);
    }

    public function test_can_get_member_by_id()
    {
        $member = Member::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/members/' . $member->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $member->id,
                    'coopId' => $member->coopId
                ]
            ]);
    }

    public function test_can_update_member()
    {
        $member = Member::factory()->create();

        $updateData = [
            'surname' => 'Updated Surname',
            'phoneNumber' => '9876543210'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/v1/members/' . $member->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'surname' => 'Updated Surname'
                ]
            ]);
    }

    public function test_can_delete_member()
    {
        $member = Member::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson('/api/v1/members/' . $member->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('members', [
            'id' => $member->id
        ]);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/v1/members');

        $response->assertStatus(401);
    }

    public function test_validates_required_fields_on_create()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/members', [
                'coopId' => '12345'
                // Missing required fields
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['surname', 'otherNames']);
    }
}
