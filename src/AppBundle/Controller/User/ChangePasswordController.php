<?php

namespace AppBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePasswordController extends Controller
{
    /**
     * @Route("/user/changepwd", name="change_passwd")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('current_passwd', PasswordType::class, array(
                'label' => 'Aktuelles Passwort',
                'attr' => array('autofocus' => ''),
                'required' => true,
                'trim' => true
            ))
            ->add('new_passwd', PasswordType::class, array(
                'label' => 'Neues Passwort',
                'required' => true,
                'trim' => true
            ))
            ->add('new_passwd_again', PasswordType::class, array(
                'label' => 'Neues Passwort wiederholen',
                'required' => true,
                'trim' => true
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Passwort ändern',
                'attr' => array('class' => 'btn-success')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->get('security.token_storage')->getToken()->getUser();

            //Daten aus der Form holen
            $data = $form->getData();

            //Überprüfen ob neue passwörter gleich sind
            if($data['new_passwd'] != $data['new_passwd_again'])
            {
                $this->addFlash('danger', 'Die neuen Passwörter sind nicht gleich!');
                return $this->render('/abistuff/user/changepasswd.html.twig', array('form' => $form->createView()));
            }

            //Überprüfen ob aktuelles Passwort richtig ist.
            if(!$encoder->isPasswordValid($user, $data['current_passwd']))
            {
                $this->addFlash('danger', 'Dein aktuelles Passwort ist nicht richtig!');
                return $this->render('/abistuff/user/changepasswd.html.twig', array('form' => $form->createView()));
            }

            $user->setPassword($encoder->encodePassword($user, $data['new_passwd']));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Dein neues Passwort wurde gesetzt!');
        }

        return $this->render('/abistuff/user/changepasswd.html.twig', array('form' => $form->createView()));
    }
}