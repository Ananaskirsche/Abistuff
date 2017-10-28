<?php

namespace AppBundle\Controller\User\Bank;

use AppBundle\Entity\BankTransaction;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class TransactionController extends Controller
{
    /**
     * @Route("/user/create_transaction", name="new_transaction")
     */
    public function showAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setMethod("POST")
            ->add('value', NumberType::class, array(
                'label' => 'Wert',
                'attr' => array('placeholder' => 'z.B. "10,95" oder "-5"', 'autofocus' => ''),
                'scale' => 2,
                'required' => true,
                'trim' => true
            ))
            ->add('note', TextareaType::class, array(
                'label' => 'Einnahmegrund / Verwendungszweck',
                'attr' => array('placeholder' => 'Ich musste ganz dringend ganz viele Kekse kaufen...'),
                'required' => true,
                'trim' => true
            ))
            ->add('date', DateType::class, array(
                'label' => 'Datum',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'attr' => array('id' => 'form_date'),
                'required' => true,
                'trim' => true,
                'data' => new \DateTime()
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
                'label' => 'Transaktion hinzufügen',
                'attr' => array('class' => 'btn-success')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Daten aus der Form holen
            $data = $form->getData();

            //Transaction-Objekt bauen
            $transaction = new BankTransaction();
            $transaction->setValue($data['value']);
            $transaction->setNote($data['note']);
            $transaction->setDate($data['date']);
            $transaction->setInitiator($data['initiator']);
            $transaction->setCreator($this->get('security.token_storage')->getToken()->getUser());

            //In die DB schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            $this->addFlash('success', 'Neue Transaktion über ' . $transaction->getValue() . '€ wurde notiert!');

            return $this->redirectToRoute('bankdetails');
        }

        return $this->render('/abistuff/user/bank/transaction.html.twig', array('form' => $form->createView()));
    }



    /**
     * @Route("/user/edit_transaction/{id}", name="edit_transaction")
     * @ParamConverter("transaction", class="AppBundle:BankTransaction")
     */
    public function editAction(Request $request, BankTransaction $transaction)
    {
        $form = $this->createFormBuilder()
            ->setMethod("POST")
            ->add('value', NumberType::class, array(
                'label' => 'Wert',
                'attr' => array('placeholder' => 'z.B. "10,95" oder "-5"', 'autofocus' => ''),
                'scale' => 2,
                'required' => true,
                'trim' => true,
                'data' => $transaction->getValue()
            ))
            ->add('note', TextareaType::class, array(
                'label' => 'Einnahmegrund / Verwendungszweck',
                'attr' => array('placeholder' => 'Ich musste ganz dringend ganz viele Kekse kaufen...'),
                'required' => true,
                'trim' => true,
                'data' => $transaction->getNote()
            ))
            ->add('date', DateType::class, array(
                'label' => 'Datum',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'attr' => array('id' => 'form_date'),
                'required' => true,
                'trim' => true,
                'data' => $transaction->getDate()
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
                'data' => $transaction->getInitiator(),
                'required' => true,
                'trim' => true
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Transaktion aktualisieren',
                'attr' => array('class' => 'btn-success')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Daten aus der Form holen
            $data = $form->getData();

            //Transaction-Objekt bauen
            $transaction->setValue($data['value']);
            $transaction->setNote($data['note']);
            $transaction->setDate($data['date']);
            $transaction->setInitiator($data['initiator']);

            //In die DB schreiben
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            $this->addFlash('success', 'Transaktion wurde aktualisiert!');

            return $this->redirectToRoute('bankdetails');
        }

        return $this->render('/abistuff/user/bank/transaction.html.twig', array('form' => $form->createView()));
    }


    /**
     * @Route("/admin/delete_transaction/{id}", name="delete_transaction")
     * @ParamConverter("transaction", class="AppBundle:BankTransaction")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(BankTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($transaction);
        $em->flush();

        $this->addFlash('success', 'Die Transaktion über ' . $transaction->getValue() . '€ wurde gelöscht!');

        return $this->redirectToRoute('bankdetails');
    }
}