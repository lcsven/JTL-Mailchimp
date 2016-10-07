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
    public $oRestClient       = null;
    public $hashIndex         = array(); // --TODO-- alter name; mayba add something like hashIndexMembers
    public $listNames         = array();
    private $oResponseLists   = null;
    private $vListMembers     = array();
    private static $oInstance = null; // singleton pattern "self instance"-holder

    private $oLogger = null; // --DEBUG--


    /**
     * construct this object
     *
     * @param object  RestClient-object
     * @return void
     */
    private function __construct(RestClient $oClient)
    {
        // --DEBUG--
        Logger::configure('/var/www/html/shop4_03/_logging_conf.xml');
        $this->oLogger = Logger::getLogger('default');
        // --DEBUG--


        $this->oRestClient = $oClient;
    }

    /**
     * singleton pattern instance-deliverer
     *
     * @param object  RestClient-object
     * @return object  MailChimpLists-object (single instance)
     */
    public static function getInstance(RestClient $oClient)
    {
        if (null === self::$oInstance) {
            self::$oInstance = new self($oClient);
        }
        return self::$oInstance;
    }

    /**
     * fetch all list from MailChimp account
     *
     * @param void
     * @return array  array of objects of MailChimp-lists
     */
    public function getAllLists()
    {
        // prevent double-calls of the Plugin-class (because tab_settings)
        if (null === $this->oResponseLists) {
            $oResponse = json_decode($this->oRestClient->retrieve('/lists'));
            $this->oResponseLists = $oResponse;
        } else {
            $oResponse = $this->oResponseLists;
        }

        // store the list-names(!) in a hash for later usage
        foreach ($oResponse->lists as $arrayPos => $oList) {
            $this->listNames[$oList->id] = $oList->name;
        }

        return $oResponse->lists;
    }

    /**
     * fetch all subscribers of a given List from remote
     *
     * @param string  list-id of a MailChimp-list
     * @return array  array of objects of (MailChimp-)subscribers
     */
    public function getAllMembers($szListId)
    {
        $oResponse = json_decode($this->oRestClient->retrieve('/lists/'.$szListId.'/members'));
        $this->vListMembers = $oResponse->members; // store the list to spare transfer of data
        $this->buildHashIndex(); // build a hash-index of that list
        return $this->vListMembers;
    }

    /**
     * build a local index of all subscribers, to find them quickly in our fetched list
     * (store that hash-index locally)
     *
     * @param void
     * @return void
     */
    private function buildHashIndex()
    {
        foreach ($this->vListMembers as $arrayPos => $oListMember) {
            $this->hashIndex[$oListMember->id] = $arrayPos;
        }
        //$this->oLogger->debug('local hash-index: '.print_r($this->hashIndex ,true)); // --DEBUG--
    }

    /**
     * locally find a subscriber in the fetched list
     *
     * @param string  MailChimp-SubscriberHash
     * @return object  Subscriber-object
     */
    public function findMemberBySubscriberHash($szSubscriberHash)
    {
        //$this->oLogger->debug('member at position: '.$this->hashIndex[$szSubscriberHash]); // --DEBUG--
        return $this->vListMembers[$this->hashIndex[$szSubscriberHash]];
    }

    /**
     * find a subscriber as list-member at remote
     *
     * @param string  Mailchimp-list-ID
     * @param string  MailChimp-SubscriberHash
     * @return object  single MailChimp subscriber-object
     */
    public function getMemberBySubscriberHash($szListId, $szSubscriberHash)
    {
        return json_decode($this->oRestClient->retrieve('/lists/'.$szListId.'/members/'.$szSubscriberHash));
    }

    public function createMember($szListId, MailChimpSubscriber $oSubscriber)
    {
        $oResponse = $this->oRestClient->create('/lists/'.$szListId.'/members', json_encode($oSubscriber));
        $this->oLogger->debug('response (create): '.print_r(json_decode($oResponse),true)); // --DEBUG--
        // --TODO-- error-handling
    }

    public function createMembersBulk($vSubscribers)
    {
        // create members a´ mass ...
        // loop $vSubscribers
        //    $this->createMember()  .... NO! ONE api-call is possible! (with up to 500 members-subscriptions)
        // /loop

        // --TODO-- we need the subscriptions as a object-list with only email and status
        // we have to do that in chunks of 500 subscribers
        $oResponse = $this->oRestClient->create('/lists/'.$szListId, json_encode($vSubscribers));
    }

    public function deleteMember($szListId, $szSubscriberHash)
    {
        $oResponse = $this->oRestClient->destroy('/lists/'.$szListId.'/members/'.$szSubscriberHash);
        $this->oLogger->debug('response (delete): '.print_r(json_decode($oResponse),true)); // --DEBUG--
        // --TODO-- error-handling
    }

    /** maybe --OBSOLETE-- ... */
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

