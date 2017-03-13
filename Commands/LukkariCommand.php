<?php
/* The MIT License (MIT)

   Copyright (c) 2016 Markus Jylhänkangas

   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software and associated documentation files (the "Software"), to deal
   in the Software without restriction, including without limitation the rights
   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
   copies of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:

   The above copyright notice and this permission notice shall be included in all
   copies or substantial portions of the Software.

   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
   SOFTWARE.
 */


namespace Longman\TelegramBot\Commands\UserCommands;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

require __DIR__ . '/../getLukkari.php';

class LukkariCommand extends UserCommand
{
    protected $name = 'lukkari';
    protected $description = 'Tämänpäivän lukkari';
    protected $usage = '/lukkari <luokka>';
    protected $version = '0.1';

    public function execute()
    {
        $message = $this->getMessage();
        $message_id = $message->getMessageId();
        $chat_id = $message->getChat()->getId();
        $text = $message->getText(true);

        date_default_timezone_set('Europe/Helsinki');
        $week = date('W');
        $year = date('Y');
        $luokka = "TTV15S3";

        if(!empty($text)) {
            $luokka = strtoupper($text);
        }

        $cacheFile = __DIR__ . "/../cache/" . $luokka . "-" . $week .'-'.$year;
        //$cacheFile = __DIR__ . "/../cache/asdf";

        if(file_exists($cacheFile)==1) {
            Get($luokka, $week, $year);
        }
        $text = file_get_contents($cacheFile);

        // Proccess data
        $text = str_replace("<hr>", "", $text);
        $text = str_replace("<h2>", "<b>", $text);
        $text = str_replace("</h2>", "</b>", $text);
        $text = str_replace("<br>", "\n", $text);

        $data = [
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $message_id,
            'text'    => $text,
        ];
        return Request::sendMessage($data);
    }
}
