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
 *
 * poperty index
 * -------------
 *
 * public $oRestClient
 * public $szLastResponse
 * public $listNames
 * public $vIndexListMembers
 * private $oResponseLists
 * private $vListMembers
 *
 *
 * method index
 * ------------
 *
 * public static function getInstance()
 * public function getAllLists()
 * public function getAllMembers()
 * public function findMemberBySubscriberHash()
 * public function createMember()
 * public function createMembersBulk()
 * public function deleteMember()
 * public function calcSubscriberHash()
 * public function updateMember()
 * public function getLastResponse()
 * private function buildHashIndex()
 *
 */
class MailChimpLists
{
    /** REST-Client object */
    public $oRestClient;

    /** the json-representation of the last REST-client action (sometimes we need it, sometimes not) */
    public $szLastResponse = '';

    /** name-hash for fast access a name-id-relation of the MailChimp-lists */
    public $listNames = [];

    /** holds a list of list-objects (all are exists at MailChimp) to prevent double-fetching! */
    private $oResponseLists;

    /** holds the last fetched list-members */
    private $vListMembers = [];

    /** index of the remote list-members */
    public $vIndexListMembers = [];

    /** singleton pattern "self instance"-holder */
    private static $oInstance ;

    /**
     * construct this object
     * (only callable via static '::getInstance()', because it's a "singleton"!)
     *
     * @param RestClient $oClient - RestClient-object
     */
    private function __construct(RestClient $oClient)
    {
        $this->oRestClient = $oClient;
    }

    /**
     * singleton pattern instance-deliverer
     *
     * @param RestClient $oClient - RestClient-object
     * @return MailChimpLists
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
     * (the result is hold in this object, to prevent double-fetching)
     *
     * @return array - array of objects of MailChimp-lists
     */
    public function getAllLists()
    {
        // prevent double-calls by the Plugin-class (or e.g. by 'tab_settings')
        if (null === $this->oResponseLists) {
            $this->szLastResponse = $this->oRestClient->retrieve('/lists');
            $oResponse            = $this->getLastResponse();

            $this->oResponseLists = $oResponse;
        } else {
            $oResponse = $this->oResponseLists;
        }
        // store the list-names(!) in a hash for faster access at later usage
        foreach ($oResponse->lists as $arrayPos => $oList) {
            $this->listNames[$oList->id] = $oList->name;
        }

        return $oResponse->lists;
    }

    /**
     * fetch all members of a given MailChimp-list
     *
     * @param string $szListId - list-id of a MailChimp-list
     * @param int    $iOffset - (optional) can be usefull for a "deeper" pagination (maybe later in the future)
     * @return array - array of objects of (MailChimp-)subscribers
     */
    public function getAllMembers($szListId, $iOffset = 0)
    {
        // the only way to get the correct members-count of a list is to ask the list directly.
        // so we "pre-read" the members-endpoint to get the current and correct member-count of that list.
        // (we filter only for one field, to spare response-time and data-amount)
        $vGetParams           = [
            'fields' => implode(',', [
                'total_items'
            ])
        ];
        $this->szLastResponse = $this->oRestClient->retrieve('/lists/' . $szListId . '/members', $vGetParams);
        $oResponse            = $this->getLastResponse();
        $iMemberCount         = $oResponse->total_items;

        $vGetParams           = [
            'offset' => $iOffset // --OPTIONAL--
            ,
            'count'  => $iMemberCount // mandatory by MailChimp (the default, without this var, is allways 10!)
            ,
            'fields' => implode(',', [
                'members.id'
                ,
                'members.email_address'
                ,
                'members.status'
                ,
                'members.last_changed'
                ,
                'members.merge_fields'
                ,
                'total_items'
            ])
        ];
        $this->szLastResponse = $this->oRestClient->retrieve('/lists/' . $szListId . '/members', $vGetParams);
        $oResponse            = $this->getLastResponse();

        if (null !== $oResponse) {
            $this->vListMembers = $oResponse->members; // store the list to spare transfer of data
            $this->buildHashIndex($this->vListMembers); // build a hash-index of that list, for faster access

            return $this->vListMembers;
        }

        return [];
    }

    /**
     * build a local index of all subscribers, to find them quickly in our fetched list
     * (store that hash-index locally)
     *
     * @param array $vListMembers
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
     * (we're using our local index, to speed up the access of the list-member)
     *
     * @param string $szSubscriberHash - MailChimp-SubscriberHash
     * @return object - Subscriber-object
     */
    public function findMemberBySubscriberHash($szSubscriberHash)
    {
        return $this->vListMembers[$this->vIndexListMembers[$szSubscriberHash]];
    }

    /**
     * create one single member in the given MailChimp-list
     *
     * @param string              $szListId - MailChimp-list-ID
     * @param MailChimpSubscriber $oSubscriber -  MailChimpSubscriber
     * @return int
     * @throws ExceptionMailChimp
     */
    public function createMember($szListId, MailChimpSubscriber $oSubscriber)
    {
        // we only need the unique_email_id to assume "a new list-member was created"
        $vGetParams           = [
            'fields' => implode(',', [
                'email_address'
                ,
                'unique_email_id'
                ,
                'last_changed'
            ])
        ];
        $this->szLastResponse = $this->oRestClient->create('/lists/' . $szListId . '/members', $vGetParams,
            json_encode($oSubscriber));
        $oResponse            = $this->getLastResponse();

        // error-handling
        if (isset($oResponse->status) && 400 <= $oResponse->status) {
            throw new ExceptionMailChimp($oResponse);
        }

        // if there is a unique_email_id created by MailChimp, so we return 1 creation
        return (isset($oResponse->unique_email_id) ? 1 : 0);
    }

