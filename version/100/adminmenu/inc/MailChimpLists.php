<?php
/* MailChimp Main-End-Point "Lists"
 * (http://developer.mailchimp.com/documentation/mailchimp/reference/overview/)
 *
 * Sub-EndPoints:
 *
 *  POST       /lists                                                                                Create a new list
 *  GET        /lists                                                                                Get information about all lists
 *  POST       /lists/{list_id}                                                                      Batch sub/unsub list members
 *  GET        /lists/{list_id}                                                                      Get information about a specific list
 *  PATCH      /lists/{list_id}                                                                      Update a specific list
 *  DELETE     /lists/{list_id}                                                                      Delete a list
 *  GET        /lists/{list_id}/abuse-reports                                                        Get information about abuse reports
 *  GET        /lists/{list_id}/abuse-reports/{report_id}                                            Get details about a specific abuse report
 *  GET        /lists/{list_id}/activity                                                             Get recent list activity
 *  GET        /lists/{list_id}/clients                                                              Get top email clients
 *  GET        /lists/{list_id}/growth-history                                                       Get list growth history data
 *  GET        /lists/{list_id}/growth-history/{month}                                               Get list growth history by month
 *  POST       /lists/{list_id}/interest-categories                                                  Create a new interest category
 *  GET        /lists/{list_id}/interest-categories                                                  Get information about a list’s interest categories
 *  GET        /lists/{list_id}/interest-categories/{interest_category_id}                           Get information about a specific interest category
 *  PATCH      /lists/{list_id}/interest-categories/{interest_category_id}                           Update a specific interest category
 *  DELETE     /lists/{list_id}/interest-categories/{interest_category_id}                           Delete a specific interest category
 *  POST       /lists/{list_id}/interest-categories/{interest_category_id}/interests                 Create a new interest in a specific category
 *  GET        /lists/{list_id}/interest-categories/{interest_category_id}/interests                 Get all interests in a specific category
 *  GET        /lists/{list_id}/interest-categories/{interest_category_id}/interests/{interest_id}   Get interests in a specific category
 *  PATCH      /lists/{list_id}/interest-categories/{interest_category_id}/interests/{interest_id}   Update interests in a specific category
 *  DELETE     /lists/{list_id}/interest-categories/{interest_category_id}/interests/{interest_id}   Delete interests in a specific category
 *  POST       /lists/{list_id}/members                                                              Add a new list member
 *  GET        /lists/{list_id}/members                                                              Get information about members in a list
 *  GET        /lists/{list_id}/members/{subscriber_hash}                                            Get information about a specific list member
 *  PATCH      /lists/{list_id}/members/{subscriber_hash}                                            Update a list member
 *  PUT        /lists/{list_id}/members/{subscriber_hash}                                            Add or update a list member
 *  DELETE     /lists/{list_id}/members/{subscriber_hash}                                            Remove a list member
 *  GET        /lists/{list_id}/members/{subscriber_hash}/activity                                   Get recent list member activity
 *  GET        /lists/{list_id}/members/{subscriber_hash}/goals                                      Get the last 50 Goal events for a member on a specific list
 *  POST       /lists/{list_id}/members/{subscriber_hash}/notes                                      Add a new note
 *  GET        /lists/{list_id}/members/{subscriber_hash}/notes                                      Get recent notes for a specific list member
 *  GET        /lists/{list_id}/members/{subscriber_hash}/notes/{note_id}                            Get a specific note for a specific list member
 *  PATCH      /lists/{list_id}/members/{subscriber_hash}/notes/{note_id}                            Update a note
 *  DELETE     /lists/{list_id}/members/{subscriber_hash}/notes/{note_id}                            Delete a note
 *  POST       /lists/{list_id}/merge-fields                                                         Add a new merge field
 *  GET        /lists/{list_id}/merge-fields                                                         Get all merge fields for a list
 *  GET        /lists/{list_id}/merge-fields/{merge_id}                                              Get a specific merge field
 *  PATCH      /lists/{list_id}/merge-fields/{merge_id}                                              Update a merge field
 *  DELETE     /lists/{list_id}/merge-fields/{merge_id}                                              Delete a merge field
 *  POST       /lists/{list_id}/segments                                                             Create a new segment
 *  GET        /lists/{list_id}/segments                                                             Get information about all segments in a list
 *  GET        /lists/{list_id}/segments/{segment_id}                                                Get information about a specific segment
 *  PATCH      /lists/{list_id}/segments/{segment_id}                                                Update a segment
 *  DELETE     /lists/{list_id}/segments/{segment_id}                                                Delete a segment
 *  POST       /lists/{list_id}/segments/{segment_id}/members                                        Add a member to a static segment
 *  GET        /lists/{list_id}/segments/{segment_id}/members                                        Get information about all members in a list segment
 *  DELETE     /lists/{list_id}/segments/{segment_id}/members/{subscriber_hash}                      Remove a member from the specified static segment
 *  POST       /lists/{list_id}/signup-forms                                                         Create a new list signup form
 *  GET        /lists/{list_id}/signup-forms                                                         Get signup forms for a specific list
 *  POST       /lists/{list_id}/twitter-lead-gen-cards                                               Create a new Twitter Lead Generation Card
 *  GET        /lists/{list_id}/twitter-lead-gen-cards                                               Get information about all Twitter Lead Generation Cards for a specific list
 *  GET        /lists/{list_id}/twitter-lead-gen-cards/{twitter_card_id}                             Get information about a specific Twitter Lead Generation Card
 *  POST       /lists/{list_id}/webhooks                                                             Create a new webhook
 *  GET        /lists/{list_id}/webhooks                                                             Get information about all webhooks for a specific list
 *  GET        /lists/{list_id}/webhooks/{webhook_id}                                                Get information about a specific webhook
 *  DELETE     /lists/{list_id}/webhooks/{webhook_id}                                                Delete a webhook
 *
 */

