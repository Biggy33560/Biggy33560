<?php
namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail{

    private $api_key="0f0e09e9cdeb6b201f803225c918e800";
    private $api_key_secret="054c5b8943ee2739c02c2e9263ac4f4c";

    public function send($to_email, $to_name, $subject,$content){

// $mj = new \Mailjet\Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
    $mj = new Client($this->api_key,$this->api_key_secret,true,['version' => 'v3.1']);



   
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "monidweb@gmail.com",
                    'Name' => "la boutique"
                ],
                'To' => [
                    [
                        'Email' => $to_email,
                        'Name' =>$to_name,
                    ]
                ],
                'TemplateID' => 3853223,
                'TemplateLanguage' => true,
                'Subject' => $subject,
                'Variables'=> [
                    'content'=> $content,
                   
                ]
            ]
        ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();
   
}
}