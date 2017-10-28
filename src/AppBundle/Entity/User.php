<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Table(name="abistuff_users")
 * @ORM\Entity()
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     */
    private $displayname;

    /**
     * @ORM\Column(type="json_array")
     * @Assert\NotBlank()
     */
    private $roles;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Assert\NotBlank()
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_visible", type="boolean")
     * @Assert\NotBlank()
     */
    private $isVisible;


    public function __construct()
    {
        $this->isVisible = true;
        $this->isActive = true;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDisplayname()
    {
        return $this->displayname;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setDisplayname($displayname)
    {
        $this->displayname = $displayname;
    }

    public function setActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
        $roles[] = 'ROLE_USER';

        return $this;
    }

    public function isVisible()
    {
        return $this->isVisible;
    }

    public function setVisible($isVisible)
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->isActive;
    }




    public function eraseCredentials(){}

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }


    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
}