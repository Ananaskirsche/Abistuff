<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BankTransaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(BankTransaction::class);
        $transactions = $repository->findBy(array(), array('date' => 'DESC'));

        return $this->render("/abistuff/homepage.html.twig", array('transactions' => $transactions, 'sum' => $repository->getTransactionValueSum()) );


        //TODO: Irgendwas sinnvolles hinzufügen!
        //return $this->redirectToRoute('login');


        // replace this example code with whatever you need
        /*
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
        */
    }
}
