<?php

namespace App\Service;

use App\Entity\Coffre;
use App\Entity\Historique;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CoffreManager {
    public function createHistorique( Coffre $coffre, User $changedBy, EntityManagerInterface $em): Historique {
        $historique = new Historique();
        $historique->setCoffre($coffre);
        $historique->setCode($coffre->getCode());
        $historique->setCreatedAt(new \DateTimeImmutable());
        $historique->setChangedBy($changedBy);

        $em->persist($historique);


        return $historique;
    }
}
