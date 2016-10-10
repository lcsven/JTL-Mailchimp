<?php
/**
 * backend tab 'Abonnenten'
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --DEBUG--
Logger::configure('/var/www/html/shop4_03/_logging_conf.xml');
$oLogger = Logger::getLogger('default');
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --DEBUG--


require_once($oPlugin->cAdminmenuPfad . 'inc/classLoader.php');

defined('TABLE_SYNC') ? : define('TABLE_SYNC', 'xplugin_jtl_mailchimp3_sync'); // --OBSOLETE-- maybe, we did not need this here

// get all lists
// find, to which list we pushed (or we have to push)
// get the list, to which we pushed
// get all subscribers from that list
// get all shop-nl-receiver
// check, who is at mcand who is not

//$oLogger->debug('Pagi...'.print_r(new Pagination ,true)); // --DEBUG--

$oDbLayer = Shop::DB();
$cQuery   = ' SELECT'
        . '   kNewsletterEmpfaenger AS id'
        //. ' , IF(nle.cAnrede = "m", "Herr", "Frau") as cAnrede'
        . ' , IF(nle.cAnrede = "w", "female", "male") as cGender'
        . ' , nle.cVorname'
        . ' , nle.cNachname'
        . ' , nle.cEmail'
        . ' , md5(lower(nle.cEmail)) as subscriberHash' // we create a MailChimp-conform "SubscriberHash" here
        . ' , nle.dEingetragen'
        . ' , tkundengruppe.cName as cKundengruppe'
    . ' FROM'
        . ' tnewsletterempfaenger nle'
        . ' LEFT JOIN tkunde ON tkunde.kKunde = nle.kKunde'
        . ' LEFT JOIN tkundengruppe ON tkunde.kKundengruppe = tkundengruppe.kKundengruppe'
;
// fetch all NL-receiver from the local shop-DB
$oNewsletterReceiver_arr = $oDbLayer->query($cQuery, 2);
//build a  receiver index-hash
foreach ($oNewsletterReceiver_arr as $key => $oVal) {
    $oReceiverIndexHash_arr[$oVal->subscriberHash] = $key;
}


$szApiKey = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key'];
$szListId = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_list'];

if ('' !== $szApiKey && '' !== $szListId) {
    $oLists = MailChimpLists::getInstance(new RestClient($szApiKey));

    $oLogger->debug('_POST: '.print_r($_POST, true)); // --DEBUG--


    switch (true) {
        case(isset($_POST['add'])) :
            // add a "clicked" member as subscriber to the current list
            $oLogger->debug('send to remote '.$_POST['add']); // --DEBUG--

            reset($oNewsletterReceiver_arr);
            while (current($oNewsletterReceiver_arr)->subscriberHash !== $_POST['add']) {
                next($oNewsletterReceiver_arr);
            }

            $oMember = new MailChimpSubscriber();
            $oMember->set('email_address' , current($oNewsletterReceiver_arr)->cEmail)
                    //->set('list_id'       ,  'feccd07475')
                    ->set('status'        ,  'subscribed')
                    ->set('merge_fields'  ,  array(
                                      'FNAME' => current($oNewsletterReceiver_arr)->cVorname
                                    , 'LNAME' => current($oNewsletterReceiver_arr)->cNachname
                                    , 'GENDER' => current($oNewsletterReceiver_arr)->cGender // --TODO-- maybe documenting
                                    )
                    );

            //$oLogger->debug('CREATE REMOTE : '.print_r($oMember ,true)); // --DEBUG--
            $oLists->createMember($szListId, $oMember);
            break;

        case(isset($_POST['remove'])) :
            // remove a "clicked" subscriber from the current list
            $oLogger->debug('remove from remote '.$_POST['remove']); // --DEBUG--
            $oLists->deleteMember($szListId, $_POST['remove']);
            break;

        case(isset($_POST['sync']) && 'sync_part' === $_POST['sync']) :
            // transfer a chunk of members to remote
            foreach ($_POST as $key => $val) {
                $oLogger->debug('key: '.$key.'   val: '.$val); // --DEBUG--
                if (preg_match('/^id_/', $key)) {
                    $oSubscribers_arr[] = $oNewsletterReceiver_arr[$oReceiverIndexHash_arr[$val]];
                }
            }
            $oLogger->debug('choooosen ... : '.print_r($oSubscribers_arr ,true)); // --DEBUG--
            $oLists->createMembersBulk($szListId, $oSubscribers_arr);
            break;
        case(isset($_POST['sync']) && 'sync_all' === $_POST['sync']) :
            // transfer ALL members to remote
            $oLists->createMembersBulk($szListId, $oNewsletterReceiver_arr);
            break;
    }

    //$oLists->getAllLists();

    //$oLogger->debug('members:'.print_r(json_decode($oLists->getAllMembers($szListId)),true));
    //$oLogger->debug('members:'.print_r($oLists->getAllMembers($szListId),true));

    //$oMember = $oLists->getMemberBySubscriberHash($szListId, '2c1f20509ea87adc76dc50c773c15a2f');

    //$oLists->createMember($szListId);
    //$oLists->deleteMember($szListId);
    //$oLists->updateMember($szListId);


    // read all members and their subscriber-state from MailChimp and show the results
    $oMembers_arr = $oLists->getAllMembers($szListId, count($oNewsletterReceiver_arr));
    $oLogger->debug('read members count: '.count($oMembers_arr)); // --DEBUG--

    for ($i = 0; $i < count($oNewsletterReceiver_arr); $i++) {
        // insert(!) and update fields in our nl-receiver-array (e.g. remote states)
        if (array_key_exists($oNewsletterReceiver_arr[$i]->subscriberHash, $oLists->vMembersIndexHash)) {

            file_put_contents(
                  '__temp.txt'
                , $oNewsletterReceiver_arr[$i]->subscriberHash
                    .' -> '
                    .$oLists->vMembersIndexHash[$oNewsletterReceiver_arr[$i]->subscriberHash]
                    ."\n"
                , FILE_APPEND
            ); // --DEBUG--

            $oMember = $oLists->findMemberBySubscriberHash($oNewsletterReceiver_arr[$i]->subscriberHash);

            $oNewsletterReceiver_arr[$i]->dLastSync = $oMember->last_changed;
            $oNewsletterReceiver_arr[$i]->remote    = true;
        } else {
            $oNewsletterReceiver_arr[$i]->remote    = false;
        }
    }
    //$oLogger->debug('oNewsletterReceiver_arr "fields inserted": '.print_r($oNewsletterReceiver_arr ,true)); // --DEBUG--

    $smarty->assign('cList', $oLists->listNames[$szListId]); // CONSIDER: "getAllLists()" has to be called previously (as happend in tab_settings)
}

$smarty->assign('oNewsletterReceiver_arr', $oNewsletterReceiver_arr);
$smarty->display($oPlugin->cAdminmenuPfad . 'templates/tab_abonnenten.tpl');


// --TODO-- settings: shop-hook ... for insert automatically or manual


/* {{{ old SQL - with TABLE_SYNC
$cQuery = ' SELECT'
        . '   kNewsletterEmpfaenger AS id'
        . ' , IF(nle.cAnrede = "m", "Herr", "Frau") as cAnrede'
        . ' , nle.cVorname'
        . ' , nle.cNachname'
        . ' , nle.cEmail'
        . ' , nle.dEingetragen'
        . ', '.TABLE_SYNC.'.*'
        . ', tkundengruppe.cName as cKundengruppe'
    . ' FROM'
        . ' tnewsletterempfaenger nle'
        . ' LEFT JOIN '.TABLE_SYNC.' ON nle.kNewsletterEmpfaenger = '.TABLE_SYNC.'.kNewsletterReceiver'
        . ' LEFT JOIN tkunde ON tkunde.kKunde = nle.kKunde'
        . ' LEFT JOIN tkundengruppe ON tkunde.kKundengruppe = tkundengruppe.kKundengruppe'
;
}}} */

