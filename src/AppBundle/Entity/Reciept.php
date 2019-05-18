<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Reciept
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "reciept")
 */
class Reciept extends AbstractEntity
{
    
    /**
     * @var string
     * @ORM\Column(name = "quantity", type = "decimal", nullable=true)
     */
    protected $quantity;


    /**
     * @var Dish
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\Dish", inversedBy = "reciepts")
     * @ORM\JoinColumn(name = "dish_id", referencedColumnName = "id")
     */
    protected $dish;

    /**
     * @var Ingridient
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\Ingridient", inversedBy = "reciepts")
     * @ORM\JoinColumn(name = "ingridient_id", referencedColumnName = "id")
     */
    protected $ingridient;

    /**
     * @var boolean
     * @ORM\Column(name = "taste", type = "boolean")
     */
    protected $taste;


    public function __construct()
    {
        parent::__construct();
        $this->taste=false;

    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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

    /**
     * @return Ingridient
     */
    public function getIngridient()
    {
        return $this->ingridient;
    }

    /**
     * @param Ingridient $ingridient
     */
    public function setIngridient($ingridient)
    {
        $this->ingridient = $ingridient;
    }

    /**
     * @return boolean
     */
    public function isTaste()
    {
        return $this->taste;
    }

    /**
     * @param boolean $taste
     */
    public function setTaste($taste)
    {
        $this->taste = $taste;
    }


}
