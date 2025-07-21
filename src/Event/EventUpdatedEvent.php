<?php

namespace App\Event;

use App\Entity\Event;
use Symfony\Contracts\EventDispatcher\Event as Events;

class EventUpdatedEvent extends Event
{
    public function __construct(private readonly Event $event) {}

    public function getEvent(): Event
    {
        return $this->event;
    }
}
