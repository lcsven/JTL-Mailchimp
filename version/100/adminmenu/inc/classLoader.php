<?php
/**
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

function classLoader($szClassName)
{
    global $oPlugin;

    $szClassFile = $oPlugin->cAdminmenuPfad . 'inc/' . $szClassName . '.php';
    if (file_exists($szClassFile)) {
        require_once($szClassFile);
        if (class_exists($szClassName)) {
            return true;
        }
        return false;
    }
}
$PREPEND         = true;
$THROW_EXCEPTION = true;
spl_autoload_register('classLoader', $THROW_EXCEPTION, $PREPEND);

