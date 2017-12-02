<?php

namespace AppBundle\Controller\User\Events;

use AppBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class EventsController extends Controller
{
    /**
     * @Route("/user/calendar", name="calendar")
     */
    public function showAction(Request $request)
    {
        return $this->render("/abistuff/user/events/calendar.html.twig");
    }

    /**
     * @Route("/user/new_event", name="new_event")
     */
    public function createAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setMethod("POST")
            ->add('title', TextType::class, array(
                'label' => 'Titel',
                'attr' => array('placeholder' => 'Wichtiger Keks-Eintrag!', 'autofocus' => ''),
                'required' => true,
                'trim' => true
            ))
            ->add('startDate', DateType::class, array(
                'label' => 'Anfangszeit',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'attr' => array('id' => 'form_startDate'),
                'required' => true,
                'trim' => true,
                'data' => new \DateTime()
            ))
            ->add('endDate',DateType::class, array(
                'label' => 'Endzeit',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'attr' => array('id' => 'form_endDate'),
                'required' => true,
                'trim' => true,
                'data' => new \DateTime()
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Details'
            ))
            ->add('initiator', EntityType::class, array(
                'class' => 'AppBundle:User',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->where('u.isVisible = 1')
                        ->orderBy('u.displayname', 'ASC');
                },
                'choice_label' => 'displayname',
                'label' => 'Wer ist dafür verantwortlich?',
                'data' => $this->get('security.token_storage')->getToken()->getUser(),
                'required' => true,
                'trim' => true
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Kalendereintrag hinzufügen',
                'attr' => array('class' => 'btn-success')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $event = new Event();
            $event->setTitle($data['title']);
            $event->setInitiator($data['initiator']);
            $event->setCreatedBy($this->get('security.token_storage')->getToken()->getUser());
            $event->setDescription($data['description']);
            $event->setStartDate($data['startDate']);
            $event->setEndDate($data['endDate']);

            //In die DB schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Neues Ereignis wurde gespeichert!');

            return $this->redirectToRoute('calendar');
        }

        return $this->render('/abistuff/user/events/events.html.twig', array('form' => $form->createView()));
    }
}