class MailChimpLists
{
    public $oRestClient   = null;
    private $szListId     = '';
    private $vListMembers = array();

    private $oLogger = null; // --DEBUG--


    public function __construct(RestClient $oClient)
    {
        // --DEBUG--
        Logger::configure('/var/www/html/shop4_03/_logging_conf.xml');
        $this->oLogger = Logger::getLogger('default');
        // --DEBUG--


        $this->oRestClient = $oClient;
    }

    /**
     * fetch all list from MailChimp account
     *
     * @param void
     * @return array  array of objects of MailChimp-lists
     */
    public function getAllLists()
    {
        $oResponse = json_decode($this->oRestClient->retrieve('/lists'));

        $this->oLogger->debug('count of lists: '.$oResponse->total_items); // --DEBUG--
        /* {{{
        for ($i = 0; $i < $oResponse->total_items; $i++) {
            $this->oLogger->debug('list id: "'.$oResponse->lists[$i]->id.'", name: "'.$oResponse->lists[$i]->name.'"'); // --DEBUG--
            $this->szListId = $oResponse->lists[$i]->id;
        }
        }}} */
        return $oResponse->lists;
    }

    /**
     * fetch all subscribers of a given List
     *
     * @param string  list-id of a MailChimp-list
     * @return array  array of objects of (MailChimp-)subscribers
     */
    public function getAllMembers($szListId)
    {
        $oResponse = json_decode($this->oRestClient->retrieve('/lists/'.$szListId.'/members'));

        $this->vListMembers = $oResponse->members; // store the list to spare transfer of data
        return $this->vListMembers;
    }

    public function getMemberBySubscriberHash($szListId, $szSubscriberHash)
    {
        // --TODO-- return direkt, without var ...
        $oMember = json_decode($this->oRestClient->retrieve('/lists/'.$szListId.'/members/'.$szSubscriberHash));
        //$this->oLogger->debug('MEMBER: '.print_r($oMember ,true)); // --DEBUG--
        return $oMember;
    }

    /** maybe --OBSOLETE--  ..but a search in the list can be helpful .. */
    public function findMemberByEmail($szEmail)
    {
        $szSubscriberHash = $this->calcSubscriberHash($szEmail);
        // --TODO-- find the SubscriberHash of a spcific member ... (for update and deletion)
        while (true) {
        }
        //return $szSubscriberHash;
    }

    public function createMember($szListId)
    {
        $oMember = new MailChimpSubscriber();
        $oMember->set('email_address', 'danny.raufeisen@jtl-software.com')
                ->set('list_id', 'feccd07475')
                ->set('status', 'subscribed')
                ->set('merge_fields', array('FNAME' => 'Danny', 'LNAME' => 'Raufeisen'));

        //$this->oLogger->debug('JSON: '.print_r($oMember,true)); // --DEBUG--
        //$this->oLogger->debug('JSON: '.json_encode($oMember)); // --DEBUG--

        $oResponse = $this->oRestClient->create('/lists/'.$szListId.'/members', json_encode($oMember));
        $this->oLogger->debug('response (create): '.print_r(json_decode($oResponse),true)); // --DEBUG--
    }

    public function createMembersBulk($vSubscribers)
    {
        // --TODO-- create members a´ mass ...
        // loop $vSubscribers
        //    $this->createMember()
        // /loop
    }

    public function deleteMember($szListId, $szSubscriberHash)
    {
        $szSubscriberHash = 'a04edc8221043dd5b0411c5243193a3b'; // --DEBUG--

        $oResponse = $this->oRestClient->destroy('/lists/'.$szListId.'/members/'.$szSubscriberHash);
        $this->oLogger->debug('response (delete): '.print_r(json_decode($oResponse),true)); // --DEBUG--
    }

    public function updateMember($szListId, $szSubscriberHash)
    {
        $szSubscriberHash = '2c1f20509ea87adc76dc50c773c15a2f'; // --DEBUG--

        $oMember = new MailChimpSubscriber();
        $oMember->set('email_address', 'Clemens.Rudolph@jtl-software.com')
                ->set('list_id', 'feccd07475')
                ->set('status', 'pending')  // "pending" user get's a confirmation-mail ... not what we want in our shops ...!
                ->set('status', 'subscribe') // overwrite previouse .. --DEBUG--
                ->set('merge_fields', array('FNAME' => 'Clemanus', 'LNAME' => 'Rudolphero'));

        $oResponse = $this->oRestClient->update('/lists/'.$szListId.'/members/'.$szSubscriberHash, json_encode($oMember));
        $this->oLogger->debug('response (update): '.print_r(json_decode($oResponse),true)); // --DEBUG--
    }

    /**
     * helper for calculating the MailChimp-conform "subscriberHash"
     *
     * @param string  email-addres of a subscriber
     * @return string  the MailChimp-conform subscriberHash
     */
    public function calcSubscriberHash($szEmail)
    {
        return md5(strtolower($szEmail));
    }
}

