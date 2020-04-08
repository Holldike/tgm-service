<?php

namespace MtProtoDriver;

use Api;
use \danog\MadelineProto\API as MadelineApi;
use danog\MadelineProto\RPCErrorException;
use Db;

class MadelineProto extends Driver implements Api
{
    private MadelineApi $api;

    public function __construct()
    {
        $this->api = new MadelineApi($this->sessionDir . '/session.madeline', [
            'app_info' => [
                'api_id' => '1308904',
                'api_hash' => 'a90141f7a3905c78f77443bd789af387'
            ]
        ]);
    }

    public function sendMessage(string $phone)
    {
        $responseData = [];

        if (!$contact = $this->importContact($phone)) {
            $responseData['status'] = 'error';
            $responseData['error_description'] = 'phone not found in telegram';
        }

        if ($contact['id'] != $selfId = $this->api->getSelf()['id']) {
            $responseData['status'] = 'error';
            $responseData['error_description'] = "you can't send message to yourself";
        }

        try {
            $message = $this->api->messages->sendMessage(['peer' => $contact['id'], 'message' => $text]);
        } catch (RPCErrorException $e) {
            $responseData['status'] = 'error';
            $responseData['error_description'] = $e->getMessage();
        }

        if (isset ($responseData['status']) && $responseData['status'] != 'error') {
            //Added a message who has been sent to local message storage
            Db::getConnect()->query("
                        INSERT INTO sent_message SET
                        tgm_contact_id = '" . (int)$selfId . "',
                        tgm_to_contact_id = '" . (int)$contact['id'] . "',
                        tgm_message_id = '" . (int)$message['id'] . "',
                        sent_at = '" . date("Y-m-d H:m:s", $message['date']) . "'
                    ");

            $responseData['id'] = $message['id'];
            $responseData['status'] = 'success';
        } else {
            $responseData = 'Sorry, but something went wrong';
        }

        return $responseData;
    }

    public function getMessage(int $message_id)
    {
        $responseData = [];

        //Fetch a message from local sent message storage
        $localMessageData = \Db::getConnect()->query("
                    SELECT * FROM sent_message WHERE 
                    tgm_message_id = '" . (int)$message_id . "' AND
                    tgm_user_id = '" . (int)$this->api->getSelf()['id'] . "' 
                ")->fetch_assoc();

        if (!$localMessageData) {
            $responseData['status'] = 'error';
            $responseData['error_description'] = 'message not found by this id';
        }

        if (isset ($responseData['status']) && $responseData['status'] != 'error') {
            $contact = $this->getContactById($localMessageData['tgm_to_user_id']);

            $responseData['to_phone'] = $contact['phone'];
            $responseData['send_at'] = $localMessageData['sent_at'];
            //If contact was online after the message had sent or online now, it mean what it read the message
            $responseData['read'] = (
                $contact['status']['_'] === 'userStatusOnline' ||
                $contact['status']['was_online'] < strtotime($localMessageData['sent_at'])
            );

            $responseData['status'] = 'success';
        } else {
            $responseData = 'Sorry, but something went wrong';
        }

        return $responseData;
    }

    private function getContactById(int $id)
    {
        $contacts = $this->api->contacts->getContacts()['users'];
        foreach ($contacts as $contact) {
            if ($contact['id'] === $id) {
                return $contact;
            }
        }
    }

     //Add contact to client
    private function importContact(string $phone)
    {
        $importContact = ['_' => 'inputPhoneContact', 'client_id' => 0, 'phone' => "+$phone", 'first_name' => '', 'last_name' => ''];
        $import = $this->api->contacts->importContacts(['contacts' => [$importContact]]);

        if ($contact = $import['users'][0]) {
            return $contact;
        } else {
            return null;
        }
    }

    public function login()
    {
        $this->api->start();
    }
}