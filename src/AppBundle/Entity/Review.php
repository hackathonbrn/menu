<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Review
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "review")
 */
class Review extends AbstractEntity
{
    
    /**
     * @var string
     * @ORM\Column(name = "email", type = "string", nullable=true)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name = "author", type = "string", nullable=true)
     */
    protected $author;
    
    /**
     * @var string
     * @ORM\Column(name = "description", type = "string")
     */
    protected $description;

    /**
     * @var boolean
     * @ORM\Column(name = "active", type = "boolean")
     */
    protected $active;

    /**
     * @var boolean
     * @ORM\Column(name = "apply", type = "boolean")
     */
    protected $apply;

    /**
     * @var Dish
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\Dish", inversedBy = "reviews")
     * @ORM\JoinColumn(name = "dish_id", referencedColumnName = "id")
     */
    protected $dish;
    
    /**
     * @var integer
     * @ORM\Column(name = "ball", type = "integer", options={"default":5})
     */
    protected $ball;


    public function __construct()
    {
        parent::__construct();
        $this->active = true;
        $this->apply = false;
        $this->ball=5;

    }
    

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    

    /**
     * @return boolean
     */
    public function isApply()
    {
        return $this->apply;
    }

    /**
     * @param boolean $apply
     */
    public function setApply($apply)
    {
        $this->apply = $apply;
    }
    

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    

    /**
     * @return int
     */
    public function getBall()
    {
        return $this->ball;
    }

    /**
     * @param int $ball
     */
    public function setBall($ball)
    {
        $this->ball = $ball;
    }

    /**
     * @return Dish
     */
    public function getDish()
    {
        return $this->dish;
    }

    /**
     * @param Dish $dish
     */
    public function setDish($dish)
    {
        $this->dish = $dish;
    }


}
