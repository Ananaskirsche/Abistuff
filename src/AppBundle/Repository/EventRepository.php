<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    public function getEventsFromTo(\DateTime $startDate, \DateTime $endDate)
    {
        $endDate = $endDate->add(new \DateInterval('P1M'));

        $query = $this->createQueryBuilder('e')
            ->where('e.startDate >= :startDate')
            ->setParameter('startDate', $startDate)
            ->andWhere('e.endDate <= :endDate')
            ->setParameter('endDate', $endDate)
            ->getQuery();

        return $query->getResult();
    }
}