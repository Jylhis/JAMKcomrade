<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Tests\Unit;

use Longman\TelegramBot\Telegram;

/**
 * @package         TelegramTest
 * @author          Avtandil Kikabidze <akalongman@gmail.com>
 * @copyright       Avtandil Kikabidze <akalongman@gmail.com>
 * @license         http://opensource.org/licenses/mit-license.php  The MIT License (MIT)
 * @link            http://www.github.com/akalongman/php-telegram-bot
 */
class TelegramTest extends TestCase
{
    /**
    * @var \Longman\TelegramBot\Telegram
    */
    private $telegram;

    /**
     * @var array A few dummy custom commands paths
     */
    private $custom_commands_paths = [
        '/tmp/php-telegram-bot-custom-commands-1',
        '/tmp/php-telegram-bot-custom-commands-2',
        '/tmp/php-telegram-bot-custom-commands-3',
    ];

    /**
    * setUp
    */
    protected function setUp()
    {
        $this->telegram = new Telegram('apikey', 'testbot');

        // Create a few dummy custom commands paths.
        foreach ($this->custom_commands_paths as $custom_path) {
            mkdir($custom_path);
        }
    }

    /**
     * tearDown
     */
    protected function tearDown()
    {
        // Clean up the custom commands paths.
        foreach ($this->custom_commands_paths as $custom_path) {
            rmdir($custom_path);
        }
    }

    /**
     * @expectedException \Longman\TelegramBot\Exception\TelegramException
     */
    public function testNewInstanceWithoutApiKeyParam()
    {
        new Telegram(null, 'testbot');
    }

    /**
     * @expectedException \Longman\TelegramBot\Exception\TelegramException
     */
    public function testNewInstanceWithoutBotNameParam()
    {
        new Telegram('apikey', null);
    }

    public function testGetApiKey()
    {
        $this->assertEquals('apikey', $this->telegram->getApiKey());
    }

    public function testGetBotName()
    {
        $this->assertEquals('testbot', $this->telegram->getBotName());
    }

    public function testEnableAdmins()
    {
        $tg = $this->telegram;

        $this->assertEmpty($tg->getAdminList());

        // Single
        $tg->enableAdmin(1);
        $this->assertCount(1, $tg->getAdminList());

        // Multiple
        $tg->enableAdmins([2, 3]);
        $this->assertCount(3, $tg->getAdminList());

        // Already added
        $tg->enableAdmin(2);
        $this->assertCount(3, $tg->getAdminList());

        // Integer as a string
        $tg->enableAdmin('4');
        $this->assertCount(3, $tg->getAdminList());

        // Random string
        $tg->enableAdmin('a string?');
        $this->assertCount(3, $tg->getAdminList());
    }

    public function testAddCustomCommandsPaths()
    {
        $tg = $this->telegram;

        $this->assertCount(1, $tg->getCommandsPaths());

        $tg->addCommandsPath($this->custom_commands_paths[0]);
        $this->assertCount(2, $tg->getCommandsPaths());
        $this->assertArraySubset(
            [$this->custom_commands_paths[0]],
            $tg->getCommandsPaths()
        );

        $tg->addCommandsPath('/invalid/path');
        $this->assertCount(2, $tg->getCommandsPaths());

        $tg->addCommandsPaths([
            $this->custom_commands_paths[1],
            $this->custom_commands_paths[2],
        ]);
        $this->assertCount(4, $tg->getCommandsPaths());
        $this->assertArraySubset(
            array_reverse($this->custom_commands_paths),
            $tg->getCommandsPaths()
        );

        $tg->addCommandsPath($this->custom_commands_paths[0]);
        $this->assertCount(4, $tg->getCommandsPaths());
    }

    public function testGetCommandsList()
    {
        $commands = $this->telegram->getCommandsList();
        $this->assertInternalType('array', $commands);
        $this->assertNotCount(0, $commands);
    }

    public function testGetHelpCommandObject()
    {
        $command = $this->telegram->getCommandObject('help');
        $this->assertInstanceOf('Longman\TelegramBot\Commands\UserCommands\HelpCommand', $command);
    }
}
