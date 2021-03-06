<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Programme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programme[]    findAll()
 * @method Programme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Programme::class);
    }

    public function getJours($id){
 
        $qb = $this->createQueryBuilder('p')
            ->where('p.session_id = :id')
            ->setParameter(':id', $id)
            ->add('orderBy', 'p.nbJour ASC');
    
        $query = $qb->getQuery();
    
        return $query->execute();
    
        }

    public function getProgramme($id){
 
        $qb = $this->createQueryBuilder('p')
            ->where('p.session_id = :id')
            ->setParameter(':id', $id)
            ->add('orderBy', 'p.module_id ASC');
    
        $query = $qb->getQuery();
    
        return $query->execute();
    
        }
 
        public function verifProgrammeExist($id, $module){
 
            $qb = $this->createQueryBuilder('p')
                ->where('p.session_id = :id')
                ->andWhere('p.module_id = :module')
                ->setParameter(':id', $id)
                ->setParameter(':module', $module);
        
            $query = $qb->getQuery();
        
            return $query->execute();
        
            }

    // /**
    //  * @return Programme[] Returns an array of Programme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Programme
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
