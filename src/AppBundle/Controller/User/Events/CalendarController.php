<?php

namespace AppBundle\Controller\User\Events;

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends Controller
{
    /**
     * @Route("/user/calendar", name="events")
     */
    public function showAction(Request $request)
    {
        return $this->render("/abistuff/user/events/calendar.html.twig");
    }

    /**
     * @Route("/user/events", name="get_events")
     */
    public function getEvents(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Event::class);

        $startDate = new \DateTime($request->get('start'));
        $endDate = new \DateTime($request->get('end'));

        $events = $repository->getEventsFromTo($startDate, $endDate);

        $output = '[';
        $count = 0;

        foreach ($events as $event)
        {
            $output .= '"'. $count . '",{';
            $output .= '"id": "' . $event->getId() . '",';
            $output .= '"title":"' . $event->getTitle() . '",';
            $output .= '"start":"' . $event->getStartDate()->format('Y-m-d H:i:s') . '",';
            $output .= '"end":"' . $event->getEndDate()->format('Y-m-d H:i:s') . '",';
            $output .= '"allDay": true,';
            $output .= '"editable": false';
            $output .= '},';
        }

        $output = substr($output, 0, strlen($output)-1);
        $output .= ']';

        return $this->render('/abistuff/stringrenderer.html.twig', array('string' => $output));
    }
}