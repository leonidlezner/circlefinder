<?php

namespace App;

class TimeTable
{
    private $timeSlots = array();
    private $memberships = null;
    private $checks = array();

    public function getTimeList()
    {
        return range(6, 22);
    }

    public static function getDayList($short = false)
    {
        if ($short == true) {
            return [
                'mo',
                'tu',
                'we',
                'th',
                'fr',
                'sa',
                'su',
            ];
        }

        return [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ];
    }

    public function timeSlot($membership = null)
    {
        return count($this->timeSlots) ? $this->timeSlots[0] : null;
    }

    public function timeSlots()
    {
        return $this->timeSlots;
    }

    public function memberships()
    {
        return $this->memberships;
    }

    public static function updateOrCreateForMembership($membership, $request_data, $current_user)
    {
        $timeTable = new \App\TimeTable;

        $timeSlot = $membership->timeSlot;

        $timeSlot->setTimeOffset($current_user->time_offset);

        foreach ($timeTable->getDayList() as $day) {
            if (key_exists($day, $request_data)) {
                $timeSlot->$day = $request_data[$day];
            } else {
                $timeSlot->$day = 0;
            }
        }

        $timeSlot->save();

        $timeTable->timeSlots[] = $timeSlot;

        $timeTable->memberships[] = $membership;

        return $timeTable;
    }

    public static function forCircle($circle, $current_user, $memberships = null)
    {
        $timeTable = new \App\TimeTable;

        if (is_null($memberships)) {
            $memberships = $circle->memberships()->with('timeSlot')->get();
        }

        if (count($memberships) < 1) {
            return null;
        }

        $timeTable->memberships = $memberships;

        foreach ($memberships as $membership) {
            $slot = $membership->timeSlot;

            $slot->setTimeOffset($current_user->time_offset);
            
            $timeTable->timeSlots[] = $slot;
        }

        $timeTable->generateCheckCounts();

        return $timeTable;
    }

    public function atTime($time)
    {
        foreach ($this->timeSlots as $timeSlot) {
            if ($timeSlot->atTime($time)) {
                return true;
            }
        }

        return false;
    }

    private function generateCheckCounts()
    {
        foreach ($this->timeSlots as $timeSlot) {
            foreach ($this->getDayList() as $day) {
                if (is_array($timeSlot->$day)) {
                    if (key_exists($day, $this->checks) == false) {
                        $this->checks[$day] = array();
                    }

                    if (is_array($timeSlot->$day)) {
                        foreach ($timeSlot->$day as $time) {
                            if (key_exists($time, $this->checks[$day]) == false) {
                                $this->checks[$day][$time] = 1;
                            } else {
                                $this->checks[$day][$time]++;
                            }
                        }
                    }
                }
            }
        }
    }

    public function checksAt($day, $time)
    {
        if (key_exists($day, $this->checks) && key_exists($time, $this->checks[$day])) {
            return $this->checks[$day][$time];
        } else {
            return 0;
        }
    }
}
