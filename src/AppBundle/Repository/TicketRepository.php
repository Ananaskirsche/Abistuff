<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository
{
    public function getTickets(bool $hideFinishedTickets, $searchKaeufer)
    {
        //QueryBuilder
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        //Standart wonach wir suchen
        $queryBuilder
            ->select('t.id, t.kaeufer, t.anzahl, t.barBezahlt, t.stammkarte, t.erhaltenAm, '.
                     't.bezahltAm, u.displayname AS seller')
            ->from('AppBundle:Ticket', 't');

        //Sollen abgeschlossene Ticketverkäufe angezeigt werden?
        if($hideFinishedTickets)
        {
            $queryBuilder->andWhere('t.erhaltenAm IS NULL OR t.bezahltAm IS NULL');
        }

        //Soll Gesucht werden?
        if($searchKaeufer != null)
        {
            $queryBuilder->andWhere('t.kaeufer LIKE :searchKaeufer');
            $queryBuilder->setParameter('searchKaeufer', '%' . $searchKaeufer . '%');
        }

        //Join um das ganze Benutzerobjekt nur durch den Benutzernamen ersetzen zu können
        $queryBuilder->join('t.createdBy', 'u');
        $queryBuilder->orderBy('t.kaeufer', 'ASC');

        return $queryBuilder->getQuery()->getResult();
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