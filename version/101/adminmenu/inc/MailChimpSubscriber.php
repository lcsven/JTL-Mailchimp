<?php
/**
 * DOA for the MailChimp-wrapper
 * represents a "Subscriber" as mentioned here:
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/overview/
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#
 */

class MailChimpSubscriber
{
    /** fields marked with (*** are mantatory for "create" (names are given by MailChimp!) */

    /** The MD5 hash of the lowercase version of the list member’s email address. */
    //public $id               = ''; //   e.g. "f4fbadbe2a123b82998b6af06ba93095" // --TODO-- check, who is responsive for that

    /** Email address for a subscriber. (*** */
    public $email_address    = '';

    /** Type of email this member asked to get. (values: "html", "text") */
    //public $email_type       = '';

    /** Subscriber’s current status. (values: "subscribed", "unsubscribed", "cleaned", "pending") (*** */
    public $status           = 'subscribed';

    /** The date and time the member’s info was last changed. */
    //public $last_changed     = '';

    /** The list id. */
    //public $list_id          = '';

    /** An individual merge var and value for a member. (e.g. array(FNAME => 'Sebastian', LNAME => 'Bach', GENDER => 'm|f')) */
    public $merge_fields     = array();


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
