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

namespace JAMKcomrade;
// Load composer
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php';

$COMMANDS_FOLDER = __DIR__.'/Commands/';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Error, Debug and Raw Update logging TODO
    // Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);
    //Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . '/' . 'error.log');
    //Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . '/' . 'debug.log');
    //Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . '/' . 'update.log');

    $telegram->addCommandsPath($COMMANDS_FOLDER);

    $telegram->enableLimiter();

    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    //echo $e;
    // Log telegram errors
    //   Longman\TelegramBot\TelegramLog::error($e);
}
