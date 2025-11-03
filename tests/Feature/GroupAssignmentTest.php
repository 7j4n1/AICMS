<?php

namespace Tests\Feature;

use App\Models\Member;
use Tests\TestCase;

class GroupAssignmentTest extends TestCase
{
    /**
     * Test that groupId is calculated correctly for different coopIds.
     * This test does not require database connection.
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
     * Test edge cases for group ID calculation.
     */
    public function test_group_id_calculation_edge_cases(): void
    {
        // Boundary values
        $this->assertEquals(1, Member::calculateGroupId(1));
        $this->assertEquals(1, Member::calculateGroupId(100));
        $this->assertEquals(2, Member::calculateGroupId(101));
        
        // Large numbers
        $this->assertEquals(50, Member::calculateGroupId(5000));
        $this->assertEquals(100, Member::calculateGroupId(10000));
        
        // Mid-range values
        $this->assertEquals(5, Member::calculateGroupId(450));
        $this->assertEquals(5, Member::calculateGroupId(500));
    }

    /**
     * Test that the calculation formula matches the requirement.
     * Each group should contain exactly 100 sequential coopIds.
     */
    public function test_group_size_consistency(): void
    {
        // Test that all values in a group of 100 map to the same groupId
        for ($coopId = 1; $coopId <= 100; $coopId++) {
            $this->assertEquals(1, Member::calculateGroupId($coopId), "coopId $coopId should be in Group 1");
        }
        
        for ($coopId = 101; $coopId <= 200; $coopId++) {
            $this->assertEquals(2, Member::calculateGroupId($coopId), "coopId $coopId should be in Group 2");
        }
        
        for ($coopId = 201; $coopId <= 300; $coopId++) {
            $this->assertEquals(3, Member::calculateGroupId($coopId), "coopId $coopId should be in Group 3");
        }
    }
}
