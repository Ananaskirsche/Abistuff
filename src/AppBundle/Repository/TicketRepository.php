<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository
{
    public function getAmountSoldOfficialTickets()
    {
        $sum = $this->getEntityManager()->createQuery('SELECT SUM(t.anzahl) FROM AppBundle:Ticket t WHERE t.stammkarte = true')->getResult()[0][1];

        if($sum == null)
        {
            return 0;
        }
        else
        {
            return $sum;
        }
    }



    public function getAmountSoldAftershowTickets()
    {
        $sum = $this->getEntityManager()->createQuery('SELECT SUM(t.anzahl) FROM AppBundle:Ticket t WHERE t.stammkarte = false')->getResult()[0][1];

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