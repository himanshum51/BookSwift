<?php

namespace App\Controller\Admin;

use App\Entity\TicketType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, TextField, MoneyField, IntegerField, DateTimeField};

class TicketTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TicketType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            MoneyField::new('price')->setCurrency('INR'),
            IntegerField::new('quantity'),
        ];
    }
}
