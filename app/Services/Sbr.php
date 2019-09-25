<?php

namespace App\Services;

use GuzzleHttp\Client;
use Carbon\Carbon;

class Sbr
{
    protected $url_currency_list = 'http://www.cbr.ru/scripts/XML_valFull.asp';

    protected $url_currency_rate = 'http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=%s&date_req2=%s&VAL_NM_RQ=%s';

    protected $data = [];

    private function parse_list($content) {
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), TRUE);
        if (!isset($data['Item']) || !is_array($data['Item'])) {
            return false;
        }
        foreach ($data['Item'] as $value) {
            $currency = array();
            $currency['code'] = isset($value['@attributes']['ID']) ? $value['@attributes']['ID'] : false;
            $currency['name'] = isset($value['Name']) ? $value['Name'] : false;
            $currency['nominal'] = isset($value['Nominal']) ? $value['Nominal'] : false;
            $currency['iso_num_code'] = isset($value['ISO_Num_Code']) ? $value['ISO_Num_Code'] : false;
            $currency['iso_char_code'] = isset($value['ISO_Char_Code']) ? $value['ISO_Char_Code'] : false;
            $this->data[] = $currency;
        }
        return true;
    }

    private function parse_item($content) {
        $xml = simplexml_load_string($content);
        $data = json_decode(json_encode($xml), TRUE);
        if (!isset($data['Record']) || !is_array($data['Record'])) {
            return false;
        }
        return isset($data['Record']['Value']) ? $data['Record']['Value'] : false;
    }


    public function get_currency_list() {
        $client = new Client();
        $options = ['allow_redirects' => ['max' => 5]];
        $response = $client->request('GET', $this->url_currency_list, $options);
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $content = $response->getBody()->getContents();
        return ($this->parse_list($content)) ? $this->data : false;
    }

    public function get_currency_rate($code) {
        $client = new Client();
        $date =  Carbon::now()->format('d/m/Y');
        $url = sprintf($this->url_currency_rate, $date, $date, $code);
        $options = ['allow_redirects' => ['max' => 5]];
        $response = $client->request('GET', $url, $options);
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $content = $response->getBody()->getContents();
        return $this->parse_item($content);
    }

}

