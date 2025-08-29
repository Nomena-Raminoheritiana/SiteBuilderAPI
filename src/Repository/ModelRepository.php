<?php

namespace App\Repository;

use App\Entity\Model;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Model>
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function findBySlugAndPropsNotNull(string $slug): Array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.slug = :slug')
            ->andWhere('m.props IS NOT NULL')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
    }

    public function findByUserAndParentOrId(User $user, string|int $parentId): Array {
        $qb = $this->createQueryBuilder('m');

        $qb->where('m.user = :user')
        ->andWhere($qb->expr()->orX(
            'm.parent = :id',
            'm.id = :id'
        ))
        ->setParameter('user', $user)
        ->setParameter('id', (int) $parentId);

        return $qb->getQuery()->getResult();

    }

    public function findChildrenOrOrphans(?Model $parent = null): array
    {
        $qb = $this->createQueryBuilder('m');

        if ($parent) {
            // Récupère les enfants du modèle
            $qb->where('m.parent = :parent')
               ->setParameter('parent', $parent);
        } else {
            // Récupère les modèles sans parent
            $qb->where('m.parent IS NULL');
        }

        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return Page[] Returns an array of Page objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Page
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
