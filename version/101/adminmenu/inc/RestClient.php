<?php
/**
 * MailChimp3 plugin - REST-Client
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 */

/**
 * Class RestClient
 */
class RestClient
{
    /**
     * @var mixed|string
     */
    private $szMcUrl = 'https://api.mailchimp.com/3.0';

    /**
     * @var string
     */
    private $apiKey  = '';

    /**
     * @var resource
     */
    private $oCurl;

    /**
     * construct and initialize a cURL-client object
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;

        // set a api-mirror, if any is pending at the api-key (defaults to 'us1')
        $dc = 'us1';
        if (strstr($this->apiKey, '-')) {
            list($key, $dc) = explode('-', $this->apiKey, 2);
            if (!$dc) {
                $dc = 'us1';
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
    }

    /**
     * create one single entity
     *
     * @param string $szEndpoint - MailChimp sub-endpoint
     * @param array  $vGetParams
     * @param array  $vParameters - array of parameters, which represent the entity
     * @return string - json-string, "response-body"
     */
    public function create($szEndpoint, $vGetParams = [], $vParameters = [])
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'POST');

        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }

    /**
     * read one single entity
     *
     * @param string $szEndpoint - MailChimp sub-endpoint
     * @param array  $vGetParams
     * @param array  $vParameters
     * @return string - json-string, "response-body"
     */
    public function retrieve($szEndpoint, $vGetParams = [], $vParameters = [])
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'GET');
        
        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }

    /**
     * update one single entity
     *
     * @param string $szEndpoint - MailChimp sub-endpoint
     * @param array  $vGetParams
     * @param array  $vParameters - array of new parameters, which update the entity
     * @return string  json-string, "response-body"
     */
    public function update($szEndpoint, $vGetParams = [], $vParameters = [])
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        
        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }

    /**
     * delete one single entity
     *
     * @param string $szEndpoint - MailChimp sub-endpoint
     * @param array  $vGetParams
     * @param array  $vParameters
     * @return string - json-string, "response-body"
     */
    public function destroy($szEndpoint, $vGetParams = [], $vParameters = [])
    {
        curl_setopt($this->oCurl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->call($szEndpoint, $vGetParams, $vParameters);
    }


    /**
     * do the cURL-call
     *
     * @param string $szEndpoint - MailChimp sub-endpoint
     * @param array  $vGetParams - a array of query-string-parameters as key->val pairs
     * @param array  $vParameters - array of new parameters, which update the entity
     * @return string - json-string, "response-body"
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

        //curl_setopt($this->oCurl, CURLOPT_URL, $this->szMcUrl . $szEndpoint); // complete response
        curl_setopt($this->oCurl, CURLOPT_URL, $this->szMcUrl . $szEndpoint . $szGetParams); // reduced response
        curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, $vParameters);

        // execute the cURL-call
        return curl_exec($this->oCurl);
    }
}
