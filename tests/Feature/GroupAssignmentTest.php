<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that groupId is calculated correctly for different coopIds.
     */
    public function test_group_id_calculation(): void
    {
        $this->assertEquals(1, Member::calculateGroupId(1));
        $this->assertEquals(1, Member::calculateGroupId(50));
        $this->assertEquals(1, Member::calculateGroupId(100));
        $this->assertEquals(2, Member::calculateGroupId(101));
        $this->assertEquals(2, Member::calculateGroupId(150));
        $this->assertEquals(2, Member::calculateGroupId(200));
        $this->assertEquals(3, Member::calculateGroupId(201));
        $this->assertEquals(10, Member::calculateGroupId(1000));
    }

    /**
     * Test that groupId is automatically assigned when creating a member.
     */
    public function test_group_id_auto_assignment_on_create(): void
    {
        $member = Member::create([
            'coopId' => 150,
            'surname' => 'Test',
        ]);

        $this->assertEquals(2, $member->groupId);
    }

    /**
     * Test that groupId is updated when coopId changes.
     */
    public function test_group_id_updates_when_coop_id_changes(): void
    {
        $member = Member::create([
            'coopId' => 50,
            'surname' => 'Test',
        ]);

        $this->assertEquals(1, $member->groupId);

        $member->coopId = 250;
        $member->save();

        $this->assertEquals(3, $member->groupId);
    }
}
