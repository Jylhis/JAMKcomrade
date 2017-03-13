<?php
// Copyright (C) 2017 Markus Jylhänkangas

namespace Longman\TelegramBot\Commands\UserCommands;
use Longman\TelegramBot\Commands\UserCommand;
//use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class RuokaCommand extends UserCommand
{
    protected $name = 'ruoka';
    protected $description = 'Tämänpäivän ruoka aimossa';
    protected $usage = '/ruoka';
    protected $version = '0.1';
    protected $public = true;

    public function execute()
    {
        $message = $this->getMessage();
        $message_id = $message->getMessageId();
        $chat_id = $message->getChat()->getId();
        $text = $message->getText(true);


        $text = 'RUOKAA!!';


        $data = [
            'chat_id' => $chat_id,
            // 'disable_notification' => false,
            'reply_to_message_id' => $message_id,
            'text'    => $text,
        ];
        return Request::sendMessage($data);
    }
}