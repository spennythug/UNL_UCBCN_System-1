<?php
namespace UNL\UCBCN\Event;

use UNL\UCBCN\ActiveRecord\Record;

class RecurringDate extends Record 
{

    public $id;                              // int(10)  not_null primary_key unsigned auto_increment
    public $recurringdate;                   // date(10)  not_null binary
    public $event_id;                        // int(10)  not_null unsigned
    public $recurrence_id;                   // int(10)  not_null unsigned
    public $ongoing;                         // int(1)  
    public $unlinked;                        // int(1)
    public $event_datetime_id;

    public static function getTable()
    {
        return 'recurringdate';
    }

    function keys()
    {
        return array(
            'id',
        );
    }
    
    /**
     * Get the first recurring date in an ongoing series
     * 
     * A row will be added for each date that an event occurs.
     * This method will aid in getting the start date for an ongoing event.
     * 
     * @return bool|RecurringDate
     * @throws \UNL\UCBCN\ActiveRecord\Exception
     */
    public function getFirstRecordInOngoingSeries()
    {
        return self::getByAnyField(
            __CLASS__,
            'recurrence_id',
            $this->recurrence_id,
            'event_id = ' . (int)$this->event_id . ' AND ongoing = 0'
        );
    }

    public static function getByEventDatetimeIDRecurrenceID($event_datetime_id, $recurrence_id) {
        return self::getByEvent_Datetime_ID($event_datetime_id, 'ongoing = 0 AND recurrence_id = ' . $recurrence_id);
    }
}
