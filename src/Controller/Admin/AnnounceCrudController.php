<?php

namespace App\Controller\Admin;

use App\Entity\Announce;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AnnounceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Announce::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
