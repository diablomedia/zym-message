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
class Zym_Message
{
    /**
     * Optional objects
     */
    protected array $_data = [];

    /**
     * Message name
     */
    protected string $_name;

    /**
     * The object that sent the message
     */
    protected object $_sender;

    /**
     * Constructor
     *
     */
    public function __construct(string $name, object $sender, array $data = [])
    {
        $this->_name   = $name;
        $this->_sender = $sender;
        $this->_data   = $data;
    }

    /**
     * Get the optional information
     */
    public function getData(): array
    {
        return $this->_data;
    }

    /**
     * Get message name
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Get the object that sent the message
     */
    public function getSender(): object
    {
        return $this->_sender;
    }
}
