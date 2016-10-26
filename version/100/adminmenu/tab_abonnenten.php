<?php
/**
 * backend tab 'Abonnenten'
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

require_once($oPlugin->cAdminmenuPfad . 'inc/classLoader.php');

$oDbLayer = Shop::DB();
$cQuery   = ' SELECT'
        . '   kNewsletterEmpfaenger AS id'
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
if (isset($_REQUEST['cSearchField']) && '' !== $_REQUEST['cSearchField']
    ) {
    // if we're in search-mode, we extend our SQL by a WHERE-clause
    // (--TODO-- that is not really nice! (in the future, we should "bind" paramters to a prep-query!))
    $cQuery .= ' WHERE'
        . ' nle.cEmail like "%' . $_REQUEST['cSearchField'] . '%"'
    ;
}
// fetch all NL-receiver from the local shop-DB
$oNewsletterReceiver_arr = $oDbLayer->query($cQuery, 2);
// build a  receiver index-hash
foreach ($oNewsletterReceiver_arr as $key => $oVal) {
    $oReceiverIndexHash_arr[$oVal->subscriberHash] = $key;
}
// re-fill the search-field
if (isset($_REQUEST['cSearchField'])) {
    $smarty->assign('szSearchString', $_REQUEST['cSearchField']);
} else {
    $smarty->assign('szSearchString', ''); // reset if nothing was searched
}
// get the plugin-settings
$szApiKey = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key'];
$szListId = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_list'];
// if we got 'n account and a list, we proceed ...
if ('' !== $szApiKey && '' !== $szListId) {
    // create a MailChimp-object for "List"-endpoints and give them a REST-client
    $oLists = MailChimpLists::getInstance(new RestClient($szApiKey));
    if (validateToken()) {
        // leaded by the buttons posted ...
        switch (true) {
            case (isset($_POST['add'])):
                // add the "clicked" member (action-button) as subscriber to the current list
                if (isset($oReceiverIndexHash_arr[$_POST['add']])) {
                    // (use the receiver index-hash to find the newsletter-receiver in "local"-list)
                    $oCurrentNewsletterReceiver = $oNewsletterReceiver_arr[$oReceiverIndexHash_arr[$_POST['add']]];
                }
                $oMember = new MailChimpSubscriber();
                $oMember->set('email_address', $oCurrentNewsletterReceiver->cEmail)
                        ->set('status'       ,  'subscribed')
                        ->set('merge_fields' ,  array(
                                          'FNAME' => $oCurrentNewsletterReceiver->cVorname
                                        , 'LNAME' => $oCurrentNewsletterReceiver->cNachname
                                        , 'GENDER' => $oCurrentNewsletterReceiver->cGender // additional merge-field (not MC-default)
                                        )
                        );
                try {
                    $iSuccessCount = $oLists->createMember($szListId, $oMember);
                    $smarty->assign('cHinweis', $iSuccessCount . ' Empf&auml;nger &uuml;bertragen');
                } catch (ExceptionMailChimp $eMC) {
                    $smarty->assign('cFehler', $eMC->getMessage());
                }
                break;

            case (isset($_POST['remove'])):
                // remove a "clicked" subscriber from the current MailChimp-list
                try {
                    $iSuccessCount = $oLists->deleteMember($szListId, $_POST['remove']);
                    $smarty->assign('cHinweis', $iSuccessCount . ' Empf&auml;nger gel&ouml;scht');
                } catch (ExceptionMailChimp $eMC) {
                    $smarty->assign('cFehler', $eMC->getMessage());
                }
                break;

            case (isset($_POST['sync']) && 'sync_part' === $_POST['sync']):
                // transfer the selected members to MailChimp
                $oNewsletterReceiverSelected_arr = array();
                foreach ($_POST as $key => $val) {
                    if (preg_match('/^id_/', $key)) {
                        $oNewsletterReceiverSelected_arr[] = $oNewsletterReceiver_arr[$oReceiverIndexHash_arr[$val]];
                    }
                }
                if (0 < count($oNewsletterReceiverSelected_arr)) {
                    try {
                        $iSuccessCount = $oLists->createMembersBulk($szListId, $oNewsletterReceiverSelected_arr);
                        $smarty->assign('cHinweis', $iSuccessCount . ' Empf&auml;nger &uuml;bertragen');
                    } catch (ExceptionMailChimp $eMC) {
                        $smarty->assign('cFehler', $eMC->getMessage());
                    }
                }
                break;

            case (isset($_POST['sync']) && 'sync_all' === $_POST['sync']):
                // transfer ALL members to MailChimp
                try {
                    $iSuccessCount = $oLists->createMembersBulk($szListId, $oNewsletterReceiver_arr);
                    $smarty->assign('cHinweis', $iSuccessCount . ' Empf&auml;nger &uuml;bertragen');
                } catch (ExceptionMailChimp $eMC) {
                    $smarty->assign('cFehler', $eMC->getMessage());
                }
                break;
            //default:
                //$smarty->assign('cFehler', 'wrong POST!');
        }
    }
    // read all members and their subscriber-state from MailChimp and show the results
    $oMembers_arr = $oLists->getAllMembers($szListId, 0);

    for ($i = 0; $i < count($oNewsletterReceiver_arr); $i++) {
        // insert(!) and update fields in our nl-receiver-array (e.g. remote states)
        if (array_key_exists($oNewsletterReceiver_arr[$i]->subscriberHash, $oLists->vIndexListMembers)) {
            $oMember = $oLists->findMemberBySubscriberHash($oNewsletterReceiver_arr[$i]->subscriberHash);

            $oNewsletterReceiver_arr[$i]->dLastSync = $oMember->last_changed;
            $oNewsletterReceiver_arr[$i]->remote    = true;
        } else {
            $oNewsletterReceiver_arr[$i]->remote    = false;
        }
    }
    $smarty->assign('cList', $oLists->listNames[$szListId]);
}
// create the pagination
$oPagiMailChimp = (new Pagination('locallist'))
    ->setItemArray($oNewsletterReceiver_arr)
    ->assemble();
$oNewsletterReceiver_arr = $oPagiMailChimp->getPageItems();
// render the template
$smarty->assign('oNewsletterReceiver_arr', $oNewsletterReceiver_arr)
       ->assign('oPagiMailChimp', $oPagiMailChimp) // pagination
;
$smarty->display($oPlugin->cAdminmenuPfad . 'templates/tab_abonnenten.tpl');

