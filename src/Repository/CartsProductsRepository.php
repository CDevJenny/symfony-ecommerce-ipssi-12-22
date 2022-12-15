<?php

namespace App\Repository;

use App\Entity\CartsProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartsProducts>
 *
 * @method CartsProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartsProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartsProducts[]    findAll()
 * @method CartsProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartsProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartsProducts::class);
    }

    public function save(CartsProducts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartsProducts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return CartsProducts[] Returns an array of CartsProducts objects
     */
    public function findByProductId($id): ?CartsProducts
    {
        return $this->createQueryBuilder('c')
            ->Where('c.product = :product')
            ->setParameter('product', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    public function findOneBySomeField($value): ?CartsProducts
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
