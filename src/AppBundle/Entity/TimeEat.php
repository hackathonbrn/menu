<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class TimeEat
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "timeeat")
 */
class TimeEat extends AbstractEntity
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
     * @var ArrayCollection|Dish[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\Dish", mappedBy = "timeeats")
     */
    protected $dishes;

    /**
     * @var ArrayCollection|DishMenu[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\DishMenu", mappedBy = "timeeat", cascade = {"persist", "remove"})
     */
    protected $dishmenus;

    public function __construct()
    {
        parent::__construct();
        $this->dishes = new ArrayCollection();
        $this->dishmenus = new ArrayCollection();

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
     * @return Dish[]|ArrayCollection
     */
    public function getDishes()
    {
        return $this->dishes;
    }

    /**
     * @param Dish[]|ArrayCollection $dishes
     */
    public function setDishes($dishes)
    {
        $this->dishes = $dishes;
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
     * @return DishMenu[]|ArrayCollection
     */
    public function getDishmenus()
    {
        return $this->dishmenus;
    }

    /**
     * @param DishMenu[]|ArrayCollection $dishmenus
     */
    public function setDishmenus($dishmenus)
    {
        $this->dishmenus = $dishmenus;
    }


}
