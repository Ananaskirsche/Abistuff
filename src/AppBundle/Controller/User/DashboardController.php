<?php

namespace AppBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/user/dashboard", name="dashboard")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction()
    {
        return $this->render("/abistuff/user/dashboard.html.twig", array("isAdmin" => false,));
    }
}