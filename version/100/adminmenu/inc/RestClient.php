<?php

class RestClient
{
    private $szMcUrl = 'https://api.mailchimp.com/3.0';
    private $apiKey  = '';
    private $oCurl   = null;

    public $oLogger  = null; // --DEBUG--


    /**
     * construct and initialize a cURL-client object
     */
    public function __construct($apiKey)
    {
        // --DEBUG--
        Logger::configure('/var/www/html/shop4_03/_logging_conf.xml');
        $this->oLogger = Logger::getLogger('default');
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
        curl_setopt($this->oCurl, CURLOPT_CONNECTTIMEOUT, 30);          // --TO-CHECK--
        curl_setopt($this->oCurl, CURLOPT_TIMEOUT, 30);   // --TO-CHECK--
        curl_setopt($this->oCurl, CURLOPT_SSL_VERIFYPEER, false); // added by convention of API v3.0
        //
        //curl_setopt($this->oCurl, CURLOPT_VERBOSE, $this->debug); // --OPTIONAL--
    }

    /**
     * create one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  array of parameters, which represent the entity
     * @return string  json-string, "response-body"
     */
    public function create($szEndpoint, $vParameters)
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'POST');
        return $this->call($szEndpoint, $vParameters);
    }

    /**
     * read one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  []
     * @return string  json-string, "response-body"
     */
    public function retrieve($szEndpoint, $vParameters = array())
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'GET');
        return $this->call($szEndpoint, $vParameters);
    }

    /**
     * update one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  array of new parameters, which update the entity
     * @return string  json-string, "response-body"
     */
    public function update($szEndpoint, $vParameters)
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        return $this->call($szEndpoint, $vParameters);
    }

    /**
     * delete one single entity
     *
     * @param string  MailChimp sub-endpoint
     * @param array  []
     * @return string  json-string, "response-body"
     */
    public function destroy($szEndpoint, $vParameters = array())
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->call($szEndpoint, $vParameters);
    }


    /**
     * do the cURL-call
     *
     * @param string  MailChimp sub-endpoint
     * @param  array  array of new parameters, which update the entity
     * @return string  json-string, "response-body"
     */
    private function call($szEndpoint, $vParameters)
    {
        $this->oLogger->debug('call to: '.$this->szMcUrl.$szEndpoint.', with: '.print_r($vParameters,true)); // --DEBUG--

        curl_setopt($this->oCurl, CURLOPT_URL, $this->szMcUrl . $szEndpoint); // --TO-CHECK-- ...
        curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, $vParameters);


        //$start = microtime(true);
        //if ($this->debug) {
            //$curl_buffer = fopen('php://memory', 'w+');
            //curl_setopt($ch, CURLOPT_STDERR, $curl_buffer);
        //}
        $response_body = curl_exec($this->oCurl);
        //$info          = curl_getinfo($this->oCurl);
        //$time          = microtime(true) - $start;
        //if ($this->debug) {
            //rewind($curl_buffer);
            //$this->log(stream_get_contents($curl_buffer));
            //fclose($curl_buffer);
        //}
        //$this->log('Completed in ' . number_format($time * 1000, 2) . 'ms');
        //$this->log('Got response: ' . $response_body);

        //$this->oLogger->debug('curl info: '.print_r($info,true)); // --DEBUG--

        return $response_body; // still json
    }
}
