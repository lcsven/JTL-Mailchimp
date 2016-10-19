<?php
/**
 * backend tab 'Dokumatation'
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

$fMarkDown      = false;
$szFileContent = '';
$szReadmeName  = PFAD_ROOT . PFAD_PLUGIN . $oPlugin->cVerzeichnis . '/' . 'README.md';
if (file_exists($szReadmeName)) {
    $szFileContent = utf8_decode(file_get_contents($szReadmeName));
    if (class_exists('Parsedown')) {
        $fMarkDown      = true;
        $oParseDown    = new Parsedown();
        $szFileContent = $oParseDown->text($szFileContent);
    }
}
$smarty->assign('fMarkDown', $fMarkDown)
       ->assign('szReadmeContent', $szFileContent);
$smarty->display($oPlugin->cAdminmenuPfad . 'templates/tab_documentation.tpl');

