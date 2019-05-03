<?php

namespace AppBundle\Controller\User\TicketSale;

use AppBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class TicketViewController extends Controller
{
    /**
     * @Route("/user/ticketsale", name="ticketsale")
     */
    public function showAction()
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->getTickets();

        return $this->render("/abistuff/user/ticketsale/ticketsale.html.twig", array(
            'tickets' => $tickets,
            'soldOfficial' => $repository->getAmountSoldOfficialTickets(),
            'soldAftershow' => $repository->getAmountSoldAftershowTickets()));
    }

    /**
     * @Route("/user/ticketdata", name="ticketdata")
     */
    public function dataAction(Request $request)
    {

    }
}