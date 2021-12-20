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
class Zym_Message_Registration
{
    protected string $_callback;

    protected object $_observer;

    /**
     * Constructor
     *
     */
    public function __construct(object $observer, string $callback)
    {
        $this->_observer = $observer;
        $this->_callback = $callback;
    }

    /**
     * Get the name of the callback method
     */
    public function getCallback(): string
    {
        return $this->_callback;
    }

    /**
     * Get the observer
     */
    public function getObserver(): object
    {
        return $this->_observer;
    }
}
