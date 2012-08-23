<?php
/**
 * DealZippy api client library
 * 
 * API docs: http://www.dealzippy.co.uk/api/documentation/
 * 
 * @author r0b1n <roman.vyakhirev@gmail.com>
 */
class DealZippy {
    
    /**
     * Deal Zippy API key, to request key go to: http://www.dealzippy.co.uk/api/signup/
     * @var string 
     */
    public $apiKey = null;
    
    /**
     * Url of the Deal Zippy API access point
     * 
     * @var string
     */
    public $apiUrl;
    
    public $useragent = "DealZippy.php v0.1";
    public $connecttimeout = 30; 
    public $timeout = 30;

    /**
     * Construct a DealZippy object
     * 
     * @param string $apiKey your api key
     * @param boolean $useDevApi for dev-api use true
     */
    public function __construct($apiKey, $useDevApi = false) {
        $this->apiKey = $apiKey;
        
        $this->apiUrl = $useDevApi ? 'http://www.dealzippy.co.uk/dev-api/' : 'http://www.dealzippy.co.uk/api/';
    }
    
    /**
     * Make an HTTP request
     *
     * @return API results
     */
    public function http($url, $method, $postfields = NULL) {
        $this->http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
        
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        
        curl_setopt($ci, CURLOPT_FOLLOWLOCATION, TRUE);

        switch ($method) {
        case 'POST':
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) {
            curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
            }
            break;
        case 'DELETE':
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if (!empty($postfields)) {
            $url = "{$url}?{$postfields}";
            }
        }

        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        curl_close ($ci);
        return $response;
    }
    
    /**
     * Get the header info to store.
     */
    function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
        $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
        $value = trim(substr($header, $i + 2));
        $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
    
    
    /**
     * Make a get request
     * 
     * @param string $url - api point url
     * @param array $params additional params for request
     * @return object decoded data 
     */
    public function get($url, $params = array()) {
        $url = "{$this->apiUrl}{$url}?" . http_build_query($params);
        $response = $this->http($url, "GET");
        return json_decode($response);
    }
    
    /**
     * Request an active deals
     * @return type 
     */
    public function getDeals() {
        return $this->get('deals', array('key' => $this->apiKey));
    }
}

?>
