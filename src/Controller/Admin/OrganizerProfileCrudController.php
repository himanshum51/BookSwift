<?php

namespace App\Controller\Admin;

use App\Entity\OrganizerProfile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, TextField, EmailField, UrlField, TextareaField};

class OrganizerProfileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrganizerProfile::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('companyName'),
            TextareaField::new('bio')->hideOnIndex(),
            UrlField::new('website')->hideOnIndex(),
            TextField::new('phoneNumber'),
            TextField::new('address')->hideOnIndex(),
        ];
    }
}
