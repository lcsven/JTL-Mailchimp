<?php
/**
 * HOOK_NEWSLETTER_PAGE_EMPFAENGERLOESCHEN (ID 38)
 * Used to delete a newsletter-receiver from a MailChimp-list
 *
 * @package     jtl_example_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2015 JTL-Software-GmbH
 */

if (isset($args_arr['oNewsletterEmpfaenger']) && is_object($args_arr['oNewsletterEmpfaenger'])) {
    require_once $oPlugin->cAdminmenuPfad . 'inc/classLoader.php';

    $oNewsletterEmpfaenger = $args_arr['oNewsletterEmpfaenger'];
    $fAutoSync             = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_autosync'];
    if ('on' === $fAutoSync) {
        $szApiKey = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key'];
        $szListId = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_list'];
        $oLists   = MailChimpLists::getInstance(new RestClient($szApiKey));
        // delete a list-member from MailChimp
        try {
            $oResponse = json_decode(
                $oLists->deleteMember($szListId, $oLists->calcSubscriberHash($oNewsletterEmpfaenger->cEmail))
            );
        } catch (ExceptionMailChimp $eMC) {
            // only log that error to not bother the end-user with MailChimp-errors!
            Jtllog::writeLog('MailChimp3: ' . $eMC->getMessage(),
                JTLLOG_LEVEL_ERROR,
                false,
                'kPlugin',
                $oPlugin->kPlugin);
        }
    }
}
