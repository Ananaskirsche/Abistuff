<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BankTransaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        if(true === $authChecker->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('bankdetails');
        }

        $repository = $this->getDoctrine()->getRepository(BankTransaction::class);
        $transactions = $repository->findBy(array(), array('date' => 'DESC'));

        return $this->render("/abistuff/homepage.html.twig", array('transactions' => $transactions, 'sum' => $repository->getTransactionValueSum()) );
    }
}
