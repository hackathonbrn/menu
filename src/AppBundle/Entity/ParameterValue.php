<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ParameterValue
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "parameter_value")
 */
class ParameterValue extends AbstractEntity
{
    
    /**
     * @var string
     * @ORM\Column(name = "value", type = "string")
     */
    protected $value;

    /**
     * @var Parameter
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\Parameter", inversedBy = "values")
     * @ORM\JoinColumn(name = "parameter_id", referencedColumnName = "id")
     */
    protected $parameter;

    /**
     * @var boolean
     * @ORM\Column(name = "active", type = "boolean")
     */
    protected $active;

    /**
     * @var ArrayCollection|Dish[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\Dish", mappedBy = "parametervalues")
     */
    protected $dishes;


    /**
     * @var ArrayCollection|Ingridient[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\Ingridient", mappedBy = "parametervalues")
     */
    protected $ingridients;



    public function __construct()
    {
        parent::__construct();
        $this->dishes = new ArrayCollection();
        $this->ingridients = new ArrayCollection();
        $this->active = true;

    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return Parameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @param Parameter $parameter
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
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
     * @return Ingridient[]|ArrayCollection
     */
    public function getIngridients()
    {
        return $this->ingridients;
    }

    /**
     * @param Ingridient[]|ArrayCollection $ingridients
     */
    public function setIngridients($ingridients)
    {
        $this->ingridients = $ingridients;
    }


}
