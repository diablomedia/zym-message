<?php

namespace ZymTest;

use PHPUnit\Framework\TestCase;
use Zym_Message_Dispatcher;
use Zym_Message_Exception;
use ZymTest\PostTest\Listener;

class PostTest extends TestCase
{
    protected ?Zym_Message_Dispatcher $dispatcher = null;

    public function setup(): void
    {
        $this->dispatcher = Zym_Message_Dispatcher::get();
        $this->dispatcher->reset();
    }

    public function testIfCallbackDoesntExistExceptionIsThrown(): void
    {
        $this->expectException(Zym_Message_Exception::class);
        $this->expectExceptionMessage('Method "notfound" is not implemented in class "ZymTest\PostTest\Listener"');

        $listener = new Listener('Group::action', 'notfound');

        $dispatcher = Zym_Message_Dispatcher::get();
        $dispatcher->post('Group::action', $this, []);
    }

    public function testSpecificListenerIsNotified(): void
    {
        $listener = new Listener('Group::action');

        $dispatcher = Zym_Message_Dispatcher::get();
        $dispatcher->post('Group::action', $this, []);

        $this->assertTrue($listener->notified);
    }

    public function testWildcardListenerFromAnotherGroupIsNotNotified(): void
    {
        $listener = new Listener('AnotherGroup::*');

        $dispatcher = Zym_Message_Dispatcher::get();
        $dispatcher->post('Group::action', $this, []);

        $this->assertFalse($listener->notified);
    }

    public function testWildcardListenerIsNotified(): void
    {
        $listener = new Listener('Group::*');

        $dispatcher = Zym_Message_Dispatcher::get();
        $dispatcher->post('Group::action', $this, []);

        $this->assertTrue($listener->notified);
    }
}
