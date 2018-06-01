<?php
/**
 * backend tab 'Einstellungen', select-source
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

require_once $this->cAdminmenuPfad . 'inc/classLoader.php'; // "$this"  because we are in object-context of "Plugin"
$szApiKey = '';
$options  = [];

// try to find the api-key
isset($this->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key'])
    ? $szApiKey = $this->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key']
    : $szApiKey = '';

// if we could'nt find the key, we try to obtain it this way
if ('' === $szApiKey) {
    $iSettingsCount = count($this->oPluginEinstellung_arr);
    $i              = -1;
    while (++$i < $iSettingsCount && 'jtl_mailchimp3_api_key' !== $this->oPluginEinstellung_arr[$i]->cName) {
        // we only count the index here
    }
    $szApiKey = $i < $iSettingsCount ? $this->oPluginEinstellung_arr[$i]->cWert : '';
}

if ('' !== $szApiKey) {
    $option        = new stdClass();
    $option->cWert = '';
    $option->cName = 'W&auml;hlen Sie eine Liste und speichern Sie Ihre Einstellung!';
    $option->nSort = 0;
    $options[]     = $option;

    if (!isset($oLists) || (null === $oLists)) {
        $oLists = MailChimpLists::getInstance(new RestClient($szApiKey));
        $vLists = $oLists->getAllLists();

        $iListsCount = count($vLists);
        for ($i = 0; $i < $iListsCount; $i++) {
            $option        = new stdClass();
            $option->cWert = $vLists[$i]->id;
            $option->cName = $vLists[$i]->name . ' (' . $vLists[$i]->id . ')';
            $option->nSort = $i +1;
            $options[]     = $option;
        }

    }
}
return $options;

