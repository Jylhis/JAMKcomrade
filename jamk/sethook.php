<?php
// Copyright (C) 2017 Markus Jylhänkangas

// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '320400820:AAFQ-5bm4qRnBBSdxIe9oQhnshGWmS7gAp4';
$BOT_NAME = 'JamkBot';
$hook_url = 'https://www.jylhis.com/jamk/webhook.php';

// Set
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Set webhook
     $result = $telegram->setWebhook($hook_url);
     //$result = $telegram->deleteWebhook();
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
