#!/usr/bin/env php
<?php


// Load composer
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php';
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
