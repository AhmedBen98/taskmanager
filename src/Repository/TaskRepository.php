<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Recherche les tâches par mot-clé dans le titre ou la description
     *
     * @param string $search
     * @return Task[]
     */
    public function findBySearch(string $search): array
    {
        $qb = $this->createQueryBuilder('t');

        if (!empty($search)) {
            $qb->andWhere('t.title LIKE :search OR t.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        return $qb->orderBy('t.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Filtrer les tâches terminées ou non terminées
     *
     * @param bool $done
     * @return Task[]
     */
    public function findByIsDone(bool $done): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isDone = :done')
            ->setParameter('done', $done)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche combinée par titre et statut (terminée ou non)
     *
     * @param string|null $title
     * @param bool|null $done
     * @return Task[]
     */
    public function findByTitleAndStatus(?string $title = '', ?bool $done = null): array
    {
        $qb = $this->createQueryBuilder('t');

        if (!empty($title)) {
            $qb->andWhere('t.title LIKE :title')
               ->setParameter('title', '%' . $title . '%');
        }

        if ($done !== null) {
            $qb->andWhere('t.isDone = :done')
               ->setParameter('done', $done);
        }

        return $qb->orderBy('t.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}
