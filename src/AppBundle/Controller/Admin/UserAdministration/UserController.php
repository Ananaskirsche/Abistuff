<?php

namespace AppBundle\Controller\Admin\UserAdministration;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/admin/create_user", name="create_user")
     */
    public function showAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        //Die Form bauen
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('username', TextType::class, array(
                'label' => 'Benutzername',
                'attr' => array('placeholder' => 'max.mustermann', 'autofocus' => ''),
                'required' => true,
                'trim' => true))
            ->add('displayname', TextType::class, array(
                'label' => 'Name',
                'attr' => array('placeholder' => 'Max Mustermann'),
                'required' => true,
                'trim' => true))
            ->add('password', PasswordType::class, array(
                'label' => 'Passwort',
                'attr' => array('placeholder' => 'Passwort'),
                'required' => true,
                'trim' => true))
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Benutzer' => 'ROLE_USER',
                    'Administrator' => 'ROLE_ADMIN'
                ),
                'label' => 'Benutzerrolle',
                'required' => true
            ))
            ->add('isActive', ChoiceType::class, array(
                'choices' => array(
                    'Aktiviert' => true,
                    'Deaktiviert' => false
                ),
                'label' => 'Benutzerstatus',
                'required' => true
            ))
            ->add('isVisible', ChoiceType::class, array(
                'choices' => array(
                    'Sichtbar' => true,
                    'Unsichtbar' => false
                ),
                'label' => 'Sichtbarkeit',
                'required' => true,
                'data' => true
            ))
            ->add('submit', SubmitType::class, array('label' => 'Benutzer anlegen', 'attr' => array('class' => 'btn-success')))
            ->getForm();


        //Prüfen ob Form abgeschickt worden ist
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Daten aus der Form holen
            $data = $form->getData();

            //Benutzer erstellen
            $user = new User();
            $user->setUsername($data['username']);
            $user->setDisplayname($data['displayname']);
            $user->setPassword($encoder->encodePassword($user, $data['password']));
            $user->setActive($data['isActive']);
            $user->setRoles(array( $data['roles'] ));

            //In die Datenbank schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Der Benutzer ' . $user->getUsername() . ' wurde hinzugefügt!');
        }

        return $this->render("/abistuff/admin/user_administration/user_administration.html.twig", array("form" => $form->createView()));
    }



    /**
     * @Route("/admin/edit_user/{id}", name="edit_user")
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function editAction(Request $request, UserPasswordEncoderInterface $encoder, User $user)
    {
        //Die Form bauen
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('username', TextType::class, array(
                'label' => 'Benutzername',
                'attr' => array('placeholder' => 'max.mustermann', 'autofocus' => ''),
                'required' => true,
                'trim' => true,
                'data' => $user->getUsername()
            ))
            ->add('displayname', TextType::class, array(
                'label' => 'Name',
                'attr' => array('placeholder' => 'Max Mustermann'),
                'required' => true,
                'trim' => true,
                'data' => $user->getDisplayname()
            ))
            ->add('password', PasswordType::class, array(
                'label' => 'Passwort',
                'attr' => array('placeholder' => 'Passwort'),
                'required' => true,
                'trim' => true))
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Benutzer' => 'ROLE_USER',
                    'Administrator' => 'ROLE_ADMIN'
                ),
                'label' => 'Benutzerrolle',
                'required' => true,
                'data' => $user->getRoles()[0]
            ))
            ->add('isActive', ChoiceType::class, array(
                'choices' => array(
                    'Aktiviert' => true,
                    'Deaktiviert' => false
                ),
                'label' => 'Benutzerstatus',
                'required' => true,
                'data' => $user->isActive()
            ))
            ->add('isVisible', ChoiceType::class, array(
                'choices' => array(
                    'Sichtbar' => true,
                    'Unsichtbar' => false
                ),
                'label' => 'Sichtbarkeit',
                'required' => true,
                'data' => $user->isVisible()
            ))
            ->add('submit', SubmitType::class, array('label' => 'Benutzer aktualisieren', 'attr' => array('class' => 'btn-success')))
            ->getForm();


        //Prüfen ob Form abgeschickt worden ist
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Daten aus der Form holen
            $data = $form->getData();

            //Benutzer erstellen
            $user->setUsername($data['username']);
            $user->setDisplayname($data['displayname']);
            $user->setPassword($encoder->encodePassword($user, $data['password']));
            $user->setActive($data['isActive']);
            $user->setRoles(array( $data['roles'] ));
            $user->setVisible($data['isVisible']);

            //In die Datenbank schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Der Benutzer ' . $user->getUsername() . ' wurde aktualisiert!');
        }

        return $this->render("/abistuff/admin/user_administration/user_administration.html.twig", array("form" => $form->createView()));
    }



    /**
     * @Route("/admin/delete_user/{id}", name="delete_user")
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Der Benutzer ' . $user->getUsername() . ' wurde gelöscht!');

        return $this->redirectToRoute('list_users');
    }



    /**
     * @Route("/admin/list_users", name="list_users")
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('/abistuff/admin/user_administration/list_users.html.twig', array('users' => $users));
    }
}
