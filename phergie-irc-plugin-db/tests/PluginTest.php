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
}
