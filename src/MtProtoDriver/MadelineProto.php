<?php

namespace MtProtoDriver;

use Symfony\Component\HttpFoundation\Request;
use \danog\MadelineProto\API as MadelineApi;
use danog\MadelineProto\RPCErrorException;
use Db;

class MadelineProto extends Driver
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

    public function sendMessage(string $phone, Request $request)
    {
        if (!$contact = $this->importContact($phone)) {
            return [
                'status' => 'error',
                'error_description' => 'phone not found in telegram'
            ];
        }

        if ($contact['id'] === (int)$selfId = $this->api->getSelf()['id']) {
            return [
                'status' => 'error',
                'error_description' => 'you can\'t send message to yourself'
            ];
        }

        try {
            $message = $this->api->messages->sendMessage(
                [
                    'peer' => $contact['id'],
                    'message' => json_decode($request->getContent(), true)['text']
                ]
            );

            //Added a message who has been sent to local message storage
            Db::getConnect()->query("
                        INSERT INTO sent_message SET
                        tgm_contact_id = '" . (int)$selfId . "',
                        tgm_to_contact_id = '" . (int)$contact['id'] . "',
                        tgm_message_id = '" . (int)$message['id'] . "',
                        sent_at = '" . date("Y-m-d H:i:s", $message['date']) . "'
                    ");

            return [
                'id' => $message['id'],
                'status' => 'success'
            ];

        } catch (RPCErrorException $e) {
            return [
                'status' => 'error',
                'error_description' => $e->getMessage()
            ];
        }
    }

    public function getMessage(int $messageId, Request $request)
    {
        //Fetch a message from local sent message storage
        $localMessageData = Db::getConnect()->query("
                    SELECT * FROM sent_message WHERE 
                    tgm_message_id = '" . (int)$messageId . "' AND
                    tgm_contact_id = '" . (int)$this->api->getSelf()['id'] . "' 
                ")->fetch_assoc();

        if (!$localMessageData) {
            return [
                'status' => 'error',
                'error_description' => 'message not found by this id'
            ];
        }

        $contact = $this->getContactById($localMessageData['tgm_to_contact_id']);

        return [
            'to_phone' => $contact['phone'],
            'send_at' => $localMessageData['sent_at'],
            //If contact was online after the message had sent or online now, it mean what it read the message
            'read' => $contact['status']['_'] === 'userStatusOnline' || $contact['status']['was_online'] > strtotime($localMessageData['sent_at']),
            'status' => 'delivered'
        ];
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

        if ($import['users']) {
            return array_shift($import['users']);
        } else {
            return null;
        }
    }

    public function login()
    {
        $this->api->start();
    }
}