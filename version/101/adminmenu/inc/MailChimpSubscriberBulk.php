<?php

/**
 * DOA for the MailChimp-wrapper
 * represents a "Subscriber" as mentioned here:
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/overview/
 *     http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#
 */
class MailChimpSubscriberBulk
{
    /** fields marked with (*** are mantatory for "create" (names are given by MailChimp!) */

    /**
     * @var MailChimpSubscriber[]
     */
    public $members = [];

    /**
     * "update"[true] existing list-members or "not-update"[false]
     *
     * @var bool
     */
    public $update_existing = false; // --NOTE-- 'update' did not work correcly by MailChimp (maybe a bug)


    /**
     * append a subscriber-object (chainable)
     *
     * @param MailChimpSubscriber $oSubscriber
     * @return $this
     */
    public function append(MailChimpSubscriber $oSubscriber)
    {
        $this->members[] = $oSubscriber;

        return $this;
    }

    /**
     * returns the length of the internal subscriber-array
     *
     * @return integer  the count() of the internal array
     */
    public function getCount()
    {
        return count($this->members);
    }
}
