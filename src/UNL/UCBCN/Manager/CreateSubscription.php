<?php
namespace UNL\UCBCN\Manager;

use UNL\UCBCN\Calendar;
use UNL\UCBCN\Calendars;
use UNL\UCBCN\Calendar\Subscription;
use UNL\UCBCN\Calendar\SubscriptionHasCalendar;

class CreateSubscription
{
    public $options = array();
    public $calendar;
    public $subscription;

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
        $this->calendar = Calendar::getByShortname($this->options['calendar_shortname']);

        if ($this->calendar === FALSE) {
            throw new \Exception("That calendar could not be found.", 500);
        }

        # check if we are posting to this controller
        if (!empty($_POST)) {
            if (array_key_exists('subscription_id', $this->options)) {
                # we are editing an existing subscription
                $this->subscription = Subscription::getById($this->options['subscription_id']);

                if ($this->subscription == FALSE) {
                    throw new \Exception("That subscription could not be found.", 500);
                }

                $this->updateSubscription($_POST);
            } else {
                # we are creating a new subscription
                $this->subscription = $this->createSubscription($_POST);
            }

            header('Location: /manager/' . $this->calendar->shortname . '/subscriptions/');
        }

        if (array_key_exists('subscription_id', $this->options)) {
            # we are editing an existing subscription
            $this->subscription = Subscription::getById($this->options['subscription_id']);

            if ($this->subscription == FALSE) {
                throw new \Exception("That subscription could not be found.", 500);
            }
        } else {
            $this->subscription = new Subscription;
        }
    }

    public function getAvailableCalendars() 
    {
        return new Calendars;
    }

    private function createSubscription($post_data) 
    {
        $subscription = new Subscription;
        $subscription->name = $post_data['title'];
        $subscription->automaticapproval = $post_data['auto_approve'] == 'yes' ? 1 : 0;
        $subscription->calendar_id = $this->calendar->id;

        $subscription->insert();
        
        # add subscription_has_calendars for each one selected
        foreach($post_data['calendars'] as $calendar_id) {
            $sub_has_calendar = new SubscriptionHasCalendar;
            $sub_has_calendar->calendar_id = $calendar_id;
            $sub_has_calendar->subscription_id = $subscription->id;
            $sub_has_calendar->insert();
        }

        # the subscription will go and get the events from those calendars that are relevant
        $subscription->process();

        return $subscription;
    }

    private function updateSubscription($post_data)
    {
        # see what calendars were removed from the subscription first...if they
        # are not present, remove the record from sub_has_calendar
        $current_subbed_calendars = $this->subscription->getSubscribedCalendars();
        $current_subbed_calendars_ids = array();
        foreach ($current_subbed_calendars as $cal) {
            $current_subbed_calendars_ids[] = $cal->id;
        }

        foreach ($current_subbed_calendars_ids as $calendar_id) {
            if (!in_array($calendar_id, $post_data['calendars'])) {
                # it has been deleted
                $record = SubscriptionHasCalendar::get($this->subscription->id, $calendar_id);
                $record->delete();
            }
        }

        # now add calendars that were not already in the subscription
        foreach ($post_data['calendars'] as $calendar_to_sub_id) {
            if (!in_array($calendar_to_sub_id, $current_subbed_calendars_ids)) {
                # add a new record
                $sub_has_calendar = new SubscriptionHasCalendar;
                $sub_has_calendar->calendar_id = $calendar_to_sub_id;
                $sub_has_calendar->subscription_id = $this->subscription->id;
                $sub_has_calendar->insert();
            }
        }

        # process the subscription again. Events that are currently already in there
        # from the subscription will not be added twice
        $this->subscription->process();

        return $this->subscription;
    }

}