/* {{{
global $oPlugin;
require_once $oPlugin->cFrontendPfad . 'inc/class.jtl_example.helper.php';

$error   = null;
$success = null;
$helper  = jtlExampleHelper::getInstance($oPlugin);

if (isset($_POST['clear-cache']) && $_POST['clear-cache'] === '1') {
    if ($helper::isModern()) {
        if (validateToken()) {
            //we used the plugin's ID as an additional cache tag, so we can flush the whole group
            $result = Shop::Cache()->flushTags($oPlugin->pluginCacheGroup);
            //flushGroup() returns the number of deleted entries or FALSE if an error occured
            if (is_numeric($result)) {
                $cHinweis = 'Cache erfolgreich gel&ouml;scht';
            } else {
                $cFehler = 'Konnte Cache nicht l&ouml;schen!';
            }
        } else {
            $cFehler = 'CSRF-Fehler!';
        }
    } else {
        //the JTLCache class does not exist - don't do anything, just inform the user
        $cFehler = 'Cache wird von Ihrer Shop-Version nicht unterst&uuml;tzt!';
    }
}
//$cHinweis and $cFehler will be automatically displayed as alerts in Shop4
$smarty->assign('cHinweis', $success);
$smarty->assign('cFehler', $error);
$smarty->assign('modern', $helper::isModern());
//build a URL to POST to
$smarty->assign('adminURL', ($helper::isModern() ? Shop::getURL() : URL_SHOP) . '/' . PFAD_ADMIN . 'plugin.php?kPlugin=' . $oPlugin->kPlugin);
}}} */

