<?php
// copyright (C) 2017 Markus Jylhänkangas

// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '320400820:AAFQ-5bm4qRnBBSdxIe9oQhnshGWmS7gAp4';
$BOT_NAME = 'JamkBot';
$COMMANDS_FOLDER = __DIR__.'/Commands/';

try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

        // Error, Debug and Raw Update logging
        Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);
        Longman\TelegramBot\TelegramLog::initErrorLog($path . '/' . $BOT_NAME . '_error.log');
        Longman\TelegramBot\TelegramLog::initDebugLog($path . '/' . $BOT_NAME . '_debug.log');
        Longman\TelegramBot\TelegramLog::initUpdateLog($path . '/' . $BOT_NAME . '_update.log');

        //TODO: Enable admin user(s)
        //$telegram->enableAdmin(your_telegram_id);

        $telegram->addCommandsPath($COMMANDS_FOLDER);

        //$telegram->setCommandConfig('lukkari', ['ryhma' => 'TTV15S3']);

        // Handle telegram webhook request
        $telegram->handle();


    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // Silence is golden!
        // log telegram errors
        // echo $e;
}