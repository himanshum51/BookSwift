<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, AssociationField, IntegerField, DateTimeField};

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('user')->setLabel('User'),
            AssociationField::new('event')->setLabel('Event'),
            AssociationField::new('ticketType')->setLabel('Ticket Type'),
            IntegerField::new('quantity'),
            DateTimeField::new('bookedAt')->onlyOnIndex(),
            DateTimeField::new('updatedAt')->onlyOnIndex(),
            DateTimeField::new('createdAt')->onlyOnIndex(),
            DateTimeField::new('canceledAt')->onlyOnIndex(),
        ];
    }
}
