<?php

namespace App\Service\TicketType;

use App\Entity\TicketType;
use App\Entity\Event;

class TicketTypeService
{
    public function createFromArray(array $data, Event $event): TicketType
    {
        $ticketType = new TicketType();
        $ticketType->setEvent($event);
        return $this->updateFromArray($ticketType, $data);
    }

    public function updateFromArray(TicketType $ticketType, array $data): TicketType
    {
        if (isset($data['name'])) {
            $ticketType->setName($data['name']);
        }
        if (isset($data['description'])) {
            $ticketType->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $ticketType->setPrice((int)$data['price']);
        }
        if (isset($data['quantity'])) {
            $ticketType->setQuantity((int)$data['quantity']);
        }
        if (isset($data['available_from'])) {
            $ticketType->setAvailableFrom(new \DateTime($data['available_from']));
        }
        if (isset($data['available_to'])) {
            $ticketType->setAvailableTo(new \DateTime($data['available_to']));
        }

        return $ticketType;
    }
}
