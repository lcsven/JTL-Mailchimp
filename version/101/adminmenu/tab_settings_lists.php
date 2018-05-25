<?php
/**
 * backend tab 'Einstellungen', select-source
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

require_once $this->cAdminmenuPfad . 'inc/classLoader.php'; // "$this"  because we are in object-context of "Plugin"
$options = [];

$szApiKey = isset($this->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key'])
    ? $this->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key']
    : '';

if ('' !== $szApiKey) {
    $option        = new stdClass();
    $option->cWert = '';
    $option->cName = 'W&auml;hlen Sie eine Liste und speichern Sie Ihre Einstellung!';
    $option->nSort = 0;
    $options[]     = $option;

    if (!isset($oLists)) {
        $oLists = MailChimpLists::getInstance(new RestClient($szApiKey));
        $vLists = $oLists->getAllLists();
        $count  = count($vLists);
        for ($i = 0; $i < $count; $i++) {
            $option        = new stdClass();
            $option->cWert = $vLists[$i]->id;
            $option->cName = $vLists[$i]->name . ' (' . $vLists[$i]->id . ')';
            $option->nSort = $i +1;
            $options[]     = $option;
        }

    }
}

return $options;
