<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Ingridient
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "ingridient")
 */
class Ingridient extends AbstractEntity
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
     * @var ArrayCollection|ParameterValue[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\ParameterValue", inversedBy = "ingridients")
     * @ORM\JoinTable(name = "ingridient_parameter_values",
     *   joinColumns = {@ORM\JoinColumn(name = "ingridient_id", referencedColumnName = "id")},
     *   inverseJoinColumns = {@ORM\JoinColumn(name = "parameter_value_id", referencedColumnName = "id")}
     * )
     */
    protected $parametervalues;

    /**
     * @var ArrayCollection|Reciept[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\Reciept", mappedBy = "ingridient", cascade = {"persist", "remove"})
     */
    protected $reciepts;

    /**
     * @var float
     * @ORM\Column(name = "cal", type = "decimal", scale=2, precision=10, nullable=true)
     */
    protected $cal;

    /**
     * @var float
     * @ORM\Column(name = "protein", type = "decimal", scale=2, precision=10, nullable=true)
     */
    protected $protein;

    /**
     * @var float
     * @ORM\Column(name = "fat", type = "decimal", scale=2, precision=10, nullable=true)
     */
    protected $fat;

    /**
     * @var float
     * @ORM\Column(name = "carbo", type = "decimal", scale=2, precision=10, nullable=true)
     */
    protected $carbo;

    /**
     * @var float
     * @ORM\Column(name = "price", type = "decimal", scale=2, precision=10, nullable=true)
     */
    protected $price;
    

    public function __construct()
    {
        parent::__construct();
        $this->parametervalues = new ArrayCollection();
        $this->active=true;
        $this->cal=0;
        $this->carbo=0;
        $this->protein=0;
        $this->fat=0;
        $this->price=0;

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
     * @return ParameterValue[]|ArrayCollection
     */
    public function getParametervalues()
    {
        return $this->parametervalues;
    }

    /**
     * @param ParameterValue[]|ArrayCollection $parametervalues
     */
    public function setParametervalues($parametervalues)
    {
        $this->parametervalues = $parametervalues;
    }

    /**
     * @return float
     */
    public function getCal()
    {
        return $this->cal;
    }

    /**
     * @param float $cal
     */
    public function setCal($cal)
    {
        $this->cal = $cal;
    }

    /**
     * @return float
     */
    public function getCarbo()
    {
        return $this->carbo;
    }

    /**
     * @param float $carbo
     */
    public function setCarbo($carbo)
    {
        $this->carbo = $carbo;
    }

    /**
     * @return float
     */
    public function getFat()
    {
        return $this->fat;
    }

    /**
     * @param float $fat
     */
    public function setFat($fat)
    {
        $this->fat = $fat;
    }

    /**
     * @return float
     */
    public function getProtein()
    {
        return $this->protein;
    }

    /**
     * @param float $protein
     */
    public function setProtein($protein)
    {
        $this->protein = $protein;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return Reciept[]|ArrayCollection
     */
    public function getReciepts()
    {
        return $this->reciepts;
    }

    /**
     * @param Reciept[]|ArrayCollection $reciepts
     */
    public function setReciepts($reciepts)
    {
        $this->reciepts = $reciepts;
    }


}
