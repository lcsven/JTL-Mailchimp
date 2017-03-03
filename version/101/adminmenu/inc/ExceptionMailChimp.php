<?php
/**
 * MailChimp3 exception
 *
 * @package     jtl_example_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2015 JTL-Software-GmbH
 */

/**
 * Class ExceptionMailChimp
 */
class ExceptionMailChimp extends Exception
{
    /**
     * exception message (default is overwritten in __construct())
     *
     * @var string
     */
    protected $szMessage = 'Unknown MailChimp-Exception';

    /**
     * plugin-own error-code - used for the return-codes of MailChimp (default is overwritten in __construct())
     *
     * @var int
     */
    protected $iCode     = 100;

    /**
     * previouse exception, if nested
     *
     * @var
     */
    private $oPrevEx;


    /**
     * construct a exception
     *
     * @param string $oResponseMessage - exception-message text
     */
    public function __construct($oResponseMessage)
    {
        // --TODO-- form better mess'es, with bold mail-addresses e.g. ...
        // cut the recommendation post-sentence
        if ('Member Exists' === $oResponseMessage->title) {
            $this->szMessage = $oResponseMessage->status . ' - ' .
                substr($oResponseMessage->detail, 0, strpos($oResponseMessage->detail, '. ')) . '.';
        } elseif ('Invalid Resource' === $oResponseMessage->title) {
            $this->szMessage = $oResponseMessage->status . ' - ' . $oResponseMessage->detail;
        } else {
            // for default set this, to prevent "Unknown MailChimp-Exception"
            $this->szMessage = $oResponseMessage->detail;
        }
        $this->iCode = $oResponseMessage->status;

        parent::__construct($this->szMessage, $this->iCode, $this->oPrevEx);
    }

    /**
     * afford to use this exception as a string
     * (only for convenience)
     *
     * @return string - string representation of the occurred error
     */
    public function __toString()
    {
        return $this->iCode . ' -  ' . $this->szMessage;
    }
}
