<?php
/* Base modular API Wrapper */
class ApiRequest{

    /**
     * Sends a GET request to a specified API URL and returns the response in JSON format,
     *
     * @param string apiUrl The URL of the API endpoint
     *
     * @return json the response from the API in JSON format, decoded as an associative array.
     */
    public function get(string $apiUrl) {

        if($apiUrl == ''){
            exit('Nieobsługiwany adres url API');
        }

        $context = stream_context_create([
            "http" => [
                "method"        => "GET",
                "header"        => "Accept: application/json",
            ],
        ]);

        $response = $this->file_contents($apiUrl, $context);
        return json_decode($response, true);
    }

    public function file_contents($path, $context) {
        $str = @file_get_contents($path, false, $context);
        if ($str === FALSE) {
            preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $match);
            $status = $match[1];
            exit("Nieprawidłowe zapytanie do API. Kod błedu: {$status}");
        } else {
            return $str;
        }
    }
}

