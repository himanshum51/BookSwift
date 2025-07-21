<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{
    IdField,
    TextField,
    TextareaField,
    BooleanField,
    DateTimeField,
    NumberField
};

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextareaField::new('description')->hideOnIndex(),
            TextField::new('location')->hideOnIndex(),
            TextField::new('banner')->hideOnIndex(), // You can customize this for image upload
            TextField::new('status'),
            BooleanField::new('isDeleted'),
            NumberField::new('totalBookings')->onlyOnIndex(),
        ];
    }
}
