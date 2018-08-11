<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;
use Illuminate\Http\Request;

/**
 * @group timetable
 */
class TimeTableTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    private function fetchMembership($user, $circle = null)
    {
        $faker = $this->fetchFaker();
        $membership = new \App\Membership;
        
        $data = [
            'type' => 'any',
            'begin' => $faker->date
        ];

        $membership->fill($data);
        
        $membership->circle_id = $circle ? $circle->id : 0;
        $membership->user_id = $user->id;
        
        $membership->save();

        return $membership;
    }

    private function fetchTimeTable($membership)
    {
        $user = $this->fetchUser();
        
        $user->timezone = 'UTC';
        $user->save();

        $timeTable = \App\TimeTable::updateOrCreateForMembership($membership, [
            'monday' => [1, 2, 3],
            'tuesday' => 0,
            'wednesday' => [5, 6, 7, 8, 9, 10],
            'thursday' => 0,
            'friday' => 0,
            'saturday' => 0,
            'sunday' => [],
        ], $user);

        return $timeTable;
    }

    public function testFindTimeTableForMembership()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $membership = $this->fetchMembership($user);

        $this->assertFalse(is_null($membership->timeSlot));
    }

    public function testUpdateOrCreateAndDeleteTimeSlot()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $membership = $this->fetchMembership($user);

        $timeTable = $this->fetchTimeTable($membership);

        $membership = $membership->refresh();

        $this->assertFalse(is_null($membership->timeSlot));

        $this->assertEquals($timeTable->timeSlot()->wednesday, $membership->timeSlot->wednesday);

        $this->assertEquals([5, 6, 7, 8, 9, 10], $membership->timeSlot->wednesday);
        $this->assertEquals(0, $membership->timeSlot->friday);

        $membership->delete();

        $this->assertDatabaseMissing('time_slots', [
            'membership_id' => $membership->id
        ]);

        $this->assertEquals($timeTable->memberships()[0], $membership);

        $this->assertTrue($timeTable->atTime(6));
        $this->assertFalse($timeTable->atTime(4));
    }

    public function testGetTableForCircle()
    {
        $user1 = $this->fetchUser();
        $user2 = $this->fetchUser();
        $user3 = $this->fetchUser();

        $user1->timezone = 'America/Barbados';
        $user1->save();

        $offset = $user1->time_offset;
        $this->assertEquals(-4, $offset);

        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user1);

        $membership1 = $this->fetchMembership($user1, $circle);
        $membership2 = $this->fetchMembership($user2, $circle);
        $membership3 = $this->fetchMembership($user3, $circle);
        
        $timeTable1 = $this->fetchTimeTable($membership1);
        $timeTable2 = $this->fetchTimeTable($membership2);
        $timeTable3 = $this->fetchTimeTable($membership3);

        $membership1 = $membership1->refresh();

        $timeTable = \App\TimeTable::forCircle($circle, $user1);

        $this->assertEquals(3, count($timeTable->timeSlots()));

        $this->assertEquals(3, $timeTable->checksAt('monday', 2 + $offset));
        $this->assertEquals(3, $timeTable->checksAt('wednesday', 7 + $offset));
        $this->assertEquals(0, $timeTable->checksAt('friday', 7 + $offset));
    }
}
