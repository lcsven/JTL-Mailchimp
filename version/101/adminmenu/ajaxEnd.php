<?php
/**
 * backend tab 'Abonnenten'
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

date_default_timezone_set('Europe/Berlin');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --DEBUG--
include('/var/www/html/shop4_03/includes/vendor/apache/log4php/src/main/php/Logger.php');
Logger::configure('/var/www/html/shop4_03/_logging_conf.xml');
$oLogger = Logger::getLogger('default');
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --DEBUG--

require_once(dirname(__FILE__) . '/inc/classLoader.php');

// try to guess our root-path, by the knowing "we are somewhere in includes/plugins/"
$szRootPath = preg_split('/includes\/plugins/',__FILE__)[0]; //
// "close the ring" to the main-application (for the correct _SESSION)
require_once($szRootPath . '/admin/includes/admininclude.php');

//$oLogger->debug('SESSION (ajax): '.print_r( $_SESSION ,true)); // --DEBUG--
//$oLogger->debug('POST: '.print_r($_POST,true)); // --DEBUG--
//$oLogger->debug('GET: '.print_r($_GET,true)); // --DEBUG--

$szListName    = '';
$iSuccessCount = 0;
$oRestResponse = new stdClass();
$szErrorMsg    = '';
$nTimeElapsed  = 0; // --OPTIONAL-- (not used yet)
if (validateToken()) {
    if (isset($_GET['action'])) {
        $nStartTime    = microtime(true); // --OPTIONAL--
        //$oRestResponse = new stdClass();
        $szErrorMsg    = '';

        $szListId = $_GET['szListId'];
        $oLists   = MailChimpLists::getInstance(new RestClient($_GET['szApiKey'])); // --TODO-- code-repeat!

        $oLists->getAllLists();
        $szListName = $oLists->listNames[$szListId]; // get the real-name of the current list

        // if we add or update, we extract the user-data and create e member-object from it
        if ('add' === $_GET['action'] || 'update' === $_GET['action']) {
            // --TODO-- maybe check, if userData is there and throw an exception if not (but that should not occur normally)
            $oUserData = json_decode($_GET['userData']);

            $oMember = new MailChimpSubscriber();
            $oMember->set('email_address', $oUserData->szEmail)
                    ->set('status'       ,  'subscribed')
                    ->set('merge_fields' ,  array(
                                      'FNAME'  => $oUserData->szFirstName
                                    , 'LNAME'  => $oUserData->szLastName
                                    , 'GENDER' => $oUserData->szGender // additional merge-field (not MC-default)
                                    )
                    );
        }

        try {
            switch ($_GET['action']) {
                case 'add':
                    $iSuccessCount = $oLists->createMember($szListId, $oMember);
                    break;
                case 'update':
                    $iSuccessCount = $oLists->updateMember($szListId, $oMember);
                    break;
                case 'remove':
                    $iSuccessCount = $oLists->deleteMember($szListId, $_GET['szSubscriberHash']);
                    break;
                default:
                    $oResponse = array('errors' => 'wrong action'); // should never occur
            }
        } catch (ExceptionMailChimp $eMC) {
            $szErrorMsg = $eMC->getMessage();
        }

        $oRestResponse = $oLists->getLastResponse();
        $nTimeElapsed  = microtime(true) - $nStartTime; // --OPTIONAL--
    }
} else {
    $szErrorMsg = '450 - Invalid AJAX-call source!';
}

// build a object with all data to respond
$oResponse                = new stdClass();
$oResponse->szListName    = $szListName;
$oResponse->iSuccessCount = $iSuccessCount;
$oResponse->oRestResponse = $oRestResponse;
$oResponse->szErrorMsg    = $szErrorMsg;
$oResponse->nTimeElapsed  = $nTimeElapsed; // --OPTIONAL-- (not used yet)

// send the response as json
echo(json_encode($oResponse));

