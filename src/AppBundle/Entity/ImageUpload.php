<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageUpload
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "images")
 */
class ImageUpload extends AbstractEntity
{
    const PUBLIC_PATH = 'web';
    const UPLOAD_DIR = '/images/';

    /**
     * @var string
     * @ORM\Column(name = "title", type = "string",  nullable = true)
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name = "alt", type = "string",  nullable = true)
     */
    protected $alt;
    
    /**
     * @var string
     * @ORM\Column(name = "image", type = "string",  nullable = true)
     */
    protected $image;



    /**
     * @var Dish
     * @ORM\ManyToOne(targetEntity = "AppBundle\Entity\Dish", inversedBy = "photos")
     * @ORM\JoinColumn(name = "dish_id", referencedColumnName = "id")
     */
    protected $dish;



    /**
     * @var boolean
     * @ORM\Column(name = "main", type = "boolean")
     */
    protected $main;


    /** @var UploadedFile */
    protected $file;

    /** @var string */
    protected $path;

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return File|null
     */
    public function upload()
    {
        if (is_null($this->file)) return null;
        $this->caption=$this->file->getClientOriginalName();
        $this->image = sha1(uniqid(mt_rand(), true)).'.'.$this->file->getClientOriginalExtension();
        $basename = basename($this->image, $this->file->getClientOriginalExtension());
        $path = $this->image;
        $i = 1;
        
        while (file_exists($this->getUploadRootDir() . '/' . $path)) {
            $path = $basename . $i . '.' . $this->file->getClientOriginalExtension();
            $i++;
        }

        $this->path = self::UPLOAD_DIR . '/' . $path;

        return $this->file->move($this->getUploadRootDir(), $path);
    }

    protected function getUploadRootDir()
    {

        $folder='products/'.$this->getDish()->getId();

        return __DIR__ . '/../../../' . self::PUBLIC_PATH . self::UPLOAD_DIR . '/'.$folder;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * @return boolean
     */
    public function isMain()
    {
        return $this->main;
    }

    /**
     * @param boolean $main
     */
    public function setMain($main)
    {
        $this->main = $main;
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
