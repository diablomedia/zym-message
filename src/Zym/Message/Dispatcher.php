<?php

/**
 * Zym
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Message
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */


/**
 * @author     Jurrien Stutterheim
 * @category   Zym
 * @package    Zym_Message
 * @copyright  Copyright (c) 2008 Zym. (http://www.zym-project.com/)
 * @license    http://www.zym-project.com/license    New BSD License
 */
class Zym_Message_Dispatcher
{
    /**
     * The default callback method name
     */
    protected string $_defaultCallback = 'notify';

    /**
     * Collection of available events.
     */
    protected array $_events = [];

    /**
     * Singleton instance
     */
    protected static array $_instances = [];

    /**
     * The collection of objects that registered to messages
     */
    protected array $_observers = [];

    /**
     * Wildcard for the catch-all event
     */
    protected string $_wildcard = '*';

    /**
     * Singleton constructor
     *
     */
    protected function __construct()
    {
    }

    /**
     * Register an observer for the specified message
     *
     * @param string|array $events
     * @param string $callback
     * @return $this
     */
    public function attach(object $observer, $events = null, string $callback = null): self
    {
        if (!$events) {
            $events = [$this->_wildcard];
        }

        if (!$callback) {
            $callback = $this->_defaultCallback;
        }

        $events       = (array) $events;
        $observerHash = spl_object_hash($observer);

        foreach ($events as $event) {
            if (!$this->isRegistered($event)) {
                $this->reset($event);
            }

            $this->_events[$event] = $event;

            if (!$this->hasObserver($observerHash, $event)) {
                $registration = new Zym_Message_Registration($observer, $callback);

                $this->_observers[$event][$observerHash] = $registration;
            }
        }

        return $this;
    }

    /**
     * Remove an observer
     *
     * @param string|array $events
     * @return $this
     */
    public function detach(object $observer, $events = null): self
    {
        if (!$events) {
            $events = $this->_events;
        } else {
            $events = (array) $events;
        }

        $observerHash = spl_object_hash($observer);

        foreach ($events as $event) {
            if ($this->isRegistered($event) &&
                $this->hasObserver($observerHash, $event)) {
                unset($this->_observers[$event][$observerHash]);

                if (empty($this->_observers[$event])) {
                    unset($this->_events[$event]);
                }
            }
        }

        return $this;
    }

    /**
     * Get a message dispatcher instance from the internal registry
     *
     */
    public static function get(string $namespace = 'default'): Zym_Message_Dispatcher
    {
        if (!self::has($namespace)) {
            self::$_instances[$namespace] = new self();
        }

        return self::$_instances[$namespace];
    }

    /**
     * Get the wildcard
     */
    public function getWildcard(): string
    {
        return $this->_wildcard;
    }

    /**
     * Check if the namespace is already set
     */
    public static function has(string $namespace): bool
    {
        return isset(self::$_instances[$namespace]);
    }

    /**
     * Check if the observer is registered for the specified event
     *
     * @param object|string $observer either spl_object_hash or the object itself
     */
    public function hasObserver(&$observer, string $event): bool
    {
        if (!$this->isRegistered($event)) {
            return false;
        }

        if (is_object($observer)) {
            $observerHash = spl_object_hash($observer);
        } else {
            $observerHash = (string) $observer;
        }

        return isset($this->_observers[$event][$observerHash]);
    }

    /**
     * Check if an event is registered
     *
     */
    public function isRegistered(string $event): bool
    {
        return isset($this->_observers[$event]);
    }

    /**
     * Post a message
     *
     * @return $this
     * @throws Zym_Message_Exception
     */
    public function post(string $name, object $sender, array $data = []): self
    {
        $toNotify = [];

        foreach ($this->_events as $event) {
            if ($event == $name || $event == $this->_wildcard) {
                $toNotify[] = $event;
            } else {
                if (str_contains($event, $this->_wildcard)) {
                    $cleanEvent = str_replace($this->_wildcard, '', $event);

                    if (str_starts_with($name, $cleanEvent)) {
                        $toNotify[] = $event;
                    }
                }
            }
        }

        $message  = new Zym_Message($name, $sender, $data);
        $notified = [];

        foreach ($toNotify as $event) {
            if (isset($this->_observers[$event])) {
                foreach ($this->_observers[$event] as $observerHash => $registration) {
                    if (!isset($notified[$observerHash])) {
                        $notified[$observerHash] = $observerHash;

                        $observer = $registration->getObserver();
                        $callback = $registration->getCallback();

                        if ($observer instanceof Zym_Message_Interface &&
                            $callback == $this->_defaultCallback) {
                            $observer->notify($message);
                        } else {
                            if (!method_exists($observer, $callback)) {
                                $error = sprintf(
                                    'Method "%s" is not implemented in class "%s"',
                                    $callback,
                                    get_class($observer)
                                );

                                throw new Zym_Message_Exception($error);
                            }

                            $observer->$callback($message);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Remove a dispatcher instance from the internal registry
     *
     */
    public static function remove(string $namespace): void
    {
        if (self::has($namespace)) {
            unset(self::$_instances[$namespace]);
        }
    }

    /**
     * Clear an event.
     * If no event is specified all events will be cleared.
     *
     * @param string $event
     * @return $this
     */
    public function reset(string $event = null): self
    {
        if (!$event) {
            $this->_observers = [];
        } else {
            $this->_observers[$event] = [];
        }

        return $this;
    }
}
