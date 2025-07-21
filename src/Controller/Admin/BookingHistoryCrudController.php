<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, AssociationField, TextField, DateTimeField};

class BookingHistoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BookingHistory::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('booking'),
            TextField::new('action'),
        ];
    }
}
