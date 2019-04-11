<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository
{
    public function getAmountSoldTickets()
    {
        $sum = $this->getEntityManager()->createQuery('SELECT SUM(t.anzahl) FROM AppBundle:Ticket t')->getResult()[0][1];

        if($sum == null)
        {
            return 0;
        }
        else
        {
            return $sum;
        }
    }
}