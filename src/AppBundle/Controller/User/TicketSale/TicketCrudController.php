<?php

namespace AppBundle\Controller\User\TicketSale;

use AppBundle\Entity\Ticket;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketCrudController extends Controller
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
                'label' => "Wurden die Karten bar bezahlt?",
                'choices' => ['Ja' => true, "Nein" => false],
                'multiple' => false,
                'data' => false,
                'required' => true
            ))
            ->add('stammkarte', ChoiceType::class, array(
                'label' => "Wurden 'Offizielle Karten' oder Laufkarten gekauft?",
                'choices' => ['Offizielle Karte' => true, 'Laufkarte' => false],
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
            $ticket->setStammkarte($data['stammkarte']);
            $ticket->setCreatedBy($this->get('security.token_storage')->getToken()->getUser());

            //In die DB schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Verkauf wurden gespeichert!');

            return $this->redirectToRoute('ticketsale');
        }

        return $this->render('/abistuff/user/ticketsale/sale.html.twig', array('form' => $form->createView()));
    }


    /**
     * @Route("/user/ticketsale/payedUpdate", name="updatePayedCards")
     * @Method({"GET","POST"})
     */
    public function updatePayedCards(Request $request)
    {
        try
        {
            //JSON Dekodieren und Ticket Repository holen
            $json = json_decode($request->getContent());
            $em = $this->getDoctrine()->getManager();
            $ticketRepo = $this->getDoctrine()->getRepository(Ticket::class);

            //Durch die gesendeten Ids iterieren
            foreach($json as $ticketId)
            {
                $ticket = $ticketRepo->find($ticketId);

                //Wenn die ID nicht gefunden wurde, dann überspringen
                if(!$ticket)
                {
                    continue;
                }

                if($ticket->getBezahltAm() === null)
                {
                    $ticket->setBezahltAm(new DateTime());
                    $em->flush();
                }

                $em->flush();
            }

        }
        catch (Exception $ex)
        {
            return new Response('An error occured', Response::HTTP_BAD_REQUEST, ['content-type' => 'text/plain']);
        }

        return new JsonResponse(array('status' => 'OK'));
    }


    /**
     * @Route("/user/ticketsale/handedUpdate", name="updateHandedCards")
     * @Method({"GET","POST"})
     */
    public function updateHandedCards(Request $request)
    {
        try
        {
            //JSON Dekodieren und Ticket Repository holen
            $json = json_decode($request->getContent());
            $em = $this->getDoctrine()->getManager();
            $ticketRepo = $this->getDoctrine()->getRepository(Ticket::class);

            //Durch die gesendeten Ids iterieren
            foreach($json as $ticketId)
            {
                $ticket = $ticketRepo->find($ticketId);

                //Wenn die ID nicht gefunden wurde, dann überspringen
                if(!$ticket)
                {
                    continue;
                }

                if($ticket->getErhaltenAm() === null)
                {
                    $ticket->setErhaltenAm(new DateTime());
                    $em->flush();
                }
            }

        }
        catch (Exception $ex)
        {
            return new Response('An error occured', Response::HTTP_BAD_REQUEST, ['content-type' => 'text/plain']);
        }

        return new JsonResponse(array('status' => 'OK'));
    }


    /**
     * @Route("/user/ticketsale/edit/{id}", name="edit_ticket")
     * @ParamConverter("ticket", class="AppBundle:Ticket")
     */
    public function editTicket(Request $request, Ticket $ticket)
    {
        $form = $this->createFormBuilder()
            ->setMethod("post")
            ->add('kaeufer', TextType::class, array(
                'label' => 'Name des Käufers (bitte mit Nachnamen)',
                'attr' => array('placeholder' => 'Manuel Neuer'),
                'required' => true,
                'trim' => true,
                'data' => $ticket->getKaeufer()
            ))
            ->add('anzahl', NumberType::class, array(
                'label' => "Anzahl der gekauften Karten",
                'attr' => array('placeholder' => "3"),
                'scale' => 0,
                'required' => true,
                'data' => $ticket->getAnzahl()
            ))
            ->add('erhaltenAm', DateType::class, array(
                'label' => "Karten ausgehändigt am",
                'widget' => "single_text",
                'format' => 'dd.MM.yyyy',
                'required' => false,
                'attr' => array('class' => 'js-datepicker','placeholder' => "Nur ausfüllen, wenn Karten übergeben wurden"),
                'data' => $ticket->getErhaltenAm()
            ))
            ->add('bezahltAm', DateType::class, array(
                'label' => "Karten wurden bezahlt am",
                'widget' => "single_text",
                'format' => 'dd.MM.yyyy',
                'required' => false,
                'attr' => array('class' => 'js-datepicker', 'placeholder' => "Nur ausfüllen, wenn Karten bezahlt wurden"),
                'data' => $ticket->getBezahltAm()
            ))
            ->add('barBezahlt', ChoiceType::class, array(
                'choices' => ['Ja' => true, "Nein" => false],
                'multiple' => false,
                'preferred_choices' => ['true'],
                'required' => true,
                'data' => $ticket->getBarBezahlt()
            ))
            ->add('stammkarte', ChoiceType::class, array(
                'label' => "Wurden 'Offizielle Karten' oder Laufkarten gekauft?",
                'choices' => ['Offizielle Karte' => true, 'Laufkarte' => false],
                'multiple' => false,
                'preferred_choices' => ['true'],
                'required' => true,
                'data' => $ticket->getStammkarte()
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

            $ticket->setKaeufer($data['kaeufer']);
            $ticket->setAnzahl($data['anzahl']);
            $ticket->setErhaltenAm($data['erhaltenAm']);
            $ticket->setBezahltAm($data['bezahltAm']);
            $ticket->setBarBezahlt($data['barBezahlt']);
            $ticket->setStammkarte($data['stammkarte']);

            //In die DB schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Verkauf wurden geändert!');

            return $this->redirectToRoute('ticketsale');
        }

        return $this->render('/abistuff/user/ticketsale/sale.html.twig', array('form' => $form->createView()));
    }
}