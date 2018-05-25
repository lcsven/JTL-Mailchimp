<?php
/**
 * HOOK_NEWSLETTER_PAGE_EMPFAENGEREINTRAGEN (ID 37)
 * Used to insert newsletter-receiver into MailChimp-list.
 *
 * @package     jtl_mailchimp3
 * @copyright   JTL-Software-GmbH
 */

if (isset($args_arr['oNewsletterEmpfaenger']) && is_object($args_arr['oNewsletterEmpfaenger'])) {
    require_once $oPlugin->cAdminmenuPfad . 'inc/classLoader.php';
    $oNewsletterEmpfaenger = $args_arr['oNewsletterEmpfaenger'];
    $fAutoSync             = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_autosync'];
    if ('on' === $fAutoSync) {
        $szApiKey = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key'];
        $szListId = $oPlugin->oPluginEinstellungAssoc_arr['jtl_mailchimp3_list'];
        $oLists   = MailChimpLists::getInstance(new RestClient($szApiKey));
        // insert a new list-member into MailChimp
        $oMember = new MailChimpSubscriber();
        $oMember->set('email_address', $oNewsletterEmpfaenger->cEmail)
                ->set('status', 'subscribed')
                ->set('merge_fields', [
                        'FNAME'  => $oNewsletterEmpfaenger->cVorname,
                        'LNAME'  => $oNewsletterEmpfaenger->cNachname,
                        'GENDER' => 'm' === $oNewsletterEmpfaenger->cAnrede ? 'male' : 'female'
                    ]
                );
        try {
            $oResponse = json_decode($oLists->createMember($szListId, $oMember));
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
