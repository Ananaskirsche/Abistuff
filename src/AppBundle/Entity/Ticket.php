<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="abistuff_tickets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketRepository")
 */
class Ticket
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank()
     */
    private $kaeufer;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     */
    private $anzahl;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $erhaltenAm;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $bezahltAm;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    private $barBezahlt;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     * @Assert\NotBlank()
     */
    private $stammkarte;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $createdBy;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getStammkarte()
    {
        return $this->stammkarte;
    }

    /**
     * @param mixed $stammkarte
     */
    public function setStammkarte($stammkarte)
    {
        $this->stammkarte = $stammkarte;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getKaeufer()
    {
        return $this->kaeufer;
    }

    /**
     * @param mixed $kaeufer
     */
    public function setKaeufer($kaeufer)
    {
        $this->kaeufer = $kaeufer;
    }

    /**
     * @return mixed
     */
    public function getAnzahl()
    {
        return $this->anzahl;
    }

    /**
     * @param mixed $anzahl
     */
    public function setAnzahl($anzahl)
    {
        $this->anzahl = $anzahl;
    }

    /**
     * @return mixed
     */
    public function getErhaltenAm()
    {
        return $this->erhaltenAm;
    }

    /**
     * @param mixed $erhaltenAm
     */
    public function setErhaltenAm($erhaltenAm)
    {
        $this->erhaltenAm = $erhaltenAm;
    }

    /**
     * @return mixed
     */
    public function getBezahltAm()
    {
        return $this->bezahltAm;
    }

    /**
     * @param mixed $bezahltAm
     */
    public function setBezahltAm($bezahltAm)
    {
        $this->bezahltAm = $bezahltAm;
    }

    /**
     * @return mixed
     */
    public function getBarBezahlt()
    {
        return $this->barBezahlt;
    }

    /**
     * @param mixed $barBezahlt
     */
    public function setBarBezahlt($barBezahlt)
    {
        $this->barBezahlt = $barBezahlt;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }
}