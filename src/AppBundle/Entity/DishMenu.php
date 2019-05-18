<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class DishMenu
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "dish_menu")
 */
class DishMenu extends AbstractEntity
{

    /**
     * @var UserMenu
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\UserMenu", inversedBy = "dishmenus")
     * @ORM\JoinColumn(name = "dish_menu_id", referencedColumnName = "id")
     */
    protected $usermenu;

    /**
     * @var Dish
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\Dish", inversedBy = "dishmenus")
     * @ORM\JoinColumn(name = "dish_id", referencedColumnName = "id")
     */
    protected $dish;

    /**
     * @var TimeEat
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\TimeEat", inversedBy = "dishmenus")
     * @ORM\JoinColumn(name = "timeeat_id", referencedColumnName = "id")
     */
    protected $timeeat;

    /**
     * @var integer
     * @ORM\Column(name = "dayeat", type = "integer")
     */
    protected $dayeat;


    public function __construct()
    {
        parent::__construct();

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
     * @return int
     */
    public function getDayeat()
    {
        return $this->dayeat;
    }

    /**
     * @param int $dayeat
     */
    public function setDayeat($dayeat)
    {
        $this->dayeat = $dayeat;
    }

    /**
     * @return TimeEat
     */
    public function getTimeeat()
    {
        return $this->timeeat;
    }

    /**
     * @param TimeEat $timeeat
     */
    public function setTimeeat($timeeat)
    {
        $this->timeeat = $timeeat;
    }

}
