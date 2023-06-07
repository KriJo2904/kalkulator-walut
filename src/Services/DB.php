<?php
/* Init and operate database connection in PHP. */
class DB{

    /**
     * Databse connection object
     * @var object
     */
    private $connection;

    public function __construct()
    {
        mysqli_report(MYSQLI_REPORT_STRICT);

        $configs = include(dirname(__FILE__)."./../../config/connection.php");
        try{
		    $this->connection = new mysqli( $configs['host'], $configs['username'], $configs['pass'], $configs['database']);
        } catch (Exception $e ) {
            exit('Aplikacja tymczasowo niedstępna. Spróbuj ponownie później.');
        }
    }
    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * The function closes the connection to the database.
     * 
     * @return bool Status of conenction closing
     */
    public function close() {
		return $this->connection->close();
	}

    /**
     * The function exits the program and displays an error message.
     *
     * @param string error Uused to pass an error message
     * or code to the function, which will then exit the program and display the error message.
     */
    public function error($error) {
        exit($error);
    }

    /**
     * Retrieves currency exchange rates from a database based on a given array of
     * currency codes.
     *
     * @param array values An array of currency codes.
     *
     * @return array array with the currency code as the key and an array of currency code,
     * currency name, and value as the value.
     */
    public function getCalculateValues(array $values){
        $values = "'" . implode("', '",    $values) . "'";
        $query = "SELECT code, currency, value FROM rates WHERE code IN (".  $values. ") GROUP BY code";
        try{
            $dbData = mysqli_fetch_all($this->connection->query($query) , MYSQLI_ASSOC);
            return  array_combine(array_column($dbData, 'code'), $dbData);
        } catch(Exception $e) {
            exit('Wystąpił błąd podczas uruchamiania kalkulatora. Spróbuj ponownie za chwilę, lub skontaktuj się z administratorem.');
        }

    }

    /**
     * Stores currency exchange rates in a database table, updating the value if the
     * currency and code already exist.
     *
     * @param array rates an array of rates, where each rate is an associative array with keys
     * 'currency', 'code', and 'mid'.
     */
    public function storeRates(array $rates){
        $sql = $this->connection->prepare("INSERT INTO rates (currency, code, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value = ?;");
        try{
            $this->connection->begin_transaction();
            foreach($rates as $rate){
                $sql->execute([
                    $rate['currency'],
                    $rate['code'],
                    $rate['mid'],
                    $rate['mid'],
                ]);
            }
            $this->connection->commit();
        } catch(Exception $e ) {
            $this->connection->rollBack();
            exit('Wystąpił błąd podczas kalkulacji. Spróbuj ponownie za chwilę, lub skontaktuj się z administratorem.');
        }
    }

    public function storeRatesData(string $date, string $no){

    }

    /**
     * Retrieves all the rates from a database table and returns them as an associative
     * array.
     *
     * @return array containing the code, currency, and value of all the rates
     * in the "rates" table.
     */
    public function getRates(){
        $query = "SELECT code, currency, value FROM rates;";
        try{
            $result = $this->connection->query($query);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } catch(Exception $e) {
            exit('Wystąpił błąd podczas uruchamiania kalkulatora. Spróbuj ponownie za chwilę, lub skontaktuj się z administratorem.');
        }
    }

    /**
     * This function stores a new calculation in a database table.
     * 
     * @param string fromCurrency The currency that the user is converting from. For example US
     * Dollars).
     * @param string fromCode The currency code of the currency being converted from. For example,
     * "USD" for US dollars.
     * @param string toCurrency The currency that the user wants to convert to.
     * @param string toCode The currency code of the currency being converted to.
     * @param float ammount The amount of currency being converted from the "fromCurrency".
     * @param float valueResult  is a float variable that represents the result of a
     * currency conversion calculation.
     */
    public function storeNewCalculation(string $fromCurrency, string $fromCode, string $toCurrency, string $toCode, float $ammount, float $valueResult){
        $sql = $this->connection->prepare("INSERT INTO latestCalculations (toCode, toCurrency, fromCode, fromCurrency, initValue, calculated, dateTime) VALUES ('$toCode','$toCurrency','$fromCode','$fromCurrency',$ammount,$valueResult, now())");
        try{
            $this->connection->begin_transaction();
            $sql->execute();
            $this->connection->commit();
        } catch(Exception $e ) {
            $this->connection->rollBack();
            exit('Wystąpił błąd podczas kalkulacji. Spróbuj ponownie za chwilę, lub skontaktuj się z administratorem.');
        }
    }

    /**
     * Retrieves the latest currency conversion calculations from a database.
     * 
     * @return array The associative array contains the following keys:
     * "toCode", "toCurrency", "fromCode", "fromCurrency", "initValue", "calculated", and "dateTime".
     */
    public function getLatestCalculations(){
        $query = "SELECT toCode, toCurrency, fromCode, fromCurrency, initValue, calculated, dateTime FROM latestCalculations ORDER BY dateTime DESC;";
        try{
            $result = $this->connection->query($query);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } catch(Exception $e) {
            exit('Wystąpił błąd podczas uruchamiania kalkulatora. Spróbuj ponownie za chwilę, lub skontaktuj się z administratorem.');
        }
    }

}
