<?php
// Copyright (C) 2017 Markus Jylhänkangas

namespace Longman\TelegramBot\Commands\UserCommands;
use Longman\TelegramBot\Commands\UserCommand;
//use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

require '../getLukkari.php';

class LukkariCommand extends UserCommand
{
    protected $name = 'lukkari';
    protected $description = 'Tämänpäivän lukkari';
    protected $usage = '/lukkari <luokka>';
    protected $version = '0.1';
    protected $public = true;

    public function execute()
    {
        $message = $this->getMessage();
        $message_id = $message->getMessageId();
        $chat_id = $message->getChat()->getId();
        $text = $message->getText(true);

        if(empty($text)) {
            $text = 'Laita luokka: /lukkari <luokka>';
        } else {
            // Tähän lukkari
            //$text = 'LUKKARI!!';
            date_default_timezone_set('Europe/Helsinki');

            $week = date('W');

            $year = date('Y');

		$luokka = "TTV15S3";

		$cacheFile = "../cache/" . $luokka . "-" . $week .'-'.$year;
		if(file_exists($cacheFile)) {
  		  $text = file_get_contents($cacheFile);
		} else {
 		   Get($luokka, $week, $year);
  		  $text = file_get_contents($cacheFile);
		}
        }

        $data = [
            'chat_id' => $chat_id,
            // 'disable_notification' => false,
            'reply_to_message_id' => $message_id,
            'text'    => $text,
        ];
        return Request::sendMessage($data);
    }
}
