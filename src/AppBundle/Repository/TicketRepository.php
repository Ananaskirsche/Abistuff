<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository
{
    public function getTickets()
    {
        $qb = $this->getEntityManager()->createQuery('SELECT t FROM AppBundle:Ticket t ORDER BY t.kaeufer ASC');
        return $qb->getResult();
    }


    /**
     * Gets the amount of the sold official tickets
     * @return int
     */
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


    /**
     * Gets the amount of the sold aftershow tickets
     * @return int
     */
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