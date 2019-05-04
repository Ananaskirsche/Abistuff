<?php

namespace AppBundle\Controller\User\TicketSale;

use AppBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class TicketViewController extends Controller
{
    /**
     * @Route("/user/ticketsale", name="ticketsale")
     */
    public function showAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->getTickets(true, null);

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
        $jsonData = json_decode($request->getContent());

        $hideFinishedTickets = true;
        $searchTerm = null;

        if(property_exists($jsonData, "hideFinishedTickets"))
        {
            $hideFinishedTickets = $jsonData->hideFinishedTickets;
        }

        if(property_exists($jsonData, "searchTerm"))
        {
            if(!empty($jsonData->searchTerm))
            {
                $searchTerm = $jsonData->searchTerm;
                $hideFinishedTickets = false;
            }
        }

        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->getTickets($hideFinishedTickets, $searchTerm);

        return new JsonResponse($tickets);
    }
}