    /**
     * create members
     *
     * @param string $szListId - the current MailChimp-list-ID
     * @param array  $vSubscribers - subscriber-array (stdClass-objects)
     * @return int
     */
    public function createMembersBulk($szListId, $vSubscribers)
    {
        // there is a limitation by MailChimp, of max. 500 allowed newsletter-receivers, in one transmission
        $iChunkSize = 499;

        // we have to send our subscribers in chunks of max. 500 subscribers
        $oSubsChunk = new MailChimpSubscriberBulk();
        $vCheckHash = []; // to prevent duplicates! (MailChimp stops the import of a complete chunk if there are any)
        foreach ($vSubscribers as $oSubs) {
            $oSub = new MailChimpSubscriber();
            $oSub->set('email_address', $oSubs->cEmail)
                 ->set('status', 'subscribed')
                 ->set('merge_fields', [
                         'FNAME'  => $oSubs->cVorname
                         ,
                         'LNAME'  => $oSubs->cNachname
                         ,
                         'GENDER' => $oSubs->cGender
                     ]
                 );
            // we have to take care, to don't give MailChimp duplicates!
            $szCheckSum = md5(strtolower($oSub->email_address));
            if (!isset($vCheckHash[$szCheckSum])) {
                $oSubsChunk->append($oSub);
                $vCheckHash[$szCheckSum] = true; // mark this subscriber as "got it"
            }
            // check the size of our current chunk
            if ($iChunkSize === $oSubsChunk->getCount()) {
                $vChunks[] = json_encode($oSubsChunk); // add a chunk as json-string
            }
        }
        // "merge" the last ("not yet full") chunk
        if (0 !== $oSubsChunk->getCount()) {
            $vChunks[] = json_encode($oSubsChunk);
        }
        // choose the fields, we want to see in the response
        $vGetParams     = [
            'fields' => implode(',', [
                'updated_members.email_address'
                ,
                'new_members.email_address'
                ,
                'total_created'
                ,
                'total_updated'
                ,
                'errors'
            ])
        ];
        $i              = 0; // --DEBUG-- (maybe used for error-handling)
        $vErrors        = [];
        $iErrorCount    = 0;
        $iTransmitCount = 0;
        // fire to MailChimp
        foreach ($vChunks as $szChunk) {
            $i++; // --DEBUG--
            $this->szLastResponse = $this->oRestClient->create('/lists/' . $szListId, $vGetParams, $szChunk);
            $oResponse            = $this->getLastResponse();

            if (isset($oResponse->status) && 400 <= $oResponse->status) {
                $vErrors[] = $oResponse;
            }
            $iTransmitCount += $oResponse->total_created;
            $iErrorCount += count($oResponse->errors); // --TODO-- maybe give the user a feedback about errors too ( array( stdClass(email_address, error)))
        }
        unset($vCheckHash); // destroy the check-hash, because there is no need to hold big data in memory!

        return $iTransmitCount;
    }

    /**
     * delete a list-member from a MailChimp-list
     *
     * @param string $szListId - MailChimp-list-ID
     * @param string $szSubscriberHash - Subscriber-Hash
     * @return int
     * @throws ExceptionMailChimp
     */
    public function deleteMember($szListId, $szSubscriberHash)
    {
        $this->szLastResponse = $this->oRestClient->destroy('/lists/' . $szListId . '/members/' . $szSubscriberHash);
        $oResponse            = $this->getLastResponse();
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
     * @param string $szEmail - email addres of a subscriber
     * @return string - the MailChimp-conform subscriberHash
     */
    public function calcSubscriberHash($szEmail)
    {
        return md5(strtolower($szEmail));
    }

    /**
     * update a list-member
     *
     * @param string $szListId - Mailchimp-list-ID
     * @param string $oSubscriber - MailChimp-SubscriberHash
     * @return int
     */
    public function updateMember($szListId, $oSubscriber)
    {
        $szSubscriberHash = $this->calcSubscriberHash($oSubscriber->email_address);
        // we only need the unique_email_id to assume "a new list-member was created"
        $vGetParams           = array(
            'fields' => implode(',', array(
                'email_address'
                ,
                'unique_email_id'
                ,
                'last_changed'
            ))
        );
        $this->szLastResponse = $this->oRestClient->update(
            '/lists/' . $szListId . '/members/' . $szSubscriberHash,
            $vGetParams,
            json_encode($oSubscriber)
        );
        $oResponse            = $this->getLastResponse();

        // if there is a unique_email_id created by MailChimp, so we return 1 creation
        return (isset($oResponse->unique_email_id) ? 1 : 0);
    }

    /**
     * return the last action resonse
     * (as JOSN or stdObject)
     *
     * @param bool $fAsJson - flag which indicates if we want JSON or object (default: object)
     * @return string|object  response of the last REST-client action
     */
    public function getLastResponse($fAsJson = false)
    {
        return (true === $fAsJson)
            ? $this->szLastResponse
            : json_decode($this->szLastResponse);
    }
}
