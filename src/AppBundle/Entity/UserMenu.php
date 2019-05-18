<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UserMenu
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "user_menu")
 */
class UserMenu extends AbstractEntity
{
    
    /**
     * @var string
     * @ORM\Column(name = "caption", type = "string")
     */
    protected $caption;


    /**
     * @var boolean
     * @ORM\Column(name = "active", type = "boolean")
     */
    protected $active;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\User", inversedBy = "usermenus")
     * @ORM\JoinColumn(name = "user_id", referencedColumnName = "id")
     */
    protected $user;
    

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}
