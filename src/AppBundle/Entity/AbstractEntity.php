<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntity
 * @package CustomerHunt\SystemBundle\Entity
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class AbstractEntity
{
    /**
     * @var int
     * @ORM\Column(name = "id", type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    protected $id;
    
    /**
     * @var \DateTime
     * @ORM\Column(name = "date_added", type = "datetime")
     */
    protected $createdAt;

   
    /**
     * @var \DateTime
     * @ORM\Column(name = "date_modified", type = "datetime")
     */
    protected $modifiedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->modifiedAt = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function preUpdate()
    {
        $this->modifiedAt = new \DateTime();
    }

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
