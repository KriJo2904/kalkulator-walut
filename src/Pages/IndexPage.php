<?php
require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/src/Api/NBPApi.php'));
require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/src/Services/DB.php'));
/* Functional class for index page. */
class IndexPage{
    /**
     * @var object
     */
    private $db;

        /**
     * @var object
     */
    private $api;

    public function __construct()
    {
        $this->db = new DB;
        $this->api = new NBPApi;
    }

    /**
     * The function refreshes data by getting rates from an API, adding PLN currency, and storing the
     * updated rates in a database.
     */
    public function refreshData(){
        $newRates = $this->api->getRates();
        array_push($newRates,['currency' => 'polski złoty', 'code' => 'PLN', 'mid' => 1]);
        $this->db->storeRates($newRates);
    }

    /**
     * Calculates the conversion rate between two currencies and stores the result in a
     * database.
     *
     * @param string fromCode The code of the currency to convert from (e.g. USD, EUR, GBP).
     * @param string toCode The currency code to which the input amount needs to be converted.
     * @param float ammount The amount of money to be converted from one currency to another.
     *
     * @return array array with the calculated amount, result, and exchange rates for the specified
     * currencies. If the currencies are not supported, the function exits with an error message.
     */
    public function calculate(string $fromCode, string $toCode, float $ammount){
        $codes = [$fromCode, $toCode];
        $data = $this->db->getCalculateValues($codes);
        if(count($data) > 0 && in_array($fromCode, array_keys($data))){
            $result = round(($data[$fromCode]['value']/$data[$toCode]['value'])*$ammount, 2);
            $this->db->storeNewCalculation(
                $data[$fromCode]['currency'],
                $data[$fromCode]['code'],
                $data[$toCode]['currency'],
                $data[$toCode]['code'],
                floatval($ammount),
                floatval($result),
            );
            return ['ammount' => $ammount, 'result' => $result, 'from' => $data[$fromCode]['value'], 'to' => $data[$toCode]['value']];
        }
        else{
            exit('Wybrane waluty nie są obsługiwane');
        }
    }

    /**
     * Returns the latest rates from a database.
     *
     * @return array returning the result of calling the `getRates()` method
     * on the database object.
     */
    public function latestRates(){
        return $this->db->getRates();
    }

    /**
     * Returns the latest calculations from a database.
     * 
     * @return array the result of a method call to `getLatestCalculations()`.
     */
    public function latestCalculations(){
        return $this->db->getLatestCalculations();
    }
}
