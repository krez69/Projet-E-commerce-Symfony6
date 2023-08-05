<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function findProductsPaginated(int $page, string $slug, int $limit = 6) : array
    {
        $limit  = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c', 'p')
            ->from('App\Entity\Products', 'p')
            ->join('p.categories', 'c')
            ->where("c.slug = '$slug'")
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);
        
        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        //On vérifie les données
        if(empty($data)){
            return $result;
        }

        //Calcule le nombre de pages
        //arrondi au supérieur avec ceil  
        $pages = ceil($paginator->count() / $limit);

        //On remplit le tableau
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;
 
        return $result;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Products $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Products $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Products[] Returns an array of Products objects
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
    public function findOneBySomeField($value): ?Products
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
