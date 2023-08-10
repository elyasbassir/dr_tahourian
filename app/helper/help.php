<?php

namespace App\helper;

use SoapClient;

class help
{

    static public function send_sms($text,$numbers)
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


}
