<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Dish
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "dish")
 */
class Dish extends AbstractEntity
{
    /**
     * @var string
     * @ORM\Column(name = "caption", type = "string")
     */
    protected $caption;

    /**
     * @var string
     * @ORM\Column(name = "description", type = "text", nullable=true)
     */
    protected $description;

    /**
     * @var boolean
     * @ORM\Column(name = "active", type = "boolean")
     */
    protected $active;

    /**
     * @var ArrayCollection|ParameterValue[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\ParameterValue", inversedBy = "dishes")
     * @ORM\JoinTable(name = "dish_parameter_values",
     *   joinColumns = {@ORM\JoinColumn(name = "dish_id", referencedColumnName = "id")},
     *   inverseJoinColumns = {@ORM\JoinColumn(name = "parameter_value_id", referencedColumnName = "id")}
     * )
     */
    protected $parametervalues;

    /**
     * @var ArrayCollection|DishMenu[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\DishMenu", mappedBy = "dish", cascade = {"persist", "remove"})
     */
    protected $dishmenus;

    /**
     * @var ArrayCollection|Reciept[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\Reciept", mappedBy = "dish", cascade = {"persist", "remove"})
     */
    protected $reciepts;

    /**
     * @var ArrayCollection|Review[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\Review", mappedBy = "dish", cascade = {"persist", "remove"})
     */
    protected $reviews;

    /**
     * @var ArrayCollection|ImageUpload[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\ImageUpload", mappedBy = "dish", cascade = {"persist", "remove"})
     */
    protected $photos;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\User", inversedBy = "dishes")
     * @ORM\JoinColumn(name = "author_id", referencedColumnName = "id")
     */
    protected $author;

    /**
     * @var ArrayCollection|ParameterValue[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\TimeEat", inversedBy = "dishes")
     * @ORM\JoinTable(name = "dish_timeeats",
     *   joinColumns = {@ORM\JoinColumn(name = "dish_id", referencedColumnName = "id")},
     *   inverseJoinColumns = {@ORM\JoinColumn(name = "timeeat_id", referencedColumnName = "id")}
     * )
     */
    protected $timeeats;

    /**
     * @var integer
     * @ORM\Column(name = "timecook", type = "integer", nullable=true)
     */
    protected $timecook;


  
    public function __construct()
    {
        parent::__construct();
        $this->parametervalues = new ArrayCollection();
        $this->dishmenus = new ArrayCollection();
        $this->reciepts = new ArrayCollection();
        $this->timeeats = new ArrayCollection();
        $this->photos = new ArrayCollection();

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

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return ImageUpload
     */
    public function getMainPhoto()
    {
        $criteria = Criteria::create()->orderBy(array("main" => Criteria::DESC));
        $photos=$this->photos->matching($criteria);
        if (count($photos)>0) {
             return $photos[0];
        }
        else {
            return null;
        }
    }

  

    /**
     * @return ImageUpload[]|ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param ImageUpload[]|ArrayCollection $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * @return Review[]|ArrayCollection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param Review[]|ArrayCollection $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * @return ParameterValue[]|ArrayCollection
     */
    public function getTimeeats()
    {
        return $this->timeeats;
    }

    /**
     * @param ParameterValue[]|ArrayCollection $timeeats
     */
    public function setTimeeats($timeeats)
    {
        $this->timeeats = $timeeats;
    }

    /**
     * @return int
     */
    public function getTimecook()
    {
        return $this->timecook;
    }

    /**
     * @param int $timecook
     */
    public function setTimecook($timecook)
    {
        $this->timecook = $timecook;
    }


    /**
 * @return array
 */
    public function getChars()
    {
        $price = 0;
        $weight=0;
        $cal=0;
        $protein=0;
        $fat=0;
        $carbo=0;
        $count=0;
        foreach($this->getReciepts() as $reciept){
            $ingridient=$reciept->getIngridient();
            if ($reciept->isTaste()) {
                $quantity=0;
            }
            else {
                $quantity =$reciept->getQuantity();
            }
            $price += ($ingridient->getPrice()/100)*$quantity;
            $weight+=$quantity;
            $cal+=($ingridient->getCal()/100)*$quantity;
            $protein+=($ingridient->getProtein()/100)*$quantity;
            $fat+=($ingridient->getFat()/100)*$quantity;
            $carbo+=($ingridient->getCarbo()/100)*$quantity;
            $count++;
        }

        return array('price'=>$price,
            'weight'=>$weight,
            'cal' =>$cal,
            'protein'=>$protein,
            'fat'=>$fat,
            'carbo'=>$carbo,
            'count'=>$count,
        );
    }

    

    

}
