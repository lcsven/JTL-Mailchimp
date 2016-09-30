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

$foo = new RestClient();
$oLogger->debug('is object: '.$foo instanceof RestClient); // --DEBUG--
$oLogger->debug('obj content: '.print_r($foo->getArray() ,true)); // --DEBUG--

// get all list
// find, to which list we pushed (or we have to push)
// get the list, to which we pushed
// get all subscribers from that list


$oDbLayer = Shop::DB();

$cQuery = ' SELECT'
        . '   IF(nle.cAnrede = "m", "Herr", "Frau") as cAnrede'
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
$oNewsletterReceiver_arr = $oDbLayer->query($cQuery, 2);

$oLogger->debug('hit the version/100/adminmenu/jtl_example_testtab.php'.print_r($oNewsletterReceiver_arr,true)); // --DEBUG--
//$oLogger->debug(''.print_r($oPlugin ,true)); // --DEBUG--
$oLogger->debug('API-key: '.$oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key'] ,true); // --DEBUG--

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

$smarty->assign('oNewsletterReceiver_arr', $oNewsletterReceiver_arr);
//display the template
$smarty->display($oPlugin->cAdminmenuPfad . 'templates/tab_abonnenten.tpl');

