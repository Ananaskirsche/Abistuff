<?php

namespace AppBundle\Controller\User\TicketSale;

use AppBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class TicketSale extends Controller
{
    /**
     * @Route("/user/ticketsale", name="ticketsale")
     */
    public function showAction(Request $request, UserInterface $user)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findAll();

        return $this->render("/abistuff/user/ticketsale/ticketsale.html.twig", array('tickets' => $tickets,
            'soldTickets' => $repository->getAmountSoldTickets()));
    }
}