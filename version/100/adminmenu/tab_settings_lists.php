<?php
/**
 * backend tab 'Einstellungen', select-source
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --DEBUG--
Logger::configure('/var/www/html/shop4_03/_logging_conf.xml');
$oLogger = Logger::getLogger('default');
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - --DEBUG--

//require_once($oPlugin->cAdminmenuPfad . 'inc/classLoader.php');
//require_once(__DIR__.'/inc/classLoader.php');

//global $oPlugin;
//$oLogger->debug('global PLUGIN: '.print_r($oPlugin,true)); // --DEBUG--
//$oLogger->debug('instance: '.(($oPlugin instanceof Plugin) ? 'yes' : 'no')); // --DEBUG--
//$oLogger->debug(' ... : '.var_dump($oPlugin->oPluginEinstellungAssoc_arr)); // --DEBUG--

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
$options = array();

//$option->cWert = 123;
//$option->cName = 'Wert Eins';
//$option->nSort = 1;
//$options[]     = $option;

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
//require_once($oPlugin->cAdminmenuPfad . 'inc/classLoader.php');
require_once($this->cAdminmenuPfad . 'inc/classLoader.php'); // "$this"  because we are in object-context of "Plugin"

//if (property_exists($this, 'oPluginEinstellungAssoc_arr')) {
(isset($this->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key']))
    ? $szApiKey = $this->oPluginEinstellungAssoc_arr['jtl_mailchimp3_api_key']
    : $szApiKey = '';


$oLogger->debug('API-key: '.$szApiKey); // --DEBUG--
if ('' !== $szApiKey) {
    $option        = new stdClass();
    $option->cWert = '';
    $option->cName = 'W&auml;hlen Sie eine Liste und speichern Sie Ihre Einstellung!';
    $option->nSort = 0;
    $options[]     = $option;

    if (!isset($oLists) || (null === $oLists)) { // --TO-CHECK-- --TODO-- seems to be executed twice! not nice!!!
        $oLists = MailChimpLists::getInstance(new RestClient($szApiKey));

        $vLists = $oLists->getAllLists();

        for ($i = 0; $i < count($vLists); $i++) {
            //$oLogger->debug('see list: '.$vLists[$i]->id); // --DEBUG--
            $option        = new stdClass();
            $option->cWert = $vLists[$i]->id;
            $option->cName = $vLists[$i]->name . ' (' . $vLists[$i]->id . ')';
            $option->nSort = $i +1;
            $options[]     = $option;
        }

    }
}
return $options;

