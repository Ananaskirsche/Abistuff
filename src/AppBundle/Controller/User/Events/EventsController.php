<?php
/**
 * Created by PhpStorm.
 * User: fatih
 * Date: 19.10.17
 * Time: 16:34
 */

namespace AppBundle\Controller\User;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventsController extends Controller
{
    /**
     * @Route("/user/events", name="events")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Request $request)
    {
        return $this->render("/abistuff/user/events.html.twig", array("isAdmin" => false));
    }
}