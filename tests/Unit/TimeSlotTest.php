<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;

/**
 * @group timeslot
 */
class TimeSlotTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    private function fetchMembership($data, $user)
    {
        $membership = new \App\Membership;
        
        $membership->fill($data);
        
        $membership->circle_id = 0;
        $membership->user_id = $user->id;
        
        $membership->save();

        return $membership;
    }

    public function testCreateNewTimeSlot()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $data = [
            'type' => 'any',
            'begin' => $faker->date
        ];

        $membership = $this->fetchMembership($data, $user);

        $timeslot = $membership->timeSlot;

        $timeslot->monday = [5, 6, 7];
        $timeslot->tuesday = 0;
        $timeslot->wednesday = 0;
        $timeslot->thursday = 0;
        $timeslot->friday = 0;
        $timeslot->saturday = 0;
        $timeslot->sunday = 0;

        $timeslot->save();

        $this->assertDatabaseHas('time_slots', [
            'membership_id' => $membership->id
        ]);

        $this->assertEquals($timeslot->monday, $membership->timeSlot->monday);

        $this->assertTrue($timeslot->atTime(6));
        $this->assertFalse($timeslot->atTime(4));
    }

    public function testTimezoneConversion()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $data = [
            'type' => 'any',
            'begin' => $faker->date
        ];

        $offset = $user->time_offset;

        $membership = $this->fetchMembership($data, $user);

        $timeslot = $membership->timeSlot;

        $timeslot->setTimeOffset($offset);

        $timeslot->monday = [10, 12, 14];
        $timeslot->tuesday = [4, 5, 6];
        $timeslot->wednesday = 0;
        $timeslot->thursday = 0;
        $timeslot->friday = 0;
        $timeslot->saturday = 0;
        $timeslot->sunday = 0;

        $timeslot->save();

        $this->assertDatabaseHas('time_slots', [
            'membership_id' => $membership->id,
            'monday' => json_encode([10 - $offset, 12 - $offset, 14 - $offset]),
            'tuesday' => json_encode([4 - $offset, 5 - $offset, 6 - $offset]),
            'wednesday' => 0
        ]);

        $timeslot = \App\TimeSlot::where('membership_id', $membership->id)->first();

        $user->timezone = '';
        $user->save();
 
        $timeslot->setTimeOffset($user->time_offset);

        $this->assertEquals([10 - $offset, 12 - $offset, 14 - $offset], $timeslot->monday);

        $timeslot->setTimeOffset($offset);

        $this->assertEquals($timeslot->monday, [10, 12, 14]);
        $this->assertEquals($timeslot->wednesday, 0);
    }
}
