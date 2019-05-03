<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TransactionRepository extends EntityRepository
{
    public function getTransactionValueSum()
    {
        $sum = $this->getEntityManager()->createQuery('SELECT SUM(t.value) FROM AppBundle:BankTransaction t')->getResult()[0][1];

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