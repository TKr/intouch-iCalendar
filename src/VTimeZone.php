<?php // BUILD: Remove line

namespace TKr\ICal;
use DateTime;
use DateTimeZone;

/**
 * The wrapper for vtimezones. Stores the timezone-id and the setup for
 * daylight savings and standard time.
 *
 * @author Morten Fangel (C) 2008
 * @author Michael Kahn (C) 2013
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class VTimeZone
{
    protected $tzid;
    protected $daylight;
    protected $standard;
    protected $cache = array();

    /**
     * Constructs a new intouch\ical\VTimeZone
     */
    public function __construct( $data )
    {
        $this->tzid = $data['tzid'];
        $this->daylight = $data['daylight'];
        $this->standard = $data['standard'];
    }

    /**
     * Returns the timezone-id for this timezone. (Used to
     * differentiate between different tzs in a calendar)
     * @return string
     */
    public function getTimeZoneId()
    {
        return $this->tzid;
    }

    /**
     * Returns the given offset in this timezone for the given
     * timestamp. (eg +0200)
     * @param  int    $ts
     * @return string
     */
    public function getOffset( $ts )
    {
        $act = $this->getActive($ts);

        return $this->{$act}['tzoffsetto'];
    }

    /**
     * Returns the timezone name for the given timestamp (eg CEST)
     * @param  int    $ts
     * @return string
     */
    public function getTimeZoneName($ts)
    {
        $act = $this->getActive($ts);

        return $this->{$act}['tzname'];
    }

    /**
     * Determines which of the daylight or standard is the active
     * setting.
     * The call is cached for a given timestamp, so a call to
     * getOffset and getTimeZoneName with the same ts won't calculate
     * the answer twice.
     * @param  int    $ts
     * @return string standard|daylight
     */
    private function getActive( $ts )
    {
        if (class_exists('DateTimeZone')) {

            //PHP >= 5.2
            $tz = new DateTimeZone( $this->tzid );
            $date = new DateTime("@$ts", $tz);

            return ($date->format('I') == 1) ? 'daylight' : 'standard';

        } else {

            if ( isset($this->cache[$ts]) ) {
                return $this->cache[$ts];
            }

            $daylight_freq = new Freq($this->daylight['rrule'], strtotime($this->daylight['dtstart']));
            $standard_freq = new Freq($this->standard['rrule'], strtotime($this->standard['dtstart']));
            $last_standard = $standard_freq->previousOccurrence($ts);
            $last_dst = $daylight_freq->previousOccurrence($ts);
            if ($last_dst > $last_standard) {
                $this->cache[$ts] = 'daylight';
            } else {
                $this->cache[$ts] = 'standard';
            }

            return $this->cache[$ts];
        }
    }
}
