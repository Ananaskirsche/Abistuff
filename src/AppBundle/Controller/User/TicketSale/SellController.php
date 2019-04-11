<?php

namespace AppBundle\Controller\User\TicketSale;

use AppBundle\Entity\Ticket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class SellController extends Controller
{
    /**
     * @Route("/user/ticketsale/new", name="new_ticketsale")
     */
    public function showAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setMethod("post")
            ->add('kaeufer', TextType::class, array(
                'label' => 'Name des Käufers (bitte mit Nachnamen)',
                'attr' => array('placeholder' => 'Manuel Neuer'),
                'required' => true,
                'trim' => true
            ))
            ->add('anzahl', NumberType::class, array(
                'label' => "Anzahl der gekauften Karten",
                'attr' => array('placeholder' => "3"),
                'scale' => 0,
                'required' => true
            ))
            ->add('erhaltenAm', DateType::class, array(
                'label' => "Karten ausgehändigt am",
                'widget' => "single_text",
                'format' => 'dd.MM.yyyy',
                'required' => false,
                'attr' => array('class' => 'js-datepicker','placeholder' => "Nur ausfüllen, wenn Karten übergeben wurden"),
            ))
            ->add('bezahltAm', DateType::class, array(
                'label' => "Karten wurden bezahlt am",
                'widget' => "single_text",
                'format' => 'dd.MM.yyyy',
                'required' => false,
                'attr' => array('class' => 'js-datepicker', 'placeholder' => "Nur ausfüllen, wenn Karten bezahlt wurden")
            ))
            ->add('barBezahlt', ChoiceType::class, array(
                'choices' => ['Ja' => true, "Nein" => false],
                'multiple' => false,
                'preferred_choices' => ['true'],
                'required' => true
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Speichern',
                'attr' => array('class' => 'btn-success')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Daten holen
            $data = $form->getData();

            //Neues Objekt erstellen
            $ticket = new Ticket();
            $ticket->setKaeufer($data['kaeufer']);
            $ticket->setAnzahl($data['anzahl']);
            $ticket->setErhaltenAm($data['erhaltenAm']);
            $ticket->setBezahltAm($data['bezahltAm']);
            $ticket->setBarBezahlt($data['barBezahlt']);
            $ticket->setCreatedBy($this->get('security.token_storage')->getToken()->getUser());

            //In die DB schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Verkauf wurden gespeichert!');

            return $this->redirectToRoute('ticketsale');
        }

        return $this->render('/abistuff/user/ticketsale/new_sale.html.twig', array('form' => $form->createView()));
    }
}