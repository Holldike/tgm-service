<?php

namespace MtProtoDriver;

use Api;
use \danog\MadelineProto\API as MadelineApi;

class MadelineProto implements Api
{
    private $api;

    public function __construct()
    {
        $this->api = new MadelineApi('session.madeline', [
            'app_info' => [
                'api_id' => '1308904',
                'api_hash' => 'a90141f7a3905c78f77443bd789af387'
            ]
        ]);
    }

    public function sendMessage(string $phone)
    {
        $text = $_POST['text'];

        $contact = ['_' => 'inputPhoneContact', 'client_id' => 0, 'phone' => "+$phone", 'first_name' => '', 'last_name' => ''];
        $import = $this->api->contacts->importContacts(['contacts' => [$contact]]);

        if (!empty($import['imported'])) {
            $user = $import['imported'][0];
            $this->api->messages->sendMessage(['peer' => $user['user_id'], 'message' => $text]);
        } else {
            //Number not found in telegram return NOT FOUND STATUS
        }
    }

    public function getMessage(int $messageId)
    {
        echo 'wer';
    }

    public function login()
    {
        $this->api->start();
    }
}