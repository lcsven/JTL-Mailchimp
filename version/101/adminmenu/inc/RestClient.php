<?php
/**
 * MailChimp3 plugin - REST-Client
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

class RestClient
{
    private $szMcUrl = 'https://api.mailchimp.com/3.0';
    private $apiKey  = '';
    private $oCurl   = null;

    // local debug-possibility with "apache log4php"
    private $fDebug  = true; // "log4php" is medatory! (composer require 'apache/log4php:dev-master')
    public $oLogger  = null;
    public $method   = '';


    /**
     * construct and initialize a cURL-client object
     */
    public function __construct($apiKey)
    {
        // --DEBUG--
        if (true === $this->fDebug) {
            Logger::configure('/var/www/html/shop4_03/_logging_conf.xml');
            $this->oLogger = Logger::getLogger('default');
        }
        // --DEBUG--

        $this->apiKey = $apiKey;

        // set a api-mirror, if any is pending at the api-key (defaults to 'us1')
        $dc = "us1";
        if (strstr($this->apiKey, "-")) {
            list($key, $dc) = explode("-", $this->apiKey, 2);
            if (!$dc) {
                $dc = "us1";
            }
        }
        $this->szMcUrl = str_replace('https://api', 'https://'.$dc.'.api', $this->szMcUrl);

        // create a CURL-instance
        $this->oCurl = curl_init();
        curl_setopt($this->oCurl, CURLOPT_USERPWD, 'apiuser:'.$this->apiKey); // auth of the new v3.0 API
        curl_setopt($this->oCurl, CURLOPT_USERAGENT, 'JTL-MailChimp3'); // it's for tracking by MailChimp
        //
        curl_setopt($this->oCurl, CURLOPT_HEADER, false);
        curl_setopt($this->oCurl, CURLOPT_POST, true);
        curl_setopt($this->oCurl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->oCurl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->oCurl, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->oCurl, CURLOPT_SSL_VERIFYPEER, false); // added by convention of API v3.0
        //
        //curl_setopt($this->oCurl, CURLOPT_VERBOSE, $this->debug); // --OPTIONAL--
    }

    /**
     * create one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  []
     * @param array  array of parameters, which represent the entity
     * @return string  json-string, "response-body"
     */
    public function create($szEndpoint, $vGetParams = array(), $vParameters = array())
    {
        $this->method = 'POST'; // --DEBUG--
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'POST');
        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }

    /**
     * read one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  []
     * @param array  []
     * @return string  json-string, "response-body"
     */
    public function retrieve($szEndpoint, $vGetParams = array(),  $vParameters = array())
    {
        $this->method = 'GET'; // --DEBUG--
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'GET');
        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }

    /**
     * update one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  []
     * @param array  array of new parameters, which update the entity
     * @return string  json-string, "response-body"
     */
    public function update($szEndpoint, $vGetParams = array(), $vParameters = array())
    {
        $this->method = 'PATCH'; // --DEBUG--
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }

    /**
     * delete one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  []
     * @param array  []
     * @return string  json-string, "response-body"
     */
    public function destroy($szEndpoint, $vGetParams = array(), $vParameters = array())
    {
        $this->method = 'DELETE'; // --DEBUG--
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }


    /**
     * do the cURL-call
     *
     * @param string  MailChimp sub-endpoint
     * @param array  a array of query-string-parameters as key->val pairs
     * @param array  array of new parameters, which update the entity
     * @return string  json-string, "response-body"
     */
    private function call($szEndpoint, $vGetParams, $vParameters)
    {
        $szGetParams = '';
        if (isset($vGetParams) && is_array($vGetParams) && 0 !== count($vGetParams)) {
            foreach ($vGetParams as $key => $val) {
                $vTemp[] = $key . '=' . $val;
            }
            $szGetParams = '?' . implode('&', $vTemp);
        }

        if ($this->fDebug) {
            //$this->oLogger->debug('call '.$this->method.' to: '.$this->szMcUrl.$szEndpoint.', with: '.print_r($vParameters,true)); // --DEBUG--
            $this->oLogger->debug('call '.$this->method.' to: '.$this->szMcUrl.$szEndpoint.$szGetParams); // --DEBUG--
        }

        //curl_setopt($this->oCurl, CURLOPT_URL, $this->szMcUrl . $szEndpoint); // complete response
        curl_setopt($this->oCurl, CURLOPT_URL, $this->szMcUrl . $szEndpoint . $szGetParams); // reduced response
        curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, $vParameters);


        //$start = microtime(true);
        //if ($this->debug) {
            //$curl_buffer = fopen('php://memory', 'w+');
            //curl_setopt($ch, CURLOPT_STDERR, $curl_buffer);
        //}
        $szResponse = curl_exec($this->oCurl);
        //$info          = curl_getinfo($this->oCurl);
        //$time          = microtime(true) - $start;
        //if ($this->debug) {
            //rewind($curl_buffer);
            //$this->log(stream_get_contents($curl_buffer));
            //fclose($curl_buffer);
        //}
        //$this->log('Completed in ' . number_format($time * 1000, 2) . 'ms');
        //$this->log('Got response: ' . $szResponse);

        //$this->oLogger->debug('curl info: '.print_r($info,true)); // --DEBUG--

        return $szResponse; // still json
    }
}
