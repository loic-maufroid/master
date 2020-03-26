<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

     /**
     * @param $price
     * @return Product[]
     */
    public function findAllGreaterThanPrice($price): array
    {
        // Va automatiquement faire un select sur la table product
        // "p" est un alias comme en SQL
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.price > :price')
            ->setParameter('price', $price)
            ->orderBy('p.price', 'ASC')
            ->getQuery();

        return $queryBuilder->getResult();
    }

    /**
     * @param $number
     * @return Product[]
     */
    public function findMoreExpensive($number): array{
        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM App\Entity\Product p ORDER BY p.price DESC'
        )->setMaxResults($number);

        return $query->getResult();
    }

    /**
     * @param $num
     * @return Product[]
     */
    public function findMoreExpensiveAsc(int $num): array{
        $conn = $this->getEntityManager()->getConnection();

    $sql = "SELECT * FROM (SELECT * FROM product p ORDER BY p.price DESC LIMIT :num) q ORDER BY q.price ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue("num",$num,\PDO::PARAM_INT);
    $stmt->execute();

    // Retourne un tableau de tableaux
    return $stmt->fetchAll();
    }
}
