<?php
/**
 * MailChimp3 exception
 *
 * @package     jtl_example_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2015 JTL-Software-GmbH
 */

class ExceptionMailChimp extends Exception
{
    /** exception message (default is overwritten in __construct()) */
    protected $szMessage = 'Unknown MailChimp-Exception';

    /** plugin-own error-code - used for the return-codes of MailChimp */
    protected $iCode     = 100;

    /** previouse exception, if nested */
    private $oPrevEx     = null;


    /**
     * construct a exception
     *
     * @param string  exception-message text
     * @param integer  plugin-own error-code
     * @param object  previouse exception, if nested called
     * @return void
     */
    public function __construct($oResponseMessage)
    {
        // cut the recommendation post-sentence --TODO-- form better mess'es, with bold mail-addresses e.g. ...
        if ('Member Exists' == $oResponseMessage->title) {
            $this->szMessage = $oResponseMessage->status . ' - ' . substr($oResponseMessage->detail, 0, strpos($oResponseMessage->detail, '. ')) . '.';
        } elseif ('Invalid Resource' == $oResponseMessage->title) {
            $this->szMessage = $oResponseMessage->status . ' - ' . $oResponseMessage->detail;
        }
        $this->iCode = $oResponseMessage->status;

        parent::__construct($this->szMessage, $this->iCode, $this->oPrevEx);
    }

    /**
     * afford to use this exception as a string
     * (only for convenience)
     *
     * @param void
     * @return string  string representation of the occurred error
     */
    public function __toString()
    {
        return $this->iCode . ' -  ' . $this->szMessage;
    }
}

