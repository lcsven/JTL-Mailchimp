CREATE TABLE IF NOT EXISTS `xplugin_jtl_mailchimp3_sync` (
      `kSync` int(10) NOT NULL AUTO_INCREMENT
    , `kNewsletterReceiver` int(10) DEFAULT NULL COMMENT 'ID of the NL-Receiver from `tnewsletterempfaenger`.`kNewsletterEmpfaenger`'
    , `cListId` varchar(255) DEFAULT NULL COMMENT 'List-ID of a MailChimp subscriber-list'
    , `cEUID` varchar(255) DEFAULT NULL
    , `cLEID` varchar(255) DEFAULT NULL
    , `dSync` datetime DEFAULT NULL
    , `dLastSync` datetime DEFAULT NULL
    , PRIMARY KEY (`kSync`)
    , UNIQUE KEY `kNewsletterReceiver` (`kNewsletterReceiver`)
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

