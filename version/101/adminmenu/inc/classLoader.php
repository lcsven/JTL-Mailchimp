<?php
/**
 * class-loader for the MailChimp3 plugin
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

/**
 * @param string $szClassName
 * @return bool
 */
function classLoader($szClassName)
{
    global $oPlugin;

    $szClassFile = (($oPlugin instanceof Plugin) && null !== $oPlugin)
        ? $oPlugin->cAdminmenuPfad . 'inc/' . $szClassName . '.php'
        : __DIR__ . '/' . $szClassName . '.php';

    if (file_exists($szClassFile)) {
        require_once $szClassFile;

        return class_exists($szClassName);
    }

    return false;
}
$PREPEND         = false;
$THROW_EXCEPTION = true;
if (function_exists('classLoader')) {
    spl_autoload_unregister('classLoader'); // remove the previouse version autoloader
}
spl_autoload_register('classLoader', $THROW_EXCEPTION, $PREPEND); // re-chain the new one
