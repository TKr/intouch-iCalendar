<?php

namespace TKr\ICal\Tests\helpers;

use TKr\ICal\Duration;

class DurationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->secsMin  = 60;
        $this->secsHour = 60 * 60;
        $this->secsDay  = 24 * 60 * 60;
        $this->secsWeek = 7 * 24 * 60 * 60;
    }

    public function testDurationDateTime()
    {
        //A duration of 10 days, 6 hours and 20 seconds
        $dur = new Duration('P10DT6H0M20S');
        $this->assertEquals($this->secsDay*10 + $this->secsHour*6 + 20, $dur->getDuration() );
    }

    public function testDurationWeek()
    {
        //A duration of 2 weeks
        $dur = new Duration('P2W');
        $this->assertEquals($this->secsWeek * 2, $dur->getDuration() );
    }

    public function testDurationNegative()
    {
        //A duration of -1 day
        $dur = new Duration('-P1D');
        $this->assertEquals(-1 * $this->secsDay, $dur->getDuration() );
    }

}
