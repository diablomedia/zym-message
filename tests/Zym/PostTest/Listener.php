<?php

namespace ZymTest\PostTest;

use Zym_Message;
use Zym_Message_Dispatcher;

class Listener
{
    public bool $notified = false;

    protected Zym_Message_Dispatcher $_dispatcher;

    public function __construct($event, $callback = null)
    {
        $this->_dispatcher = Zym_Message_Dispatcher::get();
        $this->_dispatcher->attach($this, $event, $callback);
    }

    public function notify(Zym_Message $message): void
    {
        $this->notified = true;
    }
}
