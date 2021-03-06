<?php

require_once '../src/intouch/ical/ICal.php';

use intouch\ical\iCal;
use intouch\ical\Query;

function dump_t($x)
{
    echo "<pre>".print_r($x,true)."</pre>";
}

//local
//$ICS = "Canada-Holidays.ics";
$ICS = "http://files.apple.com/calendars/Canadian32Holidays.ics";

//echo dump_t(file_get_contents($ICS));

$ical = new iCal($ICS);
$query = new Query();

$evts = $ical->getEvents();
//$evts = $query->Between($ical,strtotime('20100901'),strtotime('20101131'));

$data = array();
foreach ($evts as $id => $ev) {
    $jsEvt = array(
        "id" => ($id+1),
        "title" => $ev->getProperty('summary'),
        "start" => $ev->getStart(),
        "end"   => $ev->getEnd()-1,
        "allDay" => $ev->isWholeDay()
    );

    if (isset($ev->recurrence)) {
        $count = 0;
        $start = $ev->getStart();
        $freq = $ev->getFrequency();
        if ($freq->firstOccurrence() == $start)
            $data[] = $jsEvt;
        while (($next = $freq->nextOccurrence($start)) > 0 ) {
            if (!$next or $count >= 1000) break;
            $count++;
            $start = $next;
            $jsEvt["start"] = $start;
            $jsEvt["end"] = $start + $ev->getDuration()-1;

            $data[] = $jsEvt;
        }
    } else
        $data[] = $jsEvt;

}
//echo(date('Ymd\n',$data[0][start]));
//echo(date('Ymd\n',$data[1][start]));
//dump_t($data);

$events = "events:".json_encode($data).',';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Fullcalendar iCal Loader</title>
<link rel="stylesheet" type="text/css" href="//arshaw.com/js/fullcalendar-1.6.1/fullcalendar/fullcalendar.css">

<style type='text/css'>
    body div {
        text-align: center;
    }
    body {
        font-size: 14px;
        font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    }
    div#loading {
        position: absolute;
        top: 5px;
        right: 5px;
    }
    div#calendar {
        width: 900px;
        margin: 0 auto;
    }
</style>
</head>
<body>
<div id="loading" style="display:none;">loading...</div>
<div id="calendar"></div>
<!-- This is the last version of jQuery that seems to work with fullcalendar.js -->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="//arshaw.com/js/fullcalendar-1.6.1/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript">

    $(document).ready(function() {

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },

            year: (new Date()).getFullYear(),
            month: (new Date()).getMonth(),

            // US Holidays
            //events: $.fullCalendar.gcalFeed('http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic'),

            <?=$events ?>

            eventClick: function(event) {
                // opens events in a popup window
                window.open(event.url, 'gcalevent', 'width=700,height=600');

                return false;
            },

            loading: function(bool) {
                if (bool) {
                    $('#loading').show();
                } else {
                    $('#loading').hide();
                }
            }

        });

    });

</script>
</body>
</html>
