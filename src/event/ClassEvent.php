<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-04-27
 * Time: 9:30
 */

namespace inhere\library\event;

/**
 * Class ClassEvent
 *  the Class Level Event
 *
 * @reference yii2 Event
 *
 * @package inhere\library\event
 */
class ClassEvent
{
    /**
     * registered Events
     * @var array
     * [
     *  'event' => bool, // is once event
     * ]
     */
    private static $events = [];

    /**
     * register a event handler
     * @param string|object $class
     * @param $event
     * @param callable $handler
     */
    public static function on($class, $event, callable $handler)
    {
        $class = ltrim($class, '\\');

        if (self::isSupportedEvent($event)) {
            self::$events[$event][$class] = $handler;
        }
    }

    /**
     * trigger event
     * @param $event
     * @param array $args
     * @return bool
     */
    public static function fire($event, array $args = [])
    {
        if (!isset(self::$events[$event])) {
            return false;
        }

        // call event handlers of the event.
        foreach ((array)self::$events[$event] as $cb) {
            // return FALSE to stop go on handle.
            if (false === call_user_func_array($cb, $args)) {
                break;
            }
        }

        // is a once event, remove it
        if (self::$events[$event]) {
            return self::removeEvent($event);
        }

        return true;
    }

    /**
     * remove event and it's handlers
     * @param $event
     * @return bool
     */
    public static function off($event)
    {
        return self::removeEvent($event);
    }

    public static function removeEvent($event)
    {
        if (self::hasEvent($event)) {
            unset(self::$events[$event]);

            return true;
        }

        return false;
    }

    /**
     * @param $event
     * @return bool
     */
    public static function hasEvent($event)
    {
        return isset(self::$events[$event]);
    }

    /**
     * @param $event
     * @return bool
     */
    public static function isOnce($event)
    {
        if (self::hasEvent($event)) {
            return self::$events[$event];
        }

        return false;
    }

    /**
     * check $name is a supported event name
     * @param $event
     * @return bool
     */
    public static function isSupportedEvent($event)
    {
        if (!$event || !preg_match('/[a-zA-z][\w-]+/', $event)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public static function getEvents()
    {
        return self::$events;
    }

    /**
     * @return int
     */
    public static function countEvents()
    {
        return count(self::$events);
    }
}