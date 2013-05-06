<table>
<thead>
<tr>
<th scope="col" class="date">Time</th>
<th scope="col" class="title">Event Title</th>
</tr>
</thead>
<tbody class="vcalendar">
<?php
$oddrow = false;
foreach ($context as $e) {
    
    $startu = strtotime($e->eventdatetime->starttime);
    $endu = strtotime($e->eventdatetime->endtime);
    
    $row = '<tr class="vevent';
    if ($oddrow) {
        $row .= ' alt';
    }
    $row .= '">';
    $oddrow = !$oddrow;
    $row .=    '<td class="date">';
    if (isset($e->eventdatetime->starttime)) {
        if (strpos($e->eventdatetime->starttime,'00:00:00')) {
            $row .= '<abbr class="dtstart" title="'.date('c', $startu).'">All day</abbr>';
        } else {
            $row .= '<abbr class="dtstart" title="'.date('c', $startu).'">'.date('g:i a', $startu).'</abbr>';
        }
    } else {
        $row .= 'Unknown';
    }
    if (isset($e->eventdatetime->endtime) &&
        ($e->eventdatetime->endtime != $e->eventdatetime->starttime) &&
        ($e->eventdatetime->endtime > $e->eventdatetime->starttime)) {
        if (substr($e->eventdatetime->endtime,0,10) != substr($e->eventdatetime->starttime,0,10)) {
            // Not on the same day
            if (strpos($e->eventdatetime->endtime,'00:00:00')) {
                $row .= '-<abbr class="dtend" title="'.date(DATE_ISO8601, $endu).'">'.date('M jS', $endu).'</abbr>';
            } else {
                $row .= '-<abbr class="dtend" title="'.date(DATE_ISO8601, $endu).'">'.date('M jS g:i a', $endu).'</abbr>';
            }
        } else {
            $row .= '-<abbr class="dtend" title="'.date(DATE_ISO8601, $endu).'">'.date('g:i a', $endu).'</abbr>';
        }
    }
    $row .= '</td>' .
            '<td><a class="url summary" href="'.$frontend->getCalendarURL().date('Y/m/d/', $startu).$e->eventdatetime->id.'">'.$savvy->dbStringtoHtml($e->event->title).'</a>';
    if (isset($e->eventdatetime->location_id) && $e->eventdatetime->location_id) {
        $l = $e->eventdatetime->getLocation();
        $row .= ' <span class="location">';
        if (isset($l->mapurl)) {
            $row .= '<a class="mapurl" href="'.$savvy->dbStringtoHtml($l->mapurl).'">'.$savvy->dbStringtoHtml($l->name).'</a>';
        } else {
            $row .= $savvy->dbStringtoHtml($l->name);
        }
        $row .= '</span>';
    }
    $row .=    '<blockquote class="description">'.$savvy->dbStringtoHtml($e->event->description).'</blockquote>';
//     $facebook = new \UNL\UCBCN\Facebook\Instance($occurrence->id);
//     $row .= $facebook->like($occurrence->getURL(), $parent->context->getCalendar()->id);
    $row .= '</td></tr>';
    
    echo $row;
}

 ?>

</tbody>
</table>

