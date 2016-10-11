<?php

/**
 * DOA for the MailChimp-wrapper
 * represents a "Subscriber" as mentioned here:
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/overview/
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#
 */
class MailChimpSubscriber
{
    /** fields marked with *** are mantatory for "create" */

    /** The MD5 hash of the lowercase version of the list member’s email address. */
    //public $id               = ''; //   e.g. "f4fbadbe2a123b82998b6af06ba93095" // --TODO-- check, who is responsive for that

    /** Email address for a subscriber. */
    public $email_address    = ''; // ***

    /** Type of email this member asked to get. */
    //public $email_type       = ''; //   values: "html", "text"

    /** Subscriber’s current status. */
    public $status           = ''; // *** values: "subscribed", "unsubscribed", "cleaned", "pending"

    /** The date and time the member’s info was last changed. */
    //public $last_changed     = '';

    /** If set/detected, the subscriber’s language. */
    //public $language         = '';

    /** The list id. */
    //public $list_id          = '';

    /** An individual merge var and value for a member. */
    public $merge_fields     = array(); // e.g. array(FNAME => 'Sebastian', LNAME => 'Bach', GENDER => 'm|f')

    /** The date and time the subscriber signed up for the list. */
    //public $timestamp_signup = '';

    /** The date and time the subscribe confirmed their opt-in status. */
    //public $timestamp_opt    = ''; // e.g. "2016-10-04T12:51:01+00:00"


    /**
     * set the properties of that object (chainable)
     *
     * @param string $key  name of a property of this object
     * @param string $value  value
     * @return void
     */
    public function set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }
}
