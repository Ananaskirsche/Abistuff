<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DebugUserController extends Controller
{
    /**
     * @Route("/createDebugUser")
     */
    public function showAction(UserPasswordEncoderInterface $encoder)
    {
         $em = $this->getDoctrine()->getManager();

         $debugUser = new User();
         $debugUser->setUsername("user");
         $debugUser->setDisplayname('User');
         $debugUser->setPassword($encoder->encodePassword($debugUser, "user123"));
         $debugUser->setActive(true);
         $debugUser->setRoles(array("ROLE_USER"));

         $em->persist($debugUser);
         $em->flush();

         return new Response("Benutzer " . $debugUser->getUsername() . " wurde erstellt!");
    }
}