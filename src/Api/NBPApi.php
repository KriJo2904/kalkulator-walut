<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/src/Services/ApiRequest.php'));
/* NBP Api wrapper */
class NBPApi extends ApiRequest{
    
    public $apiUrl;

    public function __construct()
    {
        $configs = include(realpath($_SERVER['DOCUMENT_ROOT'] ."/config/connection.php"));
        $this->apiUrl = $configs['api_info']['apiURL'];
    }

    /**
     * Retrieves exchange rates from the NBP API and returns them as an array, or throws
     * an exception if the data is missing.
     *
     * @return array|Exception returns an array of exchange rates fetched from the NBP
     * (National Bank of Poland) API. If the API call is successful and the returned data contains
     * exchange rates, the function returns an array of rates.
     */
    public function getRates(){
        $result = $this->get($this->apiUrl );
        if($result && !empty($result[0]['rates'])){
            return $result[0]['rates'];
        }
        else{
            throw new Exception('Brak wymaganych danych dla prawidłowego działania aplikcaji');
        }
    }
}
