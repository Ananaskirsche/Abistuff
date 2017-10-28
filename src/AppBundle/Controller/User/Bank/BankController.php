<?php

namespace AppBundle\Controller\User\Bank;


use AppBundle\Entity\BankTransaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class BankController extends Controller
{
    /**
     * @Route("/user/bankdetails", name="bankdetails")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Request $request, UserInterface $user)
    {
        $repository = $this->getDoctrine()->getRepository(BankTransaction::class);
        $transactions = $repository->findBy(array(), array('date' => 'DESC'));

        return $this->render("/abistuff/user/bank/bankdetails.html.twig", array('transactions' => $transactions, 'sum' => $repository->getTransactionValueSum()) );
    }
}