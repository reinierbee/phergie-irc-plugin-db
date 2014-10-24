<?php
/**
 * Phergie plugin for adding phergie events to database (https://github.com/reinierbee/phergie-irc-plugin-db)
 *
 * @link https://github.com/reinierbee/phergie-irc-plugin-db for the canonical source repository
 * @copyright Copyright (c) 2014 Reinier Boon (http://www.reinierboon.com)
 * @license http://phergie.org/license New BSD License
 * @package Phergie\Irc\Plugin\React\Db
 */

namespace Phergie\Irc\Tests\Plugin\React\Db;

use Phake;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Event\EventInterface as Event;
use Phergie\Irc\Plugin\React\Db\Plugin;

/**
 * Tests for the Plugin class.
 *
 * @category Phergie
 * @package Phergie\Irc\Plugin\React\Db
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests that getSubscribedEvents() returns an array.
     */
    public function testGetSubscribedEvents()
    {
        $plugin = new Plugin;
        $this->assertInternalType('array', $plugin->getSubscribedEvents());
    }

    /**
     * Data provider for testParseCommandEmitsEvent().
     *
     * @return array
     */
    public function dataProviderParseCommandEmitsEvent()
    {
        $data = array();

        $commands = array(
            'PRIVMSG' => 'receivers',
            'NOTICE' => 'nickname',
        );

        $message = '"test message"';

        $databaseConfig =array(
            'dbname' => 'phergie-db',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );
        $nickConfig = array('nick' => true);

        $configs = array(
            'foo' => array(),
            'nickname ' . $message => $nickConfig,
            'nickname: ' . $message => $nickConfig,
            ' nickname, ' . $message => $nickConfig,
        );

        $expectedParams = array('test message');

        $targets = array('#channel', 'user');

        $getEvent = function($command, $targetField, $text, $config, $target) {
            $event = Phake::mock('\Phergie\Irc\Event\UserEventInterface');
            $connection = Phake::mock('\Phergie\Irc\ConnectionInterface');
            $params = array('text' => $text, $targetField => $target);
            Phake::when($connection)->getNickname()->thenReturn('nickname');
            Phake::when($event)->getConnection()->thenReturn($connection);
            Phake::when($event)->getCommand()->thenReturn($command);
            Phake::when($event)->getParams()->thenReturn($params);
            Phake::when($event)->getTargets()->thenReturn(array());
            return $event;
        };

        foreach ($commands as $command => $targetField) {
            foreach ($configs as $text => $config) {
                foreach ($targets as $target) {
                    $event = $getEvent($command, $targetField, $text, $config, $target);
                    $data[] = array($config, $event, $text == 'foo' ? array() : $expectedParams);
                }
            }
        }

        // Events sent directly to the bot should always be interpreted as
        // potential commands
        $data[] = array(
            $databaseConfig,
            $getEvent('PRIVMSG', $commands['PRIVMSG'], $message, $databaseConfig, 'user'),
            $expectedParams
        );

        return $data;
    }

    /**
     * Tests parseCommand() under conditions when it is expected to emit an
     * event.
     *
     * @param array $config Plugin configuration
     * @param \Phergie\Irc\Event\UserEventInterface $event
     * @param array $expectedParams Expected command event parameter values
     * @dataProvider dataProviderParseCommandEmitsEvent
     */
    public function testParseCommandEmitsEvent(array $config, Event $event, array $expectedParams)
    {
        $queue = Phake::mock('Phergie\Irc\Bot\React\EventQueueInterface');
        $eventEmitter = Phake::mock('\Evenement\EventEmitterInterface');

        $plugin = new Plugin($config);
        $plugin->setEventEmitter($eventEmitter);
        $plugin->handleEvent($event, $queue);

        Phake::verify($eventEmitter)->emit('command.foo', Phake::capture($commandEventParams));
        $commandEvent = $commandEventParams[0];
        $this->assertInstanceOf('\Phergie\Irc\Plugin\React\Command\CommandEvent', $commandEvent);
        $this->assertSame('foo', $commandEvent->getCustomCommand());
        $this->assertSame($expectedParams, $commandEvent->getCustomParams());
        $this->assertSame($queue, $commandEventParams[1]);
    }
}
