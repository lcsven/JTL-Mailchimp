<?php

/**
 * DOA for the MailChimp-wrapper
 * represents a "Subscriber" as mentioned here:
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/overview/
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#
 */
class MailChimpSubscriberBulk
{
    /** fields marked with *** are mantatory for "create" */

    /** array of subscriber-objects  */
    public $members         = array(); // ***

    /** update existing list-members (true) or do not update (false) */
    public $update_existing = true; // ***


    /**
     * append a subscriber-object (chainable)
     *
     * @param string $key  name of a property of this object
     * @param string $value  value
     * @return void
     */
    public function append(MailChimpSubscriber $oSubscriber)
    {
        $this->members[] = $oSubscriber;
        return $this;
    }

    /**
     * returns the length of the internal subscriber-array
     *
     * @param void
     * @return integer  the count() of the internal array
     */
    public function getCount()
    {
        return count($this->members);
    }
}

