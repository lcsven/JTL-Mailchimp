<?php
/**
 * MailChimp3 plugin - Lists-object
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 *
 *
 * MailChimp Main-End-Point "Lists"
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
    /** REST-Client object */
    public $oRestClient       = null;

    /** name-hash for fast access a name-id-relation of the MailChimp-lists */
    public $listNames         = array();

    /** holds a list of list-objects (all are exists at MailChimp) to prevent double-fetching! */
    private $oResponseLists   = null;

    /** holds the last fetched list-members */
    private $vListMembers     = array();

    /** index of the remote list-members */
    public $vIndexListMembers = array();

    /** singleton pattern "self instance"-holder */
    private static $oInstance = null;


    /**
     * construct this object
     *
     * @param object  RestClient-object
     * @return void
     */
    private function __construct(RestClient $oClient)
    {
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
     * fetch all lists from the current MailChimp account
     *
     * @param void
     * @return array  array of objects of MailChimp-lists
     */
    public function getAllLists()
    {
        // prevent double-calls of the Plugin-class (e.g. by tab_settings)
        if (null === $this->oResponseLists) {
            $oResponse            = json_decode($this->oRestClient->retrieve('/lists'));
            $this->oResponseLists = $oResponse;
        } else {
            $oResponse = $this->oResponseLists;
        }
        // store the list-names(!) in a hash for faster access at later usage
        foreach ($oResponse->lists as $arrayPos => $oList) {
            $this->listNames[$oList->id]   = $oList->name;
        }
        return $oResponse->lists;
    }

    /**
     * fetch all members of a given MailChimp-list
     *
     * @param string  list-id of a MailChimp-list
     * @param integer  count of "to request list-members" (needed to avoid the deafult "10" of MailChimp)
     * @return array  array of objects of (MailChimp-)subscribers
     */
    //public function getAllMembers($szListId, $iOffset, $iCount)
    public function getAllMembers($szListId, $iOffset = 0)
    {
        // the only way to get the correct members-count of a list is to ask the list directly.
        // so we "pre-read" the members-endpoint to get the current and correct member-count of that list.
        // (we filter only for one field, to spare response-time and data-amount)
        $vGetParams = array(
            'fields' => implode(',', array(
                'total_items'
            ))
        );
        $oResponse     = json_decode($this->oRestClient->retrieve('/lists/'.$szListId.'/members', $vGetParams));
        $iTotalMembers = $oResponse->total_items;

        $vGetParams = array(
              'offset' => $iOffset // --OPTIONAL--
            , 'count'  => $iTotalMembers // mandatory by MailChimp (default without this var is allways 10!)
            , 'fields' => implode(',', array(
                  'members.id'
                , 'members.email_address'
                , 'members.status'
                , 'members.last_changed'
                , 'members.merge_fields'
                , 'total_items'
                ))
        );
        $oResponse  = json_decode($this->oRestClient->retrieve('/lists/'.$szListId.'/members', $vGetParams));

        if (null !== $oResponse) {
            $this->vListMembers = $oResponse->members; // store the list to spare transfer of data
            $this->buildHashIndex($this->vListMembers); // build a hash-index of that list

            return $this->vListMembers;
        }
        // --TODO-- error-handling
    }

    /**
     * build a local index of all subscribers, to find them quickly in our fetched list
     * (store that hash-index locally)
     *
     * @param void
     * @return void
     */
    private function buildHashIndex($vListMembers)
    {
        foreach ($vListMembers as $arrayPos => $oListMember) {
            $this->vIndexListMembers[$oListMember->id] = $arrayPos;
        }
    }

    /**
     * locally find a subscriber in the fetched list
     * (we're using our local index, to speed up the access of a list-member)
     *
     * @param string  MailChimp-SubscriberHash
     * @return object  Subscriber-object
     */
    public function findMemberBySubscriberHash($szSubscriberHash)
    {
        return $this->vListMembers[$this->vIndexListMembers[$szSubscriberHash]];
    }

    /**
     * create one single member in the given MailChimp-list
     *
     * @param string  MailChimp-list-ID
     * @param object  MailChimpSubscriber
     * @return void
     */
    public function createMember($szListId, MailChimpSubscriber $oSubscriber)
    {
        // we only need the unique_email_id to assume "a new list-member was created"
        $vGetParams = array(
            'fields' => implode(',', array(
                  'email_address'
                , 'unique_email_id'
            ))
        );
        $oResponse  = json_decode($this->oRestClient->create( '/lists/'.$szListId.'/members', $vGetParams, json_encode($oSubscriber)));

        // error-handling
        if (isset($oResponse->status) && 400 <= $oResponse->status) {
            throw new ExceptionMailChimp($oResponse);
        }
        // if there is a unique_email_id created by MailChimp, so we return 1 creation
        return (isset($oResponse->unique_email_id) ? 1 : 0);
    }

    /**
     * create members a´ mass ...
     *
     * @param string  the current MailChimp-list-ID
     * @param array  subscriber-array (stdClass-objects)
     * @return void
     */
    public function createMembersBulk($szListId, $vSubscribers)
    {
        // there is a limitation by MailChimp, of max. 500 allowed newsletter-receivers, in one transmission
        $iChunkSize = 499;

        $oSubsChunk = new MailChimpSubscriberBulk();
        $vCheckHash = array(); // to prevent duplicates! (MailChimp stops the import of a complete chunk if there are any)
        foreach ($vSubscribers as $oSubs) {
            $oSub = new MailChimpSubscriber();
            $oSub->set('email_address', $oSubs->cEmail)
                 ->set('status', 'subscribed')
                 ->set('merge_fields', array(
                              'FNAME'  => $oSubs->cVorname
                            , 'LNAME'  => $oSubs->cNachname
                            , 'GENDER' => $oSubs->cGender
                            )
                );
            // we have to take care, to don't give MailChimp duplicates!
            $szCheckSum = md5(strtolower($oSub->email_address));
            if (!isset($vCheckHash[$szCheckSum])) {
                $oSubsChunk->append($oSub);
                $vCheckHash[$szCheckSum] = true; // mark this subscriber as "got it"
            }

            if ($iChunkSize === $oSubsChunk->getCount()) {
                $vChunks[] = json_encode($oSubsChunk); // add a chunk as json-string
            }
        }
        // "merge" the last chunk
        if (0 != $oSubsChunk->getCount()) {
            $vChunks[] = json_encode($oSubsChunk);
        }

        // choose the fields, we want to see in the response  --TO-CHECK--
        $vGetParams = array(
            'fields' => implode(',', array(
                  'updated_members.email_address'
                , 'new_members.email_address'
                , 'total_created'
                , 'total_updated'
                , 'errors'
                ))
        );
        $vErrors        = array(); // --TRY-OUT--
        $iTransmitCount = 0;
        // fire to MailChimp
        foreach ($vChunks as $szChunk) {
            $oResponse = json_decode($this->oRestClient->create('/lists/'.$szListId, $vGetParams, $szChunk));

            if (isset($oResponse->status) && 400 <= $oResponse->status) {
                $vErrors[] = $oResponse;
            }
            $iTransmitCount += $oResponse->total_created; // --TO-CHECK--
        }
        unset($vCheckHash); // no need to hold big data in memory!

        return $iTransmitCount;
    }

    /**
     * delete a list-member from a MailChimp-list
     *
     * @param string  MailChimp-list-ID
     * @param string  Subscriber-Hash
     * @return void
     */
    public function deleteMember($szListId, $szSubscriberHash)
    {
        $oResponse = json_decode($this->oRestClient->destroy('/lists/'.$szListId.'/members/'.$szSubscriberHash));
        // error-handling
        if (isset($oResponse->status) && 400 <= $oResponse->status) {
            throw new ExceptionMailChimp($oResponse);
        }
        // all went fine, if MailChimp did not send anything (only a http 204 "No Content")
        return (null === $oResponse ? 1 : 0);
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

