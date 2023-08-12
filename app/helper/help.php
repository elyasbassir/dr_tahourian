<?php

namespace App\helper;

use SoapClient;
use Illuminate\Support\Facades\Http;

class help
{

    static public function send_sms($text, $numbers)
    {
        $soapClientObj = new SoapClient("https://p.1000sms.ir/Post/Send.asmx?wsdl");
        $parameters['username'] = "hossein_th";
        $parameters['password'] = "369963";
        $parameters['from'] = "100015000";
        $parameters['to'] = $numbers;
        $parameters['text'] = $text;
        $parameters['isflash'] = false;
        $parameters['udh'] = "";
        $parameters['recId'] = array(0);
        $parameters['status'] = array(0);
        $soapClientObj->SendSms($parameters);
        return true;
    }

    static public function get_token_api()
    {
        return json_decode(Http::get("http://45.139.10.90:8080/shauth/login/1/8002")->body())->data->access_Token;
    }

    static public function add_user_in_crm(array $data)
    {
        $data = Http::withHeaders([
            'Authorization' => "Bearer " . self::get_token_api(),
        ])->post("http://45.139.10.90:8080/Customers/InsertCustomer", json_decode('{
  "firstname": "' . $data['name'] . '",
  "lastname": "' . $data['last_name'] . '",
  "companyname": "' . $data['company'] . '",
  "mobile": "' . $data['phone'] . '",
  "phone": "' . $data['stable_phone'] . '",
  "gender": "' . $data['gender'] . '",
  "job": "' . $data['job'] . '",
  "mail": "' . $data['email'] . '",
  "address": "' . $data['address'] . '",
  "birthdate": "' . $data['birthdate'] . '",
  "marriagedate": "' . $data['marriagedate'] . '",
  "partnerdate": "' . $data['partnerdate'] . '",
  "ncode": "' . $data['ncode'] . '",
  "explain": "' . $data['explain'] . '",
  "mobileintroducer": "' . $data['mobileintroducer'] . '",
  "idcustomer": "-1"
}'));
        return $data->body();
    }

    static public function update_user(array $data)
    {
        $data = Http::withHeaders([
            'Authorization' => "Bearer " . self::get_token_api(),
        ])->post("http://45.139.10.90:8080/Customers/InsertCustomer", json_decode('{
  "firstname": "' . $data['name'] . '",
  "lastname": "' . $data['last_name'] . '",
  "companyname": "' . $data['company'] . '",
  "mobile": "' . $data['phone'] . '",
  "phone": "' . $data['stable_phone'] . '",
  "gender": "' . $data['gender'] . '",
  "job": "' . $data['job'] . '",
  "mail": "' . $data['email'] . '",
  "address": "' . $data['address'] . '",
  "birthdate": "' . $data['birthdate'] . '",
  "marriagedate": "' . $data['marriagedate'] . '",
  "partnerdate": "' . $data['partnerdate'] . '",
  "ncode": "' . $data['ncode'] . '",
  "explain": "' . $data['explain'] . '",
  "mobileintroducer": "' . $data['mobileintroducer'] . '",
  "idcustomer": "-1"
}'));
        return $data->body();
    }

}
