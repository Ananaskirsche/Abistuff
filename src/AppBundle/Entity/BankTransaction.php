<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="abistuff_transactions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionRepository")
 */
class BankTransaction
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=10)
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     */
    private $value;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min="4")
     */
    private $note;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="initiator", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $initiator;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $createdBy;

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setNote($note)
    {
        $this->note = $note;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getInitiator()
    {
        return $this->initiator;
    }

    public function setInitiator($initiator)
    {
        $this->initiator = $initiator;
    }

    public function getCreator()
    {
        return $this->createdBy;
    }

    public function setCreator($creator)
    {
        $this->createdBy = $creator;
    }
}