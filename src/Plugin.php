<?php
/**
 * Phergie plugin for adding phergie events to database (https://github.com/reinierbee/phergie-irc-plugin-db)
 *
 * @link https://github.com/reinierbee/phergie-irc-plugin-db for the canonical source repository
 * @copyright Copyright (c) 2014 Reinier Boon (http://www.reinierboon.com)
 * @license http://phergie.org/license New BSD License
 * @package Phergie\Irc\Plugin\React\Db
 */

namespace Phergie\Irc\Plugin\React\Db;

use Phergie\Irc\Bot\React\AbstractPlugin;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Event\EventInterface as Event;
use Doctrine\Common\ClassLoader;
use Doctrine\DBAL;

/**
 * Plugin class.
 *
 * @category Phergie
 * @package Phergie\Irc\Plugin\React\Db
 */
class Plugin extends AbstractPlugin
{
    protected $db;

    /**
     * Accepts plugin configuration.
     *
     * Supported keys:
     *
     *
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if(!empty($config['database'])) {
            $connectionParams = $config['database'];
            $config['database'] = new DBAL\Configuration();
            $this->db = DBAL\DriverManager::getConnection($connectionParams, $config['database']);

        } else {
            $config['database'] = new DBAL\Configuration();
            $connectionParams = array(
                'dbname' => 'phergie-db',
                'user' => 'root',
                'password' => '',
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
            );
            $this->db = DBAL\DriverManager::getConnection($connectionParams, $config['database']);
        }

    }

    /**
     *
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'irc.' => 'handleEvent',
        );
    }

    /**
     *
     *
     * @param \Phergie\Irc\Event\EventInterface $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function handleEvent(Event $event, Queue $queue)
    {
        //$classLoader = new ClassLoader('Doctrine', '/path/to/doctrine');
        //$classLoader->register();

        //var_dump($event);
        //var_dump($queue);


        // Verify this event contains a command and remove the substring
        // identifying it as one
        $eventParams = $event->getParams();
        $target = $event->getCommand() === 'PRIVMSG'
            ? $eventParams['receivers']
            : $eventParams['nickname'];
        $message = $eventParams['text'];

        var_dump($eventParams);

        $sql = "INSERT INTO event_PRIVMSG (message, target)
                VALUES ('$message', '$target')";
        $this->db->query($sql);
    }
}
