<?php

/**
 * @package    DoctrineRefreshTokensRepository.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Slcorp\RoleModelBundle\Infrastructure\Repository;

use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenRepositoryInterface;
use Slcorp\RoleModelBundle\Domain\Entity\RefreshToken;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 * @implements RefreshTokenRepositoryInterface<RefreshToken>
 */
class DoctrineRefreshTokenRepository extends ServiceEntityRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    /**
     * @return iterable<RefreshToken>
     */
    public function findInvalid(?DateTimeInterface $datetime = null): iterable
    {
        return $this->createQueryBuilder('u')
            ->where('u.valid < :datetime')
            ->setParameter('datetime', $datetime ?? new DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param positive-int|null $batchSize
     * @param int<0, max>       $offset
     *
     * @return iterable<RefreshToken>
     */
    public function findInvalidBatch(?DateTimeInterface $datetime = null, ?int $batchSize = null, int $offset = 0): iterable
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.valid < :datetime')
            ->setParameter('datetime', $datetime ?? new DateTime());

        if ($batchSize !== null) {
            $qb->setFirstResult($offset)
                ->setMaxResults($batchSize);
        }

        return $qb->getQuery()->getResult();
    }
}
