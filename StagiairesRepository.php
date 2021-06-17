<?php

namespace App\Repository;

use App\Entity\Stagiaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stagiaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stagiaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stagiaires[]    findAll()
 * @method Stagiaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StagiairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stagiaires::class);
    }

   

    public function getAll() {
        $entityManager = $this->getEntitymanager();
        $query = $entityManager->createQuery(
            "SELECT c
                FROM App\Entity\Stagiaires c
                ORDER BY c.nom"
        );

        return $query->getResult();
    }